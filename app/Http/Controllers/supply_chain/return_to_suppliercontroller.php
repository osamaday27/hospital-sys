<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\models\supply_chain\SupplierReturn;
use App\models\supply_chain\SupplierReturnItem;
use App\models\supply_chain\storemovement;
use App\Models\supply_chain\productstorebalance;
use App\models\supply_chain\productdata;
use App\models\supply_chain\storemaindata;
use App\Models\Suppliers\suppliersdata;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierInvoiceItem;
use App\Models\supply_chain\unit;

use App\Traits\UnitConversionHelpers;
use App\services\Supplier\SupplierInvoiceService;

class return_to_suppliercontroller extends Controller
{
    use UnitConversionHelpers;

    /* ============================ Helpers ============================ */

    protected function nextCode(): string
    {
        $year = date('Y');
        $last = SupplierReturn::withTrashed()->latest('id')->first();
        $seq  = ($last?->id ?? 0) + 1;
        return 'RS-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    protected function assertEnoughStockOrFail(int $product_id, int $store_id, ?string $batch_no, ?string $expiry_date, float $qty_base): productstorebalance
    {
        $q = productstorebalance::query()
            ->where('product_id', $product_id)
            ->where('store_id', $store_id);

        if ($batch_no !== null)   $q->where('batch_no', $batch_no);
        if ($expiry_date !== null)$q->whereDate('expiry_date', $expiry_date);

        /** @var productstorebalance|null $bal */
        $bal = $q->lockForUpdate()->first();

        if (!$bal) {
            abort(422, 'لا يوجد رصيد لهذه التشغيلة/الصلاحية في هذا المخزن.');
        }

        $available = (float) $bal->quantity_in_base_unit;
        if ($available < $qty_base) {
            abort(422, 'الكمية المطلوبة أكبر من الرصيد المتاح.');
        }

        return $bal;
    }

    /* ============================ Resource ============================ */

    public function index(Request $request)
    {
        $rows = SupplierReturn::with(['supplier','store'])
            ->latest('id')
            ->paginate(20);

        return view('supply_chain.program.return_items.return_to_supplier.index', compact('rows'));
    }

    public function create()
    {
        $code = $this->nextCode();
        $today = date('Y-m-d');

        return view('supply_chain.program.return_items.return_to_supplier.create', [
            'code'  => $code,
            'today' => $today,
        ]);
    }

    public function store(Request $request, SupplierInvoiceService $invoiceSvc)
    {
        $validated = $request->validate([
            // رأس
            'code'          => ['required','string','max:50','unique:supplier_returns,code'],
            'document_date' => ['required','date'],
            'store_id'      => ['required','integer','exists:store_main_date,id'],
            'supplier_id'   => ['required','integer','exists:suppliers_data,id'],
            'reason'        => ['nullable','string','max:500'],

            // بنود
            'items'                         => ['required','array','min:1'],
            'items.*.product_id'           => ['required','integer','exists:products_data,id'],
            'items.*.units_id'             => ['required','integer','exists:units,id'],
            'items.*.quantity'             => ['required','numeric','gt:0'],
            'items.*.unit_price'           => ['required','numeric','gte:0'],
            'items.*.tax_value'            => ['required','numeric','gte:0'],
            'items.*.unit_price_with_tax'  => ['required','numeric','gte:0'],
            'items.*.batch_no'             => ['required','string','max:100'],
            'items.*.expiry_date'          => ['nullable','date'],
            'items.*.supplier_invoice_id'  => ['required','integer','exists:supplier_invoices,id'],
            'items.*.notes'                => ['nullable','string','max:500'],
        ]);

        // ✅ منع تكرار (نفس المنتج + نفس الفاتورة) داخل نفس المستند — تحقق سيرفري
        $seen = [];
        foreach ($validated['items'] as $ix => $it) {
            $key = $it['product_id'].'|'.$it['supplier_invoice_id'];
            if (isset($seen[$key])) {
                abort(422, 'غير مسموح بتكرار نفس الصنف من نفس الفاتورة داخل نفس المرتجع.');
            }
            $seen[$key] = true;
        }

        $code          = $validated['code'];
        $document_date = $validated['document_date'];
        $store_id      = (int) $validated['store_id'];
        $supplier_id   = (int) $validated['supplier_id'];
        $reason        = $validated['reason'] ?? null;
        $itemsInput    = $validated['items'];

        $totals = ['subtotal' => 0.0, 'tax_total' => 0.0, 'grand_total' => 0.0];

        DB::transaction(function () use (
            $code, $document_date, $store_id, $supplier_id, $reason, $itemsInput, &$totals, $invoiceSvc
        ) {
            // 1) رأس المرتجع
            $head = SupplierReturn::create([
                'code'          => $code,
                'store_id'      => $store_id,
                'supplier_id'   => $supplier_id,
                'reason'        => $reason,
                'subtotal'      => 0,
                'tax_total'     => 0,
                'grand_total'   => 0,
                'document_date' => $document_date,
            ]);

            $invoiceItemsPayload = []; // لاستخدامها في إنشاء "فاتورة مرتجع"

            // 2) البنود
            foreach ($itemsInput as $i => $it) {
                $product_id  = (int) $it['product_id'];
                $units_id    = (int) $it['units_id'];
                $qty         = (float) $it['quantity'];                 // بوحدة السطر
                $unit_price  = (float) $it['unit_price'];               // قبل الضريبة
                $tax_value   = (float) $it['tax_value'];                // ضريبة/وحدة
                $price_w_tax = (float) $it['unit_price_with_tax'];      // شامل/وحدة
                $batch_no    = $it['batch_no'] ?? null;
                $expiry_date = $it['expiry_date'] ?? null;
                $ref_inv_id  = isset($it['supplier_invoice_id']) ? (int) $it['supplier_invoice_id'] : null;
                $notes       = $it['notes'] ?? null;

                // 2.a) تحويل للأصغر
                $qty_base = $this->ucToBase($product_id, $qty, $units_id);

                // 2.b) الرصيد كفاية؟
                $bal = $this->assertEnoughStockOrFail($product_id, $store_id, $batch_no, $expiry_date, $qty_base);

                // 2.c) أنقص الرصيد
                $bal->quantity_in_base_unit = (float) $bal->quantity_in_base_unit - $qty_base;
                if ($bal->units_id) {
                    try {
                        $bal->quantity = $this->ucFromBase($product_id, (float)$bal->quantity_in_base_unit, (int)$bal->units_id);
                    } catch (\Throwable $e) {}
                }
                $bal->save();

                // 2.d) سطر المرتجع
                SupplierReturnItem::create([
                    'supplier_return_id'    => $head->id,
                    'product_id'            => $product_id,
                    'units_id'              => $units_id,
                    'quantity'              => $qty,
                    'unit_price'            => $unit_price,
                    'tax_value'             => $tax_value,
                    'unit_price_with_tax'   => $price_w_tax,
                    'batch_no'              => $batch_no,
                    'supplier_invoice_id'   => $ref_inv_id,
                    'notes'                 => $notes,
                ]);

                // 2.e) حركة مخزن
                $refInvNo = $ref_inv_id ? SupplierInvoice::find($ref_inv_id)?->invoice_no : null;
                storemovement::create([
                    'product_id'     => $product_id,
                    'store_id'       => $store_id,
                    'units_id'       => $units_id,
                    'supplier_id'    => $supplier_id,
                    'quantity'       => $qty, // موجبة (خروج إلى المورد)
                    'batch_no'       => $batch_no,
                    'expiry_date'    => $expiry_date,
                    'movement_type'  => 'return',
                    'movement_number'=> $head->code,
                    'reference_no'   => $refInvNo,
                    'movement_date'  => $document_date,
                    'cost_price'     => null,
                    'sale_price'     => null,
                    'notes'          => $notes,
                    'user_id'        => auth()->id(),
                ]);

                // 2.f) مجاميع
                $totals['subtotal']    += $qty * $unit_price;
                $totals['tax_total']   += $qty * $tax_value;
                $totals['grand_total'] += $qty * $price_w_tax;

                // 2.g) بند الفاتورة (إشعار دائن)
                $invoiceItemsPayload[] = [
                    'item_type'       => 'product',
                    'product_id'      => $product_id,
                    'product_unit_id' => $units_id,
                    'service_id'      => null,
                    'description'     => $notes,
                    'qty'             => $qty,
                    'unit_price'      => $unit_price,
                    'discount'        => 0,
                    'tax'             => $qty * $tax_value,
                ];
            }

            // 3) تحديث مجاميع الرأس
            $head->update($totals);

            // 4) إنشاء فاتورة المرتجع على المورد (قيم موجبة، حالة new)
            $supplier = suppliersdata::find($supplier_id);
            $invoice  = $invoiceSvc->createInvoice([
                'supplier_id'     => $supplier_id,
                'store_id'        => $store_id,
                'branch_id'       => $supplier?->branch_id,
                'invoice_no'      => $head->code,
                'invoice_date'    => $document_date,
                'due_date'        => $document_date,
                'invoice_type'    => 'returned_products',
                'subtotal'        => 0,
                'discount'        => 0,
                'tax'             => 0,
                'total_amount'    => 0,
                'paid_amount'     => 0,
                'remaining_amount'=> 0,
                'status'          => 'new',
                'payment_method'  => 'credit',
                'reference_no'    => null,
                'notes'           => 'Supplier return linked to '.$head->code,
                'user_id'         => auth()->id(),
                'approved_by'     => null,
            ], $invoiceItemsPayload);

            // 5) تحديث حركة المخزون بأسعار من الفاتورة المرجعية أو تعريف الوحدات
            foreach ($head->items as $row) {
                $cost = null; $sale = null;

                if ($row->supplier_invoice_id) {
                    $invItem = SupplierInvoiceItem::where('invoice_id', $row->supplier_invoice_id)
                                ->where('product_id', $row->product_id)->first();
                    if ($invItem) {
                        $cost = (float) $invItem->unit_price;
                        $qtyInv = max(1.0, (float) $invItem->qty);
                        $taxUnit = ((float) $invItem->tax) / $qtyInv;
                        $sale = $cost + $taxUnit;
                    }
                }

                if (is_null($cost) || is_null($sale)) {
                    $units = $this->ucUnits($row->product_id);
                    $u     = $units->firstWhere('unit_id', (int)$row->units_id);
                    if ($u) {
                        $cost = $cost ?? ($u['cost_per_unit'] ?? null);
                        $sale = $sale ?? ($u['sale_per_unit'] ?? null);
                    }
                }

                storemovement::where('movement_number', $head->code)
                    ->where('product_id', $row->product_id)
                    ->where('units_id', $row->units_id)
                    ->latest('id')->limit(1)
                    ->update(['cost_price' => $cost, 'sale_price' => $sale]);
            }
        });

        return redirect()
            ->route('return_to_supplier.index')
            ->with('success', 'تم حفظ مرتجع المورد وتحديث الأرصدة والحركات بنجاح.');
    }

    /* ============================ AJAX Endpoints ============================ */

    public function ajaxSuppliers(Request $request)
    {
        $q = trim($request->get('q',''));
        $res = suppliersdata::query()
            ->when($q !== '', fn($qr)=>$qr->where(function($w) use ($q){
                $w->where('name_ar','like',"%$q%")
                  ->orWhere('name_en','like',"%$q%")
                  ->orWhere('code','like',"%$q%");
            }))
            ->orderBy('name_ar')->limit(50)->get(['id','code','name_ar','name_en']);

        return response()->json([
            'results' => $res->map(fn($s)=>[
                'id'   => $s->id,
                'text' => ($s->code ? $s->code.' - ' : '').($s->name_ar ?? $s->name_en),
            ])
        ]);
    }

    public function ajaxStores(Request $request)
    {
        $q = trim($request->get('q',''));
        $res = storemaindata::query()
            ->when($q !== '', fn($qr)=>$qr->where(function($w) use ($q){
                $w->where('name_ar','like',"%$q%")
                  ->orWhere('name_en','like',"%$q%")
                  ->orWhere('manual_code','like',"%$q%");
            }))
            ->orderBy('name_ar')->limit(50)->get(['id','manual_code','name_ar','name_en']);

        return response()->json([
            'results' => $res->map(fn($s)=>[
                'id'   => $s->id,
                'text' => ($s->manual_code ? $s->manual_code.' - ' : '').($s->name_ar ?? $s->name_en),
            ])
        ]);
    }

    public function ajaxInvoices(Request $request)
    {
        $supplier_id = (int) $request->get('supplier_id', 0);
        if (!$supplier_id) return response()->json(['results'=>[]]);

        $invoices = SupplierInvoice::where('supplier_id', $supplier_id)
            ->latest('invoice_date')
            ->limit(100)->get(['id','invoice_no','invoice_date','invoice_type','total_amount','status']);

        return response()->json([
            'results' => $invoices->map(fn($i)=>[
                'id'    => $i->id,
                'no'    => $i->invoice_no,
                'date'  => $i->invoice_date,
                'type'  => $i->invoice_type,
                'total' => $i->total_amount,
                'status'=> $i->status,
            ])
        ]);
    }

    // أصناف المورد التي لها رصيد بالمخزن
    public function ajaxProducts(Request $request)
    {
        $supplier_id = (int) $request->get('supplier_id', 0);
        $store_id    = (int) $request->get('store_id', 0);
        $q = trim($request->get('q',''));

        if (!$supplier_id || !$store_id) return response()->json(['results'=>[]]);

        $fromInvoices = SupplierInvoiceItem::whereHas('invoice', fn($iq)=>$iq->where('supplier_id',$supplier_id))
            ->whereNotNull('product_id')
            ->pluck('product_id')->unique()->values();

        $inStore = productstorebalance::where('store_id',$store_id)
            ->where('quantity_in_base_unit','>',0)
            ->pluck('product_id')->unique()->values();

        $ids = $fromInvoices->intersect($inStore);

        $products = productdata::whereIn('id',$ids)
            ->when($q !== '', fn($qr)=>$qr->where(function($w) use($q){
                $w->where('name_ar','like',"%$q%")->orWhere('name_en','like',"%$q%")
                  ->orWhere('product_code','like',"%$q%")->orWhere('system_code','like',"%$q%");
            }))
            ->orderBy('name_ar')->limit(50)->get(['id','name_ar','name_en','product_code']);

        return response()->json([
            'results' => $products->map(fn($p)=>[
                'id'   => $p->id,
                'text' => ($p->product_code ? $p->product_code.' / ' : '').($p->name_ar ?? $p->name_en),
            ])
        ]);
    }

    // وحدات المنتج + أسعار الوحدة + عامل التحويل
    public function ajaxUnits(Request $request)
    {
        $product_id = (int) $request->get('product_id',0);
        if (!$product_id) return response()->json(['results'=>[]]);

        $units = $this->ucUnits($product_id, app()->getLocale()==='ar' ? 'name_ar' : 'name_en');

        return response()->json([
            'results' => $units->map(fn($u)=>[
                'id'    => $u['unit_id'],
                'text'  => $u['unit_name'],
                'cost'  => $u['cost_per_unit'],
                'sale'  => $u['sale_per_unit'],
                'ftb'   => $u['factor_to_base'],
            ])
        ]);
    }

    // عناصر الفاتورة (للسعر المرجعي)
    public function ajaxInvoiceItems(Request $request)
    {
        $invoice_id = (int) $request->get('invoice_id', 0);
        if (!$invoice_id) return response()->json(['items' => []]);

        $items = SupplierInvoiceItem::query()
            ->where('invoice_id', $invoice_id)
            ->whereNotNull('product_id')
            ->get(['id','product_id','product_unit_id','qty','unit_price','tax']);

        $out = $items->map(function($it){
            return [
                'id'              => $it->id,
                'product_id'      => (int) $it->product_id,
                'product_unit_id' => (int) ($it->product_unit_id ?? 0),
                'qty'             => (float) ($it->qty ?? 0),
                'unit_price'      => (float) ($it->unit_price ?? 0), // قبل الضريبة
                'tax'             => (float) ($it->tax ?? 0),        // إجمالي ضريبة السطر
            ];
        })->values();

        return response()->json(['items' => $out]);
    }

    // الباتشات: من نفس فاتورة الشراء فقط + لديها رصيد فعلي
    public function ajaxBatches(Request $request)
    {
        $product_id = (int) $request->get('product_id',0);
        $store_id   = (int) $request->get('store_id',0);
        $invoice_id = (int) $request->get('supplier_invoice_id',0);
        if (!$product_id || !$store_id || !$invoice_id) return response()->json(['results'=>[]]);

        $inv = SupplierInvoice::find($invoice_id);
        if (!$inv) return response()->json(['results'=>[]]);
        $invNo = $inv->invoice_no;

        $batchRows = DB::table('receiving_inspecting as ri')
            ->join('receiving_inspecting_items as rii', 'rii.receiving_inspecting_id', '=', 'ri.id')
            ->where('ri.code', $invNo)
            ->where('ri.supplier_id', $inv->supplier_id)
            ->where('ri.store_id', $store_id)
            ->where('rii.product_id', $product_id)
            ->select('rii.batch_no', 'rii.expiry_date')
            ->get();

        if ($batchRows->isEmpty()) {
            return response()->json(['results'=>[]]);
        }

        $batchNos = $batchRows->pluck('batch_no')->filter()->unique()->values();

        $balances = DB::table('product_store_balances')
            ->where('product_id', $product_id)
            ->where('store_id', $store_id)
            ->whereIn('batch_no', $batchNos)
            ->where('quantity_in_base_unit', '>', 0)
            ->select('batch_no', 'expiry_date', 'quantity_in_base_unit')
            ->orderBy('expiry_date')
            ->get();

        $results = $balances->map(function($b){
            return [
                'batch_no'  => $b->batch_no,
                'expiry'    => $b->expiry_date ? date('Y-m-d', strtotime($b->expiry_date)) : null,
                'available' => (float) $b->quantity_in_base_unit,
            ];
        })->values();

        return response()->json(['results' => $results]);
    }

    // عرض JSON مبسط
    public function show($id)
    {
        $doc = SupplierReturn::with(['supplier','store','items.product','items.unit'])->findOrFail($id);
        return response()->json($doc);
    }

    /**
     * حذف: يعكس الرصيد، يحذف فاتورة المرتجع للمورد، يزيل الحركات، ثم يحذف السجل وتفاصيله.
     */
    public function destroy($id)
    {
        $doc = SupplierReturn::with(['items'])->findOrFail($id);

        DB::transaction(function () use ($doc) {
            $store_id = (int) $doc->store_id;

            // 1) عكس أثر الرصيد لكل بند
            foreach ($doc->items as $row) {
                $product_id  = (int) $row->product_id;
                $units_id    = (int) $row->units_id;
                $qty         = (float) $row->quantity;
                $qty_base    = $this->ucToBase($product_id, $qty, $units_id);

                $q = productstorebalance::query()
                    ->where('product_id', $product_id)
                    ->where('store_id', $store_id);

                if ($row->batch_no !== null)   $q->where('batch_no', $row->batch_no);
                if ($row->expiry_date !== null)$q->whereDate('expiry_date', $row->expiry_date);

                /** @var productstorebalance|null $bal */
                $bal = $q->lockForUpdate()->first();

                if ($bal) {
                    $bal->quantity_in_base_unit = (float)$bal->quantity_in_base_unit + $qty_base;
                    if ($bal->units_id) {
                        try {
                            $bal->quantity = $this->ucFromBase($product_id, (float)$bal->quantity_in_base_unit, (int)$bal->units_id);
                        } catch (\Throwable $e) {}
                    }
                    $bal->save();
                } else {
                    // لو مفيش سجل رصيد، ننشئ واحدًا (بوحدة الأساس للعرض)
                    $baseUnitId = $this->ucBaseUnitId($product_id);
                    $qty_disp   = $this->ucFromBase($product_id, $qty_base, $baseUnitId);
                    productstorebalance::create([
                        'product_id'             => $product_id,
                        'store_id'               => $store_id,
                        'batch_no'               => $row->batch_no,
                        'expiry_date'            => $row->expiry_date,
                        'quantity_in_base_unit'  => $qty_base,
                        'units_id'               => $baseUnitId,
                        'quantity'               => $qty_disp,
                    ]);
                }
            }

            // 2) إزالة الحركات
            storemovement::where('movement_number', $doc->code)->delete();

            // 3) حذف فاتورة المورد الخاصة بالمرتجع (والبنود)
            if ($doc->code) {
                $inv = SupplierInvoice::where('invoice_no', $doc->code)
                      ->whereIn('invoice_type', ['returned_products','returned_services','returned_mixed'])
                      ->first();
                if ($inv) {
                    SupplierInvoiceItem::where('invoice_id', $inv->id)->delete();
                    $inv->delete();
                }
            }

            // 4) حذف تفاصيل المرتجع ثم الرأس (Soft delete للرأس)
            SupplierReturnItem::where('supplier_return_id', $doc->id)->delete();
            $doc->delete();
        });

        return back()->with('success','تم حذف المرتجع وعكس الرصيد وحذف الفاتورة والحركات بنجاح.');
    }
}
