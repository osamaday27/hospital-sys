<?php

namespace App\Http\Controllers\purchases;

use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use App\Models\Purchases\PriceQuotation;
use App\Models\Supply_Chain\ProductData;      // ✔ موحّد
use App\Models\Supply_Chain\ProductUnit;      // ✔
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\Purchases\PurchaseOrder;
use App\Models\Purchases\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchases\PurchaseOrderCommittee;
use App\Models\hr\Employee;
use App\Models\hr\Department;
use App\Models\Purchases\PurchaseRequisition;
use Illuminate\Support\Facades\DB;            // ✔ لاستخدام store_products

// نفس خدمة التحويل المستخدمة في عروض الأسعار
use App\Services\SupplyChain\UnitConversionService;

class purchase_ordercontroller extends Controller
{
    use file_storage;

    /* ===================== Helpers (نفس منطق عروض الأسعار) ===================== */

    /** حوّل product_units.id إلى units.id لاستخدامه في أمر الشراء:
     * - وحدة البند: الصغيرة إن وُجدت، وإلا الكبيرة
     */
    protected function unitIdFromProductUnitForPO(ProductUnit $pu): ?int
    {
        if ($pu->unit_small_id) return (int)$pu->unit_small_id;
        if ($pu->unit_large_id) return (int)$pu->unit_large_id;
        return null;
    }

    /** حوّل product_units.id إلى units.id لحدود المخزن (أول سجل كبير، والباقي صغير إن وُجد) */
    protected function unitIdFromProductUnitForStoreLimits(ProductUnit $pu, int $firstPuIdForProduct): ?int
    {
        if ($pu->id === $firstPuIdForProduct) {
            // أول سجل: الكبيرة
            return $pu->unit_large_id ? (int)$pu->unit_large_id : ($pu->unit_small_id ? (int)$pu->unit_small_id : null);
        }
        // غير الأول: الصغيرة إن وُجدت، وإلا الكبيرة
        if ($pu->unit_small_id) return (int)$pu->unit_small_id;
        if ($pu->unit_large_id) return (int)$pu->unit_large_id;
        return null;
    }

    /** ابنِ خريطة حدود الأصناف للمخزن: product_id => [min_qty,min_unit_name,max_qty,max_unit_name] */
    protected function buildProductLimitsMap(int $storeId, array $productIds): array
    {
        if (empty($productIds)) return [];

        $rows = DB::table('store_products')
            ->where('store_id', $storeId)
            ->whereIn('product_id', $productIds)
            ->get([
                'product_id',
                'order_min_qty',
                'order_min_unit_id',
                'order_max_qty',
                'order_max_unit_id',
            ]);

        if ($rows->isEmpty()) return [];

        $allPu = ProductUnit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->whereIn('product_id', $productIds)
            ->orderBy('product_id')->orderBy('id')
            ->get(['id', 'product_id', 'unit_large_id', 'unit_small_id']);

        $firstPuByProduct = [];
        foreach ($allPu as $pu) {
            if (!isset($firstPuByProduct[$pu->product_id])) {
                $firstPuByProduct[$pu->product_id] = $pu->id;
            }
        }

        $puById = $allPu->keyBy('id');

        $out = [];
        foreach ($rows as $r) {
            $pid = (int)$r->product_id;

            $minName = $maxName = null;

            if ($r->order_min_unit_id && isset($puById[$r->order_min_unit_id])) {
                $pu = $puById[$r->order_min_unit_id];
                $unitId = $this->unitIdFromProductUnitForStoreLimits($pu, $firstPuByProduct[$pid] ?? $pu->id);
                $minName = $pu->smallUnit && $unitId === (int)$pu->unit_small_id
                    ? ($pu->smallUnit->name_ar . ' / ' . $pu->smallUnit->name_en)
                    : ($pu->largeUnit ? ($pu->largeUnit->name_ar . ' / ' . $pu->largeUnit->name_en) : null);
            }
            if ($r->order_max_unit_id && isset($puById[$r->order_max_unit_id])) {
                $pu = $puById[$r->order_max_unit_id];
                $unitId = $this->unitIdFromProductUnitForStoreLimits($pu, $firstPuByProduct[$pid] ?? $pu->id);
                $maxName = $pu->smallUnit && $unitId === (int)$pu->unit_small_id
                    ? ($pu->smallUnit->name_ar . ' / ' . $pu->smallUnit->name_en)
                    : ($pu->largeUnit ? ($pu->largeUnit->name_ar . ' / ' . $pu->largeUnit->name_en) : null);
            }

            $out[$pid] = [
                'min_qty'        => is_null($r->order_min_qty) ? null : (float)$r->order_min_qty,
                'min_unit_name'  => $minName,
                'max_qty'        => is_null($r->order_max_qty) ? null : (float)$r->order_max_qty,
                'max_unit_name'  => $maxName,
            ];
        }

        return $out;
    }

    /** تحقّق الحدود لأمر الشراء: يرجّع [violations[], orderTotals[], limitsMap[]] */
    protected function checkOrderLimits(array $data, PurchaseRequisition $req): array
    {
        $storeId = (int)$req->warehouse_id;

        // حدود قيمة الأمر على مستوى المخزن
        $warehouse = DB::table('store_main_date')->where('id', $storeId)->first(['min_order_quantity', 'max_order_quantity']);
        $minAmount = $warehouse ? ($warehouse->min_order_quantity ?? null) : null;
        $maxAmount = $warehouse ? ($warehouse->max_order_quantity ?? null) : null;

        // إجمالي قبل الضريبة
        $totalAmount = 0.0;
        $n = count($data['product_id']);
        for ($i = 0; $i < $n; $i++) {
            $totalAmount += ((float)$data['quantity'][$i]) * ((float)$data['price'][$i]);
        }

        $violations = [];

        // أسماء المنتجات لرسائل أوضح
        $productIds = array_map('intval', $data['product_id']);
        $prodRows = ProductData::whereIn('id', $productIds)->get(['id', 'system_code', 'name_ar', 'name_en'])->keyBy('id');

        // خريطة الحدود للعرض + المقارنة
        $limitsMap = $this->buildProductLimitsMap($storeId, $productIds);

        // product_units المستخدمة في البنود
        $usedPuIds = array_map('intval', $data['unit_id']);
        $puRows = ProductUnit::whereIn('id', $usedPuIds)->get(['id', 'product_id', 'unit_large_id', 'unit_small_id'])->keyBy('id');

        // أيضاً وحدات حدود المخزن للصنف
        $limitPuIds = [];
        foreach ($limitsMap as $pid => $L) {
            $row = DB::table('store_products')->where('store_id', $storeId)->where('product_id', $pid)->first([
                'order_min_unit_id',
                'order_max_unit_id'
            ]);
            if ($row) {
                if ($row->order_min_unit_id) $limitPuIds[] = (int)$row->order_min_unit_id;
                if ($row->order_max_unit_id) $limitPuIds[] = (int)$row->order_max_unit_id;
            }
        }
        if ($limitPuIds) {
            $morePu = ProductUnit::whereIn('id', array_values(array_unique($limitPuIds)))->get(['id', 'product_id', 'unit_large_id', 'unit_small_id'])->keyBy('id');
            $puRows = $puRows->union($morePu)->keyBy('id');
        }

        // أول سجل لكل منتج (قاعدة الكبيرة أولًا)
        $allPuForProducts = ProductUnit::whereIn('product_id', $productIds)->orderBy('product_id')->orderBy('id')->get(['id', 'product_id', 'unit_large_id', 'unit_small_id']);
        $firstPuByProduct = [];
        foreach ($allPuForProducts as $pu) {
            if (!isset($firstPuByProduct[$pu->product_id])) {
                $firstPuByProduct[$pu->product_id] = $pu->id;
            }
        }

        /** @var UnitConversionService $svc */
        $svc = app(UnitConversionService::class);

        // تحقق الكميات
        for ($i = 0; $i < $n; $i++) {
            $pid   = (int)$data['product_id'][$i];
            $puId  = (int)$data['unit_id'][$i];
            $qty   = (float)$data['quantity'][$i];

            if ($qty < 0) continue;

            $prod = $prodRows[$pid] ?? null;
            $label = $prod ? ($prod->system_code . ' - ' . trim(($prod->name_ar ?? '') . ' / ' . ($prod->name_en ?? ''))) : ('#' . $pid);

            $pu = $puRows[$puId] ?? null;
            if (!$pu) continue;

            $lineUnitId = $this->unitIdFromProductUnitForPO($pu);
            if (!$lineUnitId) continue;

            // الكمية بالأصغر
            try {
                $qtyBase = $svc->toBase($pid, $qty, $lineUnitId);
            } catch (\Throwable $e) {
                continue;
            }

            $sp = DB::table('store_products')->where('store_id', $storeId)->where('product_id', $pid)->first([
                'order_min_qty',
                'order_min_unit_id',
                'order_max_qty',
                'order_max_unit_id'
            ]);
            if (!$sp) continue;

            // الحد الأدنى
            if (!is_null($sp->order_min_qty) && $sp->order_min_qty >= 0 && $sp->order_min_unit_id) {
                $minPu = $puRows[(int)$sp->order_min_unit_id] ?? null;
                if ($minPu) {
                    $minUnitId = $this->unitIdFromProductUnitForStoreLimits($minPu, $firstPuByProduct[$pid] ?? $minPu->id);
                    if ($minUnitId) {
                        try {
                            $minBase = $svc->toBase($pid, (float)$sp->order_min_qty, $minUnitId);
                            if ($qtyBase < $minBase) {
                                $limText = isset($limitsMap[$pid]) && $limitsMap[$pid]['min_qty'] && $limitsMap[$pid]['min_unit_name']
                                    ? number_format($limitsMap[$pid]['min_qty'], 6) . ' ' . $limitsMap[$pid]['min_unit_name']
                                    : number_format((float)$sp->order_min_qty, 6);
                                $violations[] = "الكمية المطلوبة للصنف ({$label}) أقل من الحد الأدنى المسموح: {$limText}.";
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }
            // الحد الأقصى
            if (!is_null($sp->order_max_qty) && $sp->order_max_qty >= 0 && $sp->order_max_unit_id) {
                $maxPu = $puRows[(int)$sp->order_max_unit_id] ?? null;
                if ($maxPu) {
                    $maxUnitId = $this->unitIdFromProductUnitForStoreLimits($maxPu, $firstPuByProduct[$pid] ?? $maxPu->id);
                    if ($maxUnitId) {
                        try {
                            $maxBase = $svc->toBase($pid, (float)$sp->order_max_qty, $maxUnitId);
                            if ($qtyBase > $maxBase) {
                                $limText = isset($limitsMap[$pid]) && $limitsMap[$pid]['max_qty'] && $limitsMap[$pid]['max_unit_name']
                                    ? number_format($limitsMap[$pid]['max_qty'], 6) . ' ' . $limitsMap[$pid]['max_unit_name']
                                    : number_format((float)$sp->order_max_qty, 6);
                                $violations[] = "الكمية المطلوبة للصنف ({$label}) تتجاوز الحد الأقصى المسموح: {$limText}.";
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }
        }

        // تحقق قيمة الأمر على مستوى المخزن (قبل الضريبة)
        if (!is_null($minAmount) && (float)$minAmount > 0 && $totalAmount < (float)$minAmount) {
            $violations[] = 'إجمالي مبلغ الأمر أقل من الحد الأدنى المسموح للمخزن.';
        }
        if (!is_null($maxAmount) && (float)$maxAmount > 0 && $totalAmount > (float)$maxAmount) {
            $violations[] = 'إجمالي مبلغ الأمر يتجاوز الحد الأقصى المسموح للمخزن.';
        }

        $orderTotals = [
            'total_amount' => $totalAmount,
            'min_amount'   => is_null($minAmount) ? null : (float)$minAmount,
            'max_amount'   => is_null($maxAmount) ? null : (float)$maxAmount,
        ];

        return [$violations, $orderTotals, $limitsMap];
    }

    /* ===================== Actions ===================== */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseOrder::with('supplier')
                ->select([
                    'purchase_orders.id',
                    'purchase_orders.order_number',
                    'purchase_orders.order_date',
                    'purchase_orders.supplier_id',
                    'purchase_orders.subtotal',
                    'purchase_orders.total_tax',
                    'purchase_orders.total_amount_including_tax',
                    'purchase_orders.priority',
                    DB::raw("CASE WHEN EXISTS (
                    SELECT 1 FROM receiving_inspecting ri
                    WHERE ri.purchase_order_id = purchase_orders.id
                      AND ri.deleted_at IS NULL
                ) THEN 1 ELSE 0 END AS has_receiving")
                ])
                ->orderBy('order_date', 'desc');

            return datatables()
                ->of($query)
                ->addIndexColumn()
                ->addColumn(
                    'supplier_name',
                    fn($row) => (optional($row->supplier)->name_ar) . ' / ' . (optional($row->supplier)->name_en)
                )
                ->editColumn('order_date', fn($row) => $row->order_date->format('Y-m-d'))
                ->editColumn('subtotal', fn($row) => number_format($row->subtotal, 2))
                ->editColumn('total_tax', fn($row) => number_format($row->total_tax, 2))
                ->editColumn('total_amount_including_tax', fn($row) => number_format($row->total_amount_including_tax, 2))
                ->editColumn('priority', fn($row) => __('purchases.priority_' . $row->priority))
                ->make(true);
        }

        return view('purchases.programs.purchase_order.index');
    }
    protected function poHasReceiving(int $orderId): bool
    {
        return DB::table('receiving_inspecting')
            ->whereNull('deleted_at')
            ->where('purchase_order_id', $orderId)
            ->exists();
    }

    /** صفحة إنشاء أمر الشراء */

    public function create(Request $request)
    {
        $quotationId = $request->query('quotation_id');
        if (!$quotationId) {
            return redirect()
                ->route('price_quotations.index')
                ->with('error', __('purchases.quotation_not_specified'));
        }

        $quotation = PriceQuotation::with([
            'items.product',
            'items.unit.smallUnit',
            'items.unit.largeUnit',
            'requisition'
        ])->findOrFail($quotationId);
        // منع تكرار إنشاء أمر شراء لنفس عرض السعر
        if (\App\Models\Purchases\PurchaseOrder::where('quotation_id', $quotationId)->exists()) {

            return redirect()
                ->route('price_quotations.index', ['requisition_id' => optional($quotation->requisition)->id])
                ->with('error', 'تم بالفعل إنشاء أمر شراء لهذا العرض. لا يمكن إنشاء أمر جديد لنفس عرض السعر.');
        }
        // ✅ منع إنشاء أمر شراء إذا كان طلب الاحتياج المرتبط مقفول (تم إنشاء أمر شراء/مغلق)
        $req = $quotation->requisition;
        if (!$req) {
            return redirect()
                ->route('price_quotations.index')
                ->with('error', __('purchases.requisition_not_specified'));
        }
        if (in_array($req->status, ['closed'])) {
            return redirect()
                ->route('price_quotations.index', ['requisition_id' => $req->id])
                ->with('error', 'لا يمكن إنشاء أمر شراء لهذا الطلب لأن حالته تم إنشاء أمر شراء له أو تم إغلاقه.');
        }
        // ✅ نهاية الحماية

        // رقم الأمر
        $lastOrder   = PurchaseOrder::latest('id')->first();
        $nextId      = $lastOrder ? $lastOrder->id + 1 : 1;
        $orderNumber = 'PO-' . now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // الموردين
        $suppliers         = Supplier::where('status', 'active')->get();
        $defaultSupplierId = $quotation->supplier_id;

        // منتجات المخزن + منتجات بنود العرض
        $storeId = (int) optional($quotation->requisition)->warehouse_id;
        $storeProductIds = collect();

        if ($storeId) {
            $storeProductIds = DB::table('store_products')
                ->where('store_id', $storeId)
                ->where('status', 'active')
                ->pluck('product_id');
        }

        $rowProductIds = $quotation->items->pluck('item_id');
        $productIds    = $storeProductIds->merge($rowProductIds)->unique()->values();

        $products = ProductData::whereIn('id', $productIds)
            ->where('status', 'active')
            ->select('id', 'name_ar', 'name_en', 'vat', 'vat_value')
            ->get();

        // وحدات الأصناف
        $units = ProductUnit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('product_id')->orderBy('id')
            ->get();

        $unitsMap = [];
        foreach ($units as $u) {
            $nameAr = optional($u->smallUnit)->name_ar ?? optional($u->largeUnit)->name_ar ?? '-';
            $nameEn = optional($u->smallUnit)->name_en ?? optional($u->largeUnit)->name_en ?? '-';
            $unitsMap[$u->product_id][] = [
                'id'   => $u->id,
                'name' => "$nameAr / $nameEn",
            ];
        }

        // خيارات عامة
        $currencyOptions = ['EGP' => __('purchases.currency_egp')];
        $defaultCurrency = 'EGP';
        $priorityOptions = ['low', 'normal', 'high', 'urgent'];
        $defaultPriority = 'normal';

        // الموظفون والأقسام (للجنة)
        $employees = Employee::with('emp_dep')
            ->get()
            ->map(function ($e) {
                return [
                    'id'             => $e->id,
                    'full_name'      => $e->full_name,
                    'department'     => optional($e->emp_dep)->id,
                    'departmentName' => optional($e->emp_dep)->name_ar,
                    'job_position'   => $e->job_position,
                ];
            })
            ->values();

        $departments = Department::select('id', 'name_ar')->get();

        // ===== حدود المخزن (قيمة الأمر) + حدود الأصناف لواجهة الإنشاء =====
        $warehouse = DB::table('store_main_date')->where('id', $storeId)->first(['min_order_quantity', 'max_order_quantity']);
        $storeAmountLimits = [
            'min' => $warehouse ? ($warehouse->min_order_quantity ?? null) : null,
            'max' => $warehouse ? ($warehouse->max_order_quantity ?? null) : null,
        ];
        $limitsMap = $this->buildProductLimitsMap($storeId, $products->pluck('id')->all());

        return view('purchases.programs.purchase_order.create', compact(
            'quotation',
            'orderNumber',
            'suppliers',
            'defaultSupplierId',
            'products',
            'unitsMap',
            'currencyOptions',
            'defaultCurrency',
            'priorityOptions',
            'defaultPriority',
            'employees',
            'departments',
            'storeAmountLimits',
            'limitsMap'
        ));
    }

    /** حفظ أمر الشراء (مع التحقق من الحدود) */
    public function store(Request $request)
    {
        $rules = [
            'quotation_id'      => 'required|exists:price_quotations,id',
            'order_number'      => 'required|unique:purchase_orders,order_number',
            'order_date'        => 'required|date',
            'supplier_id'       => 'required|exists:suppliers_data,id',
            'currency'          => 'required|string|size:3',
            'delivery_period'   => 'nullable|integer|min:0',
            'delivery_location' => 'nullable|string|max:255',
            'payment_method'    => 'nullable|in:cash,bank_transfer,credit',
            'payment_terms'     => 'nullable|string|max:500',
            'notes'             => 'nullable|string|max:1000',
            'priority'          => 'nullable|in:low,normal,high,urgent',
            // بنود الطلب
            'product_id'        => 'required|array|min:1',
            'product_id.*'      => 'required|exists:products_data,id',
            'unit_id'           => 'required|array',
            'unit_id.*'         => 'required|exists:product_units,id',
            'quantity'          => 'required|array',
            'quantity.*'        => 'required|numeric|min:0.01',
            'price'             => 'required|array',
            'price.*'           => 'required|numeric|min:0',
            'tax_rate'          => 'required|array',
            'tax_rate.*'        => 'required|numeric|min:0',
            'item_notes'        => 'nullable|array',
            'item_notes.*'      => 'nullable|string|max:500',
        ];
        $data = $request->validate($rules);

        $quotation  = PriceQuotation::with('requisition')->findOrFail($data['quotation_id']);
        $requisition = $quotation->requisition ?: PurchaseRequisition::findOrFail($quotation->requisition_id);

        // ===== تحقق الحدود قبل الحفظ =====
        [$violations, $orderTotals] = $this->checkOrderLimits($data, $requisition);
        if (!empty($violations)) {
            return back()->withInput()->with([
                'showLimitModal'  => true,
                'limitViolations' => $violations,
                'orderTotals'     => $orderTotals,
            ]);
        }

        // ===== حساب الإجماليات والحفظ =====
        $subtotal = 0;
        $totalTax = 0;
        $lineItems = [];
        foreach ($data['product_id'] as $i => $prodId) {
            $qty     = (float)$data['quantity'][$i];
            $prc     = (float)$data['price'][$i];
            $taxRate = (float)$data['tax_rate'][$i];
            $lineSub = $qty * $prc;
            $lineTax = $lineSub * $taxRate / 100;
            $subtotal += $lineSub;
            $totalTax += $lineTax;
            $lineItems[] = [
                'product_id' => $prodId,
                'unit_id'    => $data['unit_id'][$i],
                'quantity'   => $qty,
                'price'      => $prc,
                'tax_amount' => $lineTax,
                'notes'      => $data['item_notes'][$i] ?? null,
            ];
        }
        $totalWithTax = $subtotal + $totalTax;

        $order = PurchaseOrder::create([
            'quotation_id'               => $quotation->id,
            'requisition_id'             => $quotation->requisition_id,
            'order_number'               => $data['order_number'],
            'order_date'                 => $data['order_date'],
            'supplier_id'                => $data['supplier_id'],
            'currency'                   => $data['currency'],
            'delivery_period'            => $data['delivery_period'] ?? null,
            'delivery_location'          => $data['delivery_location'] ?? null,
            'payment_method'             => $data['payment_method'] ?? null,
            'payment_terms'              => $data['payment_terms'] ?? null,
            'notes'                      => $data['notes'] ?? null,
            'priority'                   => $data['priority'] ?? 'normal',
            'subtotal'                   => $subtotal,
            'total_tax'                  => $totalTax,
            'total_amount_including_tax' => $totalWithTax,
            'created_by'                 => Auth::id(),
        ]);

        $requisition = PurchaseRequisition::find($order->requisition_id);
        if ($requisition && $requisition->status !== 'order_created') {
            $requisition->status = 'order_created';
            $requisition->save();
        }

        foreach ($lineItems as $line) {
            PurchaseOrderItem::create([
                'order_id'            => $order->id,
                'item_id'             => $line['product_id'],
                'unit_id'             => $line['unit_id'],
                'requested_qty'       => $line['quantity'],
                'pending_qty'         => $line['quantity'],
                'current_balance'     => 0,
                'other_stores_balance' => 0,
                'last_price'          => $line['price'],
                'tax_amount'          => $line['tax_amount'],
                'notes'               => $line['notes'],
            ]);
        }

        // لجنة الفحص — in: ديناميكي
        $empIdsForRule = $request->input('committee_employee_id', []);
        $chairInList   = empty($empIdsForRule) ? '0' : implode(',', range(1, count($empIdsForRule)));

        $commRules = [
            'committee_employee_id'     => 'required|array|min:1',
            'committee_employee_id.*'   => 'required|exists:employees,id',
            'committee_department_id.*' => 'nullable|exists:hr_department,id',
            'committee_job_position.*'  => 'nullable|string|max:255',
            'committee_chair'           => 'nullable|in:' . $chairInList,
        ];
        $commData = $request->validate($commRules);

        $chairIndex = (int) ($commData['committee_chair'] ?? 0);
        foreach ($commData['committee_employee_id'] as $i => $empId) {
            PurchaseOrderCommittee::create([
                'purchase_order_id' => $order->id,
                'employee_id'       => $empId,
                'department_id'     => $commData['committee_department_id'][$i] ?? null,
                'job_position'      => $commData['committee_job_position'][$i] ?? null,
                'is_chair'          => ($i + 1 === $chairIndex),
            ]);
        }

        return redirect()
            ->route('purchase_order.index')
            ->with('success', __('purchases.purchase_order_created'));
    }

    /** صفحة تعديل أمر الشراء */
    public function edit($id)
    {
        if ($this->poHasReceiving((int)$id)) {
            return redirect()
                ->route('purchase_order.index')
                ->with('error', 'لا يمكن تعديل أمر الشراء بعد إنشاء محضر استلام/فحص له.');
        }
        $order = PurchaseOrder::with([
            'items.product',
            'items.unit.smallUnit',
            'items.unit.largeUnit',
            'committees.employee',
            'committees.department',
            'requisition',                              // ✔ لاستخراج warehouse
        ])->findOrFail($id);

        $suppliers = Supplier::where('status', 'active')->get();

        // منتجات المخزن + منتجات الصفوف الحالية
        $storeId = (int) optional($order->requisition)->warehouse_id;
        $storeProductIds = collect();

        if ($storeId) {
            $storeProductIds = DB::table('store_products')
                ->where('store_id', $storeId)
                ->where('status', 'active')
                ->pluck('product_id');
        }

        $rowProductIds = $order->items->pluck('item_id');
        $productIds    = $storeProductIds->merge($rowProductIds)->unique()->values();

        $products = ProductData::whereIn('id', $productIds)
            ->where('status', 'active')
            ->select('id', 'name_ar', 'name_en', 'vat', 'vat_value')
            ->get();

        $units = ProductUnit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('product_id')->orderBy('id')
            ->get();

        $unitsMap = [];
        foreach ($units as $u) {
            $nameAr = optional($u->smallUnit)->name_ar ?? optional($u->largeUnit)->name_ar ?? '-';
            $nameEn = optional($u->smallUnit)->name_en ?? optional($u->largeUnit)->name_en ?? '-';
            $unitsMap[$u->product_id][] = ['id' => $u->id, 'name' => "$nameAr / $nameEn"];
        }

        $currencyOptions = ['EGP' => __('purchases.currency_egp')];
        $priorityOptions = ['low', 'normal', 'high', 'urgent'];

        $employees = Employee::with('emp_dep')
            ->get()
            ->map(function ($e) {
                return [
                    'id'             => $e->id,
                    'full_name'      => $e->full_name,
                    'department'     => optional($e->emp_dep)->id,
                    'departmentName' => optional($e->emp_dep)->name_ar,
                    'job_position'   => $e->job_position,
                ];
            })->values();

        $departments = Department::select('id', 'name_ar')->get();

        // ===== حدود المخزن (قيمة الأمر) + حدود الأصناف لواجهة التعديل (اختياري للعرض) =====
        $warehouse = DB::table('store_main_date')->where('id', $storeId)->first(['min_order_quantity', 'max_order_quantity']);
        $storeAmountLimits = [
            'min' => $warehouse ? ($warehouse->min_order_quantity ?? null) : null,
            'max' => $warehouse ? ($warehouse->max_order_quantity ?? null) : null,
        ];
        $limitsMap = $this->buildProductLimitsMap($storeId, $products->pluck('id')->all());

        return view('purchases.programs.purchase_order.edit', compact(
            'order',
            'suppliers',
            'products',
            'unitsMap',
            'currencyOptions',
            'priorityOptions',
            'employees',
            'departments',
            'storeAmountLimits',   // ✔ إن احتجت إظهاره في الواجهة
            'limitsMap'            // ✔ إن احتجت إظهاره في الواجهة
        ));
    }

    /** حفظ التعديلات على أمر الشراء (مع التحقق من الحدود) */
    public function update(Request $request, $id)
    {
        if ($this->poHasReceiving((int)$id)) {
            return back()->with('error', 'لا يمكن تعديل أمر الشراء بعد إنشاء محضر استلام/فحص له.');
        }
        $order = PurchaseOrder::with('requisition')->findOrFail($id);

        $rules = [
            'order_date'        => 'required|date',
            'supplier_id'       => 'required|exists:suppliers_data,id',
            'currency'          => 'required|string|size:3',
            'delivery_period'   => 'nullable|integer|min:0',
            'delivery_location' => 'nullable|string|max:255',
            'payment_method'    => 'nullable|in:cash,bank_transfer,credit',
            'payment_terms'     => 'nullable|string|max:500',
            'notes'             => 'nullable|string|max:1000',
            'priority'          => 'nullable|in:low,normal,high,urgent',

            'product_id'        => 'required|array|min:1',
            'product_id.*'      => 'required|exists:products_data,id',
            'unit_id'           => 'required|array',
            'unit_id.*'         => 'required|exists:product_units,id',
            'quantity'          => 'required|array',
            'quantity.*'        => 'required|numeric|min:0.01',
            'price'             => 'required|array',
            'price.*'           => 'required|numeric|min:0',
            'tax_rate'          => 'required|array',
            'tax_rate.*'        => 'required|numeric|min:0',
            'item_notes'        => 'nullable|array',
            'item_notes.*'      => 'nullable|string|max:500',

            'committee_employee_id'     => 'required|array|min:1',
            'committee_employee_id.*'   => 'required|exists:employees,id',
            'committee_department_id.*' => 'nullable|exists:hr_department,id',
            'committee_job_position.*'  => 'nullable|string|max:255',
        ];
        $data = $request->validate($rules);

        // ===== تحقق الحدود قبل الحفظ =====
        $req = $order->requisition()->first();
        if ($req) {
            [$violations, $orderTotals] = $this->checkOrderLimits($data, $req);
            if (!empty($violations)) {
                return back()->withInput()->with([
                    'showLimitModal'  => true,
                    'limitViolations' => $violations,
                    'orderTotals'     => $orderTotals,
                ]);
            }
        }

        // ===== حساب الإجماليات وتحديث الأمر =====
        $subtotal = 0;
        $totalTax = 0;
        $lineItems = [];
        for ($i = 0; $i < count($data['product_id']); $i++) {
            $qty     = (float) $data['quantity'][$i];
            $prc     = (float) $data['price'][$i];
            $vatRate = (float) $data['tax_rate'][$i];

            $lineSub = $qty * $prc;
            $lineTax = $lineSub * $vatRate / 100;

            $subtotal += $lineSub;
            $totalTax += $lineTax;

            $lineItems[] = [
                'product_id' => $data['product_id'][$i],
                'unit_id'    => $data['unit_id'][$i],
                'quantity'   => $qty,
                'price'      => $prc,
                'tax_amount' => $lineTax,
                'notes'      => $data['item_notes'][$i] ?? null,
            ];
        }
        $totalWithTax = $subtotal + $totalTax;

        $order->update([
            'order_date'                  => $data['order_date'],
            'supplier_id'                 => $data['supplier_id'],
            'currency'                    => $data['currency'],
            'delivery_period'             => $data['delivery_period']   ?? null,
            'delivery_location'           => $data['delivery_location'] ?? null,
            'payment_method'              => $data['payment_method']    ?? null,
            'payment_terms'               => $data['payment_terms']     ?? null,
            'notes'                       => $data['notes']             ?? null,
            'priority'                    => $data['priority']          ?? 'normal',
            'subtotal'                    => $subtotal,
            'total_tax'                   => $totalTax,
            'total_amount_including_tax'  => $totalWithTax,
        ]);

        $order->items()->delete();
        foreach ($lineItems as $line) {
            PurchaseOrderItem::create([
                'order_id'             => $order->id,
                'item_id'              => $line['product_id'],
                'unit_id'              => $line['unit_id'],
                'requested_qty'        => $line['quantity'],
                'pending_qty'          => $line['quantity'],
                'current_balance'      => 0,
                'other_stores_balance' => 0,
                'last_price'           => $line['price'],
                'tax_amount'           => $line['tax_amount'],
                'notes'                => $line['notes'],
            ]);
        }

        // لجنة الفحص – ضبط رئيس اللجنة ديناميكيًا
        PurchaseOrderCommittee::where('purchase_order_id', $order->id)->delete();

        $empIdsForRule = $request->input('committee_employee_id', []);
        $chairInList   = empty($empIdsForRule) ? '0' : implode(',', range(0, count($empIdsForRule) - 1));
        $request->validate([
            'committee_chair' => 'nullable|in:' . $chairInList,
        ]);

        foreach ($data['committee_employee_id'] as $i => $empId) {
            PurchaseOrderCommittee::create([
                'purchase_order_id' => $order->id,
                'employee_id'       => $empId,
                'department_id'     => $data['committee_department_id'][$i] ?? null,
                'job_position'      => $data['committee_job_position'][$i]  ?? null,
                'is_chair'          => ((string)$i === ($request->input('committee_chair') ?? '')) ? 1 : 0,
            ]);
        }

        return redirect()
            ->route('purchase_order.index')
            ->with('success', __('purchases.purchase_order_updated'));
    }

    /** تفاصيل أمر الشراء (AJAX) */
    public function show($id)
    {
        $order = PurchaseOrder::with([
            'supplier',
            'requisition',
            'items.product',
            'items.unit.smallUnit',
            'items.unit.largeUnit',
            'committees.employee',
            'committees.department',
        ])->findOrFail($id);

        $details = [
            __('purchases.order_number')       => $order->order_number,
            __('purchases.requisition_number') => optional($order->requisition)->requisition_number,
            __('purchases.order_date')         => $order->order_date->format('Y-m-d'),
            __('purchases.supplier')           => $order->supplier ? ($order->supplier->name_ar . ' / ' . $order->supplier->name_en) : '-',
            __('purchases.currency')           => $order->currency,
            __('purchases.delivery_period')    => $order->delivery_period ?: '-',
            __('purchases.priority')           => __('purchases.priority_' . $order->priority),
            __('purchases.payment_method')     => match ($order->payment_method) {
                'cash'          => __('purchases.payment_cash'),
                'bank_transfer' => __('purchases.payment_bank_transfer'),
                'credit'        => __('purchases.payment_credit'),
                default         => __('purchases.select_payment_method'),
            },
            __('purchases.payment_terms')      => $order->payment_terms ?: '-',
            __('purchases.notes')              => $order->notes ?: '-',
            __('purchases.subtotal')           => number_format($order->subtotal, 2),
            __('purchases.total_tax')          => number_format($order->total_tax, 2),
            __('purchases.net_amount')         => number_format($order->total_amount_including_tax, 2),
        ];

        $itemsTable = '<table class="table table-sm table-bordered"><thead><tr>'
            . '<th>' . __('purchases.item') . '</th>'
            . '<th>' . __('purchases.unit') . '</th>'
            . '<th>' . __('purchases.quantity') . '</th>'
            . '<th>' . __('purchases.price') . '</th>'
            . '<th>' . __('purchases.vat_amount') . '</th>'
            . '<th>' . __('purchases.line_total') . '</th>'
            . '<th>' . __('general.notes') . '</th>'
            . '</tr></thead><tbody>';

        foreach ($order->items as $it) {
            $prod     = $it->product;
            $name     = $prod ? ($prod->name_ar . ($prod->name_en ? ' / ' . $prod->name_en : '')) : '-';
            $unitRow  = $it->unit;
            $unitName = '-';
            if ($unitRow) {
                if ($unitRow->smallUnit) {
                    $unitName = $unitRow->smallUnit->name_ar . ' / ' . $unitRow->smallUnit->name_en;
                } elseif ($unitRow->largeUnit) {
                    $unitName = $unitRow->largeUnit->name_ar . ' / ' . $unitRow->largeUnit->name_en;
                }
            }
            $qty     = $it->requested_qty;
            $price   = $it->last_price;
            $vatAmt  = $it->tax_amount;
            $lineTot = ($qty * $price) + $vatAmt;

            $itemsTable .= '<tr>'
                . '<td>' . $name . '</td>'
                . '<td>' . $unitName . '</td>'
                . '<td>' . number_format($qty, 2) . '</td>'
                . '<td>' . number_format($price, 2) . '</td>'
                . '<td>' . number_format($vatAmt, 2) . '</td>'
                . '<td>' . number_format($lineTot, 2) . '</td>'
                . '<td>' . ($it->notes ?: '-') . '</td>'
                . '</tr>';
        }

        $itemsTable .= '</tbody></table>';
        $details[__('purchases.items')] = $itemsTable;

        $committeeTable = '<table class="table table-sm table-bordered"><thead><tr>'
            . '<th>' . __('purchases.employee_name') . '</th>'
            . '<th>' . __('purchases.department') . '</th>'
            . '<th>' . __('purchases.job_position') . '</th>'
            . '<th>' . __('purchases.committee_chair') . '</th>'
            . '</tr></thead><tbody>';

        foreach ($order->committees as $cm) {
            $empName   = $cm->employee ? $cm->employee->full_name : '-';
            $deptName  = $cm->department ? $cm->department->name_ar : '-';
            $chairText = $cm->is_chair ? __('general.yes') : __('general.no');

            $committeeTable .= '<tr>'
                . '<td>' . $empName . '</td>'
                . '<td>' . $deptName . '</td>'
                . '<td>' . ($cm->job_position ?: '-') . '</td>'
                . '<td>' . $chairText . '</td>'
                . '</tr>';
        }

        $committeeTable .= '</tbody></table>';
        $details[__('purchases.committee_inspection')] = $committeeTable;

        return response()->json(['details' => $details]);
    }

    /** طباعة أمر الشراء */
    public function print($id)
    {
        $order = PurchaseOrder::with([
            'supplier',
            'items.product',
            'items.unit.smallUnit',
            'items.unit.largeUnit',
            'committees.employee',
            'committees.department',
        ])->findOrFail($id);

        $order_summary = (object)[
            'order_number'      => $order->order_number,
            'order_date'        => $order->order_date->format('Y-m-d'),
            'supplier'          => $order->supplier,
            'delivery_location' => $order->delivery_location,
            'payment_method'    => $order->payment_method,
            'payment_terms'     => $order->payment_terms,
            'notes'             => $order->notes,
            'priority'          => $order->priority,
        ];

        $order_items      = $order->items;
        $order_committees = $order->committees;
        $title = __('purchases.print_purchase_order_title');

        return view('purchases.programs.purchase_order.print_purchase_order', compact(
            'order_summary',
            'order_items',
            'order_committees',
            'title'
        ));
    }

    /** حذف أمر الشراء (AJAX) */
    public function destroy($id)
    {
        if ($this->poHasReceiving((int)$id)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف أمر الشراء بعد إنشاء محضر استلام/فحص له.'
            ], 422);
        }
        $order = PurchaseOrder::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => __('purchases.order_not_found')
            ], 404);
        }

        $order->items()->delete();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => __('purchases.order_deleted')
        ]);
    }
}
