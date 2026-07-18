<?php

namespace App\Http\Controllers\supply_chain;

use Illuminate\Http\Request;
use App\Models\supply_chain\unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Traits\UnitConversionHelpers;
use App\models\supply_chain\productdata;
use App\models\supply_chain\storemaindata;
use App\models\supply_chain\storemovement;
use App\models\supply_chain\storetransfer;
use App\models\supply_chain\storedirectreturn;

use Illuminate\Validation\ValidationException;
use App\Models\supply_chain\productstorebalance;

class return_direct_to_storecontroller extends Controller
{
    use UnitConversionHelpers;

    /* ============================ Helpers ============================ */

    protected function nextCode(): string
    {
        $year = date('Y');
        $last = storedirectreturn::withTrashed()->latest('id')->first();
        $seq  = ($last?->id ?? 0) + 1;
        return 'RDS-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    protected function assertAvailableOrFail(
        int $product_id,
        int $store_from_id,
        ?string $batch_no,
        ?string $expiry_date,
        float $qty_base
    ): productstorebalance {
        $q = productstorebalance::query()
            ->where('product_id', $product_id)
            ->where('store_id', $store_from_id);

        if ($batch_no !== null)   $q->where('batch_no', $batch_no);
        if ($expiry_date !== null)$q->whereDate('expiry_date', $expiry_date);

        /** @var productstorebalance|null $bal */
        $bal = $q->lockForUpdate()->first();

        if (!$bal) {
            abort(422, 'لا يوجد رصيد لهذه التشغيلة/الصلاحية في مخزن المصدر.');
        }

        if ((float)$bal->quantity_in_base_unit < $qty_base) {
            abort(422, 'الكمية المطلوبة أكبر من المتاح في مخزن المصدر.');
        }

        return $bal;
    }

    protected function incBalance(
        int $product_id,
        int $store_id,
        ?string $batch_no,
        ?string $expiry_date,
        float $qty_base
    ): void {
        $baseUnitId = $this->ucBaseUnitId($product_id);

        /** @var productstorebalance|null $bal */
        $bal = productstorebalance::lockForUpdate()
            ->where('product_id',$product_id)
            ->where('store_id',$store_id)
            ->when($batch_no !== null, fn($w)=>$w->where('batch_no',$batch_no))
            ->when($expiry_date !== null, fn($w)=>$w->whereDate('expiry_date',$expiry_date))
            ->first();

        if ($bal) {
            $bal->quantity_in_base_unit = (float)$bal->quantity_in_base_unit + $qty_base;
            if ($bal->units_id) {
                try {
                    $bal->quantity = $this->ucFromBase($product_id, (float)$bal->quantity_in_base_unit, (int)$bal->units_id);
                } catch (\Throwable $e) { /* ignore */ }
            }
            $bal->save();
        } else {
            // إنشاء سجل جديد
            $qty_disp = $baseUnitId ? $this->ucFromBase($product_id, $qty_base, $baseUnitId) : $qty_base;
            productstorebalance::create([
                'product_id'            => $product_id,
                'store_id'              => $store_id,
                'batch_no'              => $batch_no,
                'expiry_date'           => $expiry_date,
                'quantity_in_base_unit' => $qty_base,
                'units_id'              => $baseUnitId,
                'quantity'              => $qty_disp,
            ]);
        }
    }

    protected function decBalance(
        int $product_id,
        int $store_id,
        ?string $batch_no,
        ?string $expiry_date,
        float $qty_base
    ): void {
        $bal = $this->assertAvailableOrFail($product_id, $store_id, $batch_no, $expiry_date, $qty_base);
        $bal->quantity_in_base_unit = (float)$bal->quantity_in_base_unit - $qty_base;
        if ($bal->units_id) {
            try {
                $bal->quantity = $this->ucFromBase($product_id, (float)$bal->quantity_in_base_unit, (int)$bal->units_id);
            } catch (\Throwable $e) {}
        }
        $bal->save();
    }

    /* ============================ Resource ============================ */

    public function index(Request $request)
    {
        // دعم استرجاع أول id بالكود لواجهة العرض/الحذف
        $codeParam = trim($request->get('code',''));
        if ($codeParam !== '') {
            $row = storedirectreturn::where('code',$codeParam)->orderBy('id')->first();
            return response()->json(['first_id' => $row?->id]);
        }

        // AJAX لـ DataTables
        if ($request->ajax()) {
            $draw   = (int) $request->get('draw', 1);
            $start  = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 10);
            $search = trim($request->input('search.value', ''));

            // subquery مجمّع على مستوى المستند (code)
            $base = storedirectreturn::query()
                ->select([
                    'code',
                    DB::raw('MIN(transfer_date)  AS document_date'),
                    DB::raw('MIN(store_from_id)  AS store_from_id'),
                    DB::raw('MIN(store_to_id)    AS store_to_id'),
                    DB::raw('COUNT(*)            AS items_count'),
                ])
                ->groupBy('code');

            // إجمالي قبل الفلترة
            $total = DB::query()->fromSub($base, 't')->count();

            // تطبيق البحث على الكود (يمكن التوسعة لاحقًا)
            $wrapped = DB::query()->fromSub($base, 't')
                ->when($search !== '', function ($q) use ($search) {
                    $q->where('t.code', 'like', "%{$search}%");
                });

            $filtered = (clone $wrapped)->count();

            // ترتيب افتراضي حسب التاريخ تنازلي
            $rows = $wrapped
                ->orderBy('t.document_date', 'desc')
                ->skip($start)->take($length)
                ->get();

            // جلب أسماء المخازن بكفاءة
            $storeIds = collect($rows)->pluck('store_from_id')->merge(collect($rows)->pluck('store_to_id'))->unique()->filter()->values();
            $storesMap = storemaindata::whereIn('id', $storeIds)->get(['id','name_ar','name_en'])
                ->keyBy('id');

            // تجهيز بيانات الجدول
            $data = [];
            foreach ($rows as $idx => $r) {
                $from = $storesMap->get($r->store_from_id);
                $to   = $storesMap->get($r->store_to_id);

                $viewBtn = '<button class="btn btn-sm btn-info view-btn" data-code="'.e($r->code).'" title="عرض"><i class="ri-eye-line"></i></button>';
                $delBtn  = '<button class="btn btn-sm btn-danger delete-btn" data-code="'.e($r->code).'" title="حذف"><i class="ri-delete-bin-6-line"></i></button>';

                $data[] = [
                    'DT_RowIndex'   => $start + $idx + 1,
                    'code'          => e($r->code),
                    'store_from'    => e($from?->name_ar ?? $from?->name_en ?? '-'),
                    'store_to'      => e($to?->name_ar ?? $to?->name_en ?? '-'),
                    'document_date' => date('Y-m-d', strtotime($r->document_date)),
                    'items_count'   => (int) $r->items_count,
                    'action'        => '<div class="hstack gap-2 justify-content-center">'.$viewBtn.$delBtn.'</div>',
                ];
            }

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $total,
                'recordsFiltered' => $filtered,
                'data'            => $data,
            ]);
        }

        // أول تحميل للـ Blade
        return view('supply_chain.program.return_items.return_direct_to_store.index');
    }

    public function create()
    {
        return view('supply_chain.program.return_items.return_direct_to_store.create', [
            'code'  => $this->nextCode(),
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code'            => ['required','string','max:50','unique:store_direct_returns,code'],
                'transfer_date'   => ['required','date'],
                'store_from_id'   => ['required','integer','exists:store_main_date,id','different:store_to_id'],
                'store_to_id'     => ['required','integer','exists:store_main_date,id','different:store_from_id'],

                'items'                    => ['required','array','min:1'],
                'items.*.product_id'      => ['required','integer','exists:products_data,id'],
                'items.*.units_id'        => ['required','integer','exists:units,id'],
                'items.*.quantity'        => ['required','numeric','gt:0'],
                'items.*.batch_no'        => ['required','string','max:100'],
                'items.*.transfer_number' => ['nullable','string','max:50'],
                'items.*.notes'           => ['nullable','string','max:500'],
            ]);

            // منع التكرار داخل نفس المستند
            $seen = [];
            foreach ($validated['items'] as $it) {
                $k = $it['product_id'].'|'.$it['batch_no'].'|'.($it['transfer_number'] ?? '');
                if (isset($seen[$k])) {
                    throw ValidationException::withMessages([
                        'items' => ['ممنوع تكرار نفس الصنف والباتش وكود التحويل داخل نفس المستند.']
                    ]);
                }
                $seen[$k] = true;
            }

            $code          = $validated['code'];
            $date          = $validated['transfer_date'];
            $store_from_id = (int) $validated['store_from_id'];
            $store_to_id   = (int) $validated['store_to_id'];
            $items         = $validated['items'];

            DB::transaction(function () use ($code,$date,$store_from_id,$store_to_id,$items) {
                foreach ($items as $it) {
                    $product_id    = (int) $it['product_id'];
                    $units_id      = (int) $it['units_id'];
                    $qty           = (float) $it['quantity'];
                    $batch_no      = $it['batch_no'] ?? null;
                    $transfer_no   = $it['transfer_number'] ?? null;
                    $notes         = $it['notes'] ?? null;

                    // تحويل للأصغر
                    $qty_base = $this->ucToBase($product_id, $qty, $units_id);

                    // خصم من المصدر ثم إضافة للوجهة
                    $this->decBalance($product_id, $store_from_id, $batch_no, null, $qty_base);
                    $this->incBalance($product_id, $store_to_id,   $batch_no, null, $qty_base);

                    // حركة خروج من المصدر
                    storemovement::create([
                        'product_id'     => $product_id,
                        'store_id'       => $store_from_id,
                        'units_id'       => $units_id,
                        'supplier_id'    => null,
                        'quantity'       => $qty * -1,    // سالب = خروج
                        'batch_no'       => $batch_no,
                        'expiry_date'    => null,
                        'movement_type'  => 'return',
                        'movement_number'=> $code,
                        'reference_no'   => $transfer_no,
                        'movement_date'  => $date,
                        'cost_price'     => null,
                        'sale_price'     => null,
                        'notes'          => 'Direct return OUT - TN: '.$transfer_no,
                        'user_id'        => auth()->id(),
                    ]);

                    // حركة دخول للوجهة
                    storemovement::create([
                        'product_id'     => $product_id,
                        'store_id'       => $store_to_id,
                        'units_id'       => $units_id,
                        'supplier_id'    => null,
                        'quantity'       => $qty,         // موجب = دخول
                        'batch_no'       => $batch_no,
                        'expiry_date'    => null,
                        'movement_type'  => 'return',
                        'movement_number'=> $code,
                        'reference_no'   => $transfer_no,
                        'movement_date'  => $date,
                        'cost_price'     => null,
                        'sale_price'     => null,
                        'notes'          => 'Direct return IN - TN: '.$transfer_no,
                        'user_id'        => auth()->id(),
                    ]);

                    // تخزين السطر (في نفس الجدول)
                    $transfer_id = null;
                    if ($transfer_no) {
                        $transfer_id = storetransfer::where('transfer_number',$transfer_no)->value('id');
                    }

                    storedirectreturn::create([
                        'code'             => $code,
                        'store_transfer_id'=> $transfer_id,
                        'transfer_number'  => $transfer_no,
                        'store_from_id'    => $store_from_id,
                        'store_to_id'      => $store_to_id,
                        'transfer_date'    => $date,
                        'product_id'       => $product_id,
                        'units_id'         => $units_id,
                        'quantity'         => $qty,
                        'batch_no'         => $batch_no,
                        'user_id'          => auth()->id(),
                        'notes'            => $notes,
                    ]);
                }
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'ok'       => true,
                    'message'  => 'تم الحفظ بنجاح.',
                    'redirect' => route('return_direct_to_store.index')
                ]);
            }

            return redirect()->route('return_direct_to_store.index')
                ->with('success','تم حفظ ارتجاع المخزن المباشر وتحديث الأرصدة والحركات بنجاح.');

        } catch (ValidationException $ve) {
            if ($request->expectsJson()) {
                return response()->json(['ok'=>false,'errors'=>$ve->errors()], 422);
            }
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('DirectReturn store error: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['ok'=>false,'message'=>$e->getMessage()], 500);
            }
            abort(500);
        }
    }

    /* ============================ SHOW / DELETE ============================ */

    // تفاصيل مستند (حسب id -> نجلب الكود ثم باقي السطور)
public function show($id)
{
    $row  = storedirectreturn::findOrFail($id);
    $rows = storedirectreturn::where('code', $row->code)->get();

    // جهّز المنتجات والوحدات
    $productIds = $rows->pluck('product_id')->unique();
    $unitIds    = $rows->pluck('units_id')->unique();

    $products = productdata::whereIn('id', $productIds)
        ->get(['id','name_ar','name_en','product_code'])
        ->keyBy('id');

    $units = unit::whereIn('id', $unitIds)
        ->get(['id','name_ar','name_en'])
        ->keyBy('id');

    // جهّز بيانات المخازن (الاسمين + الكود اليدوي إن وُجد)
    $stores = storemaindata::whereIn('id', [$row->store_from_id, $row->store_to_id])
        ->get(['id','name_ar','name_en','manual_code'])
        ->keyBy('id');

    $storeFrom = $stores->get($row->store_from_id);
    $storeTo   = $stores->get($row->store_to_id);

    $out = [
        'code'           => $row->code,
        'transfer_date'  => $row->transfer_date?->format('Y-m-d'),
        'store_from_id'  => $row->store_from_id,
        'store_to_id'    => $row->store_to_id,

        // ✅ الأسماء بدل الـID
        'store_from' => $storeFrom ? [
            'id'          => $storeFrom->id,
            'name_ar'     => $storeFrom->name_ar,
            'name_en'     => $storeFrom->name_en,
            'manual_code' => $storeFrom->manual_code,
        ] : null,
        'store_to' => $storeTo ? [
            'id'          => $storeTo->id,
            'name_ar'     => $storeTo->name_ar,
            'name_en'     => $storeTo->name_en,
            'manual_code' => $storeTo->manual_code,
        ] : null,

        'items' => [],
    ];

    foreach ($rows as $r) {
        $out['items'][] = [
            'product'         => $products[$r->product_id] ?? null,
            'unit'            => $units[$r->units_id] ?? null,
            'quantity'        => $r->quantity,
            'batch_no'        => $r->batch_no,
            'transfer_number' => $r->transfer_number,
            'notes'           => $r->notes,
        ];
    }

    return response()->json($out);
}


    public function destroy($id)
    {
        $row = storedirectreturn::findOrFail($id);
        $code = $row->code;

        DB::transaction(function () use ($code) {
            $rows = storedirectreturn::where('code',$code)->get();

            foreach ($rows as $r) {
                $product_id = (int) $r->product_id;
                $units_id   = (int) $r->units_id;
                $qty        = (float) $r->quantity;
                $batch_no   = $r->batch_no;
                $qty_base   = $this->ucToBase($product_id, $qty, $units_id);

                // عكس الأرصدة: أعد إلى المصدر، وأنقص من الوجهة
                $this->incBalance($product_id, (int)$r->store_from_id, $batch_no, null, $qty_base);

                $bal = productstorebalance::lockForUpdate()
                        ->where('product_id',$product_id)
                        ->where('store_id',(int)$r->store_to_id)
                        ->when($batch_no !== null, fn($w)=>$w->where('batch_no',$batch_no))
                        ->first();
                if ($bal) {
                    $bal->quantity_in_base_unit = max(0, (float)$bal->quantity_in_base_unit - $qty_base);
                    if ($bal->units_id) {
                        try {
                            $bal->quantity = $this->ucFromBase($product_id, (float)$bal->quantity_in_base_unit, (int)$bal->units_id);
                        } catch (\Throwable $e) {}
                    }
                    $bal->save();
                }

                $r->delete();
            }

            // حذف الحركات لكامل المستند
            storemovement::where('movement_number',$code)->delete();
        });

        if (request()->expectsJson()) {
            return response()->json(['ok'=>true,'message'=>'تم حذف المستند وعكس الحركات والأرصدة.']);
        }

        return back()->with('success','تم حذف المستند وعكس كل الحركات والأرصدة بنجاح.');
    }

    /* ============================ AJAX ============================ */

    // Select2: المخازن القابلة للاختيار كمصدر (هي التي استلمت تحويلات من قبل)
    public function ajaxStoreFroms(Request $request)
    {
        $q = trim($request->get('q',''));

        $ids = DB::table('store_transfers')
            ->whereNotNull('store_to_id')
            ->distinct()->pluck('store_to_id')->filter()->values();

        if ($ids->isEmpty()) return response()->json(['results'=>[]]);

        $rows = storemaindata::query()
            ->whereIn('id', $ids)
            ->when($q !== '', function($qr) use ($q){
                $qr->where(function($w) use ($q){
                    $w->where('name_ar','like',"%$q%")
                      ->orWhere('name_en','like',"%$q%")
                      ->orWhere('manual_code','like',"%$q%");
                });
            })
            ->orderBy('name_ar')->limit(50)
            ->get(['id','name_ar','name_en','manual_code']);

        return response()->json([
            'results' => $rows->map(fn($s)=>[
                'id'   => $s->id,
                'text' => ($s->manual_code ? $s->manual_code.' - ' : '').($s->name_ar ?? $s->name_en),
            ])
        ]);
    }

    // Select2: المخازن الشريكة (وجهة الارجاع) بناءً على المخزن المصدر المحدد
    public function ajaxStoreTos(Request $request)
    {
        $from = (int) $request->get('from_store_id', 0);
        if (!$from) return response()->json(['results'=>[]]);

        $partners = DB::table('store_transfers')
            ->where('store_to_id', $from)     // هذا المخزن سبق أن استلم من هؤلاء
            ->whereNotNull('store_from_id')
            ->distinct()
            ->pluck('store_from_id')
            ->filter()->values();

        if ($partners->isEmpty()) return response()->json(['results'=>[]]);

        $q = trim($request->get('q',''));
        $rows = storemaindata::query()
            ->whereIn('id', $partners)
            ->when($q !== '', function($qr) use ($q){
                $qr->where(function($w) use ($q){
                    $w->where('name_ar','like',"%$q%")
                      ->orWhere('name_en','like',"%$q%")
                      ->orWhere('manual_code','like',"%$q%");
                });
            })
            ->orderBy('name_ar')->limit(50)
            ->get(['id','name_ar','name_en','manual_code']);

        return response()->json([
            'results' => $rows->map(fn($s)=>[
                'id'   => $s->id,
                'text' => ($s->manual_code ? $s->manual_code.' - ' : '').($s->name_ar ?? $s->name_en),
            ])
        ]);
    }

    // وحدات الصنف
    public function ajaxUnits(Request $request)
    {
        $product_id = (int) $request->get('product_id',0);
        if (!$product_id) return response()->json(['results'=>[]]);

        $units = $this->ucUnits($product_id, app()->getLocale()==='ar' ? 'name_ar' : 'name_en');

        return response()->json([
            'results' => $units->map(fn($u)=>[
                'id'   => $u['unit_id'],
                'text' => $u['unit_name'],
                'ftb'  => $u['factor_to_base'],
            ])
        ]);
    }

    /**
     * حركات التحويل المُرشَّحة للارتجاع:
     * - يجب أن تكون st.store_to_id = المخزن المُرتجَع منه (الذي بيده البضاعة الآن)
     * - خياريا نقيّد بـ store_from_id = المخزن المرتجع إليه (الشريك)
     * - نرجع فقط البنود التي لها رصيد متبقّي في نفس «المخزن المُرتجَع منه» والباتش.
     * Params: store_from_id (required), store_to_id (optional), q (optional)
     */
    public function ajaxTransfers(Request $request)
    {
        $returnFrom = (int) $request->get('store_from_id', 0); // المخزن الذي سنُرجِع منه الآن
        $returnTo   = (int) $request->get('store_to_id', 0);   // المخزن الذي سنُرجِع إليه (اختياري للتصفية)
        $q          = trim($request->get('q',''));

        if (!$returnFrom) return response()->json(['results'=>[]]);

        // بنود التحويلات الواردة إلى هذا المخزن
        $rows = DB::table('store_transfers as st')
            ->join('products_data as p', 'p.id', '=', 'st.product_id')
            ->join('units as u', 'u.id', '=', 'st.units_id')
            ->where('st.store_to_id', $returnFrom)
            ->when($returnTo > 0, fn($qr)=>$qr->where('st.store_from_id', $returnTo))
            ->when($q !== '', function($qr) use ($q){
                $qr->where(function($w) use ($q){
                    $w->where('p.name_ar','like',"%$q%")
                      ->orWhere('p.name_en','like',"%$q%")
                      ->orWhere('p.product_code','like',"%$q%");
                });
            })
            ->select([
                'st.id',
                'st.transfer_number',
                'st.transfer_date',
                'st.product_id',
                'st.units_id',
                'st.quantity',
                'st.batch_no',
                'st.expiry_date',
                'p.name_ar as product_name_ar',
                'p.name_en as product_name_en',
                'p.product_code',
                'u.name_ar as unit_name_ar',
                'u.name_en as unit_name_en',
            ])
            ->orderByDesc('st.transfer_date')
            ->limit(200)
            ->get();

        if ($rows->isEmpty()) return response()->json(['results'=>[]]);

        // اجمع IDs المنتجات
        $prodIds = $rows->pluck('product_id')->unique()->values();

        // اجلب أرصدة المخزن المُرتجَع منه لهذه المنتجات، مجمعة بالباتش
        $balances = DB::table('product_store_balances')
            ->where('store_id', $returnFrom)
            ->whereIn('product_id', $prodIds)
            ->where('quantity_in_base_unit','>',0)
            ->select('product_id','batch_no', DB::raw('SUM(quantity_in_base_unit) as avail_base'))
            ->groupBy('product_id','batch_no')
            ->get()
            ->reduce(function($acc,$r){
                $acc[$r->product_id.'|'.$r->batch_no] = (float)$r->avail_base;
                return $acc;
            }, []);

        // حضّر النتيجة
        $result = [];
        foreach ($rows as $r) {
            $key = $r->product_id.'|'.(string)$r->batch_no;
            $availBase = $balances[$key] ?? 0.0;
            if ($availBase <= 0) continue; // فقط ما له متاح

            $availInRowUnit = $this->ucFromBase((int)$r->product_id, (float)$availBase, (int)$r->units_id);

            $result[] = [
                'transfer_number'     => $r->transfer_number,
                'transfer_date'       => $r->transfer_date ? date('Y-m-d', strtotime($r->transfer_date)) : null,
                'product_id'          => (int) $r->product_id,
                'product_text'        => (($r->product_code ? $r->product_code.' / ' : '') . ($r->product_name_ar ?? $r->product_name_en)),
                'units_id'            => (int) $r->units_id,
                'unit_text'           => ($r->unit_name_ar ?? $r->unit_name_en),
                'quantity_transfered' => (float) $r->quantity,
                'batch_no'            => $r->batch_no,
                'expiry_date'         => $r->expiry_date ? date('Y-m-d', strtotime($r->expiry_date)) : null,
                'available_base'      => $availBase,                  // بالأصغر
                'available_unit'      => round($availInRowUnit, 6),   // بنفس وحدة السطر
            ];
        }

        return response()->json(['results' => array_values($result)]);
    }

    // باتشات متاحة للصنف من نفس مخزن المصدر (اختياري تقييد برقم تحويل)
    public function ajaxBatches(Request $request)
    {
        $product_id = (int) $request->get('product_id',0);
        $store_id   = (int) $request->get('store_id',0);
        $transfer_no= trim($request->get('transfer_number',''));
        if (!$product_id || !$store_id) return response()->json(['results'=>[]]);

        $qBal = productstorebalance::where('product_id',$product_id)
            ->where('store_id',$store_id)
            ->where('quantity_in_base_unit','>',0);

        // لو فيه رقم تحويل، قيّد الباتشات بالتحويلات التي دخلت إلى هذا المخزن برقم التحويل
        if ($transfer_no !== '') {
            $batchNos = storetransfer::where('transfer_number',$transfer_no)
                ->where('store_to_id',$store_id) // ✅ المخزن الذي استلم التحويل
                ->where('product_id',$product_id)
                ->pluck('batch_no')->filter()->unique()->values();
            if ($batchNos->isNotEmpty()) {
                $qBal->whereIn('batch_no',$batchNos);
            }
        }

        $rows = $qBal->orderBy('expiry_date')->get(['batch_no','expiry_date','quantity_in_base_unit']);

        return response()->json([
            'results' => $rows->map(fn($r)=>[
                'batch_no'  => $r->batch_no,
                'expiry'    => $r->expiry_date?->format('Y-m-d'),
                'available' => (float)$r->quantity_in_base_unit,
            ])
        ]);
    }

    // يعيد أول id لمستند بكود معيّن (بديل سريع لو حبيت)
    public function firstIdByCode(Request $request)
    {
        $code = trim($request->get('code',''));
        if ($code==='') return response()->json(['first_id'=>null]);
        $row = storedirectreturn::where('code',$code)->orderBy('id')->first();
        return response()->json(['first_id'=>$row?->id]);
    }
}
