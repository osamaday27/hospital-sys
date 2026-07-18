<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\models\supply_chain\SectionStoreReturn;          // ✅ الجدول الجديد section_store_returns
use App\Models\supply_chain\productstorebalance;
use App\models\supply_chain\storemovement;
use App\Models\supply_chain\storemaindata;
use App\models\supply_chain\productdata;
use App\Models\supply_chain\unit;

use App\models\hr\section as HrSection;                  // ✅ sections بدلاً من departments

use App\Models\supply_chain\departmentItemRequest;       // department_item_requests_stores
use App\Models\supply_chain\departmentItemRequestDetail; // department_item_request_details_stroe

use App\Traits\UnitConversionHelpers;

class return_from_sectioncontroller extends Controller
{
    use UnitConversionHelpers;

    /* ============================ Helpers ============================ */

    protected function nextCode(): string
    {
        $year = date('Y');
        $last = SectionStoreReturn::withTrashed()->latest('id')->first();
        $seq  = ($last?->id ?? 0) + 1;
        return 'RFS-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /** زيادة رصيد المخزن (بالأصغر) */
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
                } catch (\Throwable $e) {}
            }
            $bal->save();
        } else {
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

    /* ============================ Index (DataTables) ============================ */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $draw   = (int) $request->get('draw', 1);
            $start  = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 10);
            $search = trim($request->input('search.value', ''));

            // تجميع على مستوى المستند (code)
            $base = SectionStoreReturn::query()
                ->select([
                    'code',
                    DB::raw('MIN(return_date)  AS document_date'),
                    DB::raw('MIN(section_id)   AS section_id'),
                    DB::raw('MIN(store_id)     AS store_id'),
                    DB::raw('COUNT(*)          AS items_count'),
                ])
                ->groupBy('code');

            $total = DB::query()->fromSub($base, 't')->count();

            $wrapped = DB::query()->fromSub($base, 't')
                ->when($search !== '', fn($q)=>$q->where('t.code','like',"%{$search}%"));

            $filtered = (clone $wrapped)->count();

            $rows = $wrapped->orderBy('t.document_date','desc')->skip($start)->take($length)->get();

            // أسماء الأقسام (sections) والمخازن
            $sectionIds = collect($rows)->pluck('section_id')->unique()->filter()->values();
            $storeIds   = collect($rows)->pluck('store_id')->unique()->filter()->values();

            $sections = HrSection::whereIn('id',$sectionIds)->get(['id','name_ar','name_ne'])->keyBy('id');
            $stores   = storemaindata::whereIn('id',$storeIds)->get(['id','name_ar','name_en'])->keyBy('id');

            $data = [];
            foreach ($rows as $idx => $r) {
                $sec = $sections->get($r->section_id);
                $sto = $stores->get($r->store_id);

                $viewBtn = '<button class="btn btn-sm btn-info view-btn" data-code="'.e($r->code).'" title="عرض"><i class="ri-eye-line"></i></button>';
                $delBtn  = '<button class="btn btn-sm btn-danger delete-btn" data-code="'.e($r->code).'" title="حذف"><i class="ri-delete-bin-6-line"></i></button>';

                $data[] = [
                    'DT_RowIndex'   => $start + $idx + 1,
                    'code'          => e($r->code),
                    'section'       => e($sec?->name_ar ?? $sec?->name_ne ?? '-'),
                    'store'         => e($sto?->name_ar ?? $sto?->name_en ?? '-'),
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

        // أول تحميل للفيو
        return view('supply_chain.program.return_items.return_from_section.index');
    }

    /* ============================ Create / Store ============================ */

    public function create()
    {
        return view('supply_chain.program.return_items.return_from_section.create', [
            'code'  => $this->nextCode(),
            'today' => date('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required','string','max:50'],
            'return_date' => ['required','date'],
            'section_id'  => ['required','integer','exists:hr_sections,id'],
            'store_id'    => ['required','integer','exists:store_main_date,id'],

            'items'                    => ['required','array','min:1'],
            'items.*.request_number'   => ['required','string','max:50'],
            'items.*.product_id'       => ['required','integer','exists:products_data,id'],
            'items.*.unit_id'          => ['required','integer','exists:units,id'], // ✅ unit_id
            'items.*.quantity'         => ['required','numeric','gt:0'],
            'items.*.batch_no'         => ['nullable','string','max:100'],
            'items.*.notes'            => ['nullable','string','max:500'],
        ]);

        // منع إعادة استخدام نفس الكود
        if (SectionStoreReturn::where('code',$validated['code'])->exists()) {
            return back()->with('error','هذا الكود مستخدم مسبقًا، رجاء إعادة فتح الصفحة لتوليد كود جديد.')->withInput();
        }

        // منع تكرار (request_number + product + batch) داخل المستند
        $seen = [];
        foreach ($validated['items'] as $it) {
            $k = ($it['request_number'] ?? '').'|'.$it['product_id'].'|'.($it['batch_no'] ?? '');
            if (isset($seen[$k])) {
                return back()->with('error','ممنوع تكرار نفس الصنف والباتش ونفس رقم الطلب داخل المستند.')->withInput();
            }
            $seen[$k] = true;
        }

        $code      = $validated['code'];
        $date      = $validated['return_date'];
        $sectionId = (int) $validated['section_id'];
        $storeId   = (int) $validated['store_id'];
        $items     = $validated['items'];

        // ✅ منع الخلط: كل أرقام الطلبات يجب أن تعود لنفس (القسم + المخزن) وحالتها processed
        $reqNos       = collect($items)->pluck('request_number')->unique()->values();
        $validReqNos  = departmentItemRequest::whereIn('request_number', $reqNos)
                            ->where('department_id', $sectionId) // عمود الطلبات اسمه department_id لكنه يشير للـ section
                            ->where('store_id', $storeId)
                            ->where('status', 'processed')
                            ->pluck('request_number')->unique()->values();

        if ($validReqNos->count() !== $reqNos->count()) {
            abort(422, 'لا يمكن إضافة طلبات من أقسام أخرى أو مخازن مختلفة داخل نفس المستند، أو طلبات حالتها ليست processed.');
        }

        DB::transaction(function () use ($code,$date,$sectionId,$storeId,$items) {
            foreach ($items as $it) {
                $reqNo      = $it['request_number'];
                $product_id = (int) $it['product_id'];
                $unit_id    = (int) $it['unit_id'];       // ✅
                $qty        = (float) $it['quantity'];
                $batch_no   = $it['batch_no'] ?? null;
                $notes      = $it['notes'] ?? null;

                // حوّل للأصغر + تحقق من "المتاح للإرجاع" (من الطلبات processed فقط)
                $qty_base = $this->ucToBase($product_id, $qty, $unit_id);

                // إجمالي المُصرّف من الطلبات processed
                $issued_base = departmentItemRequestDetail::query()
                    ->join('department_item_requests_stores as r', 'r.id','=','department_item_request_details_stroe.request_id')
                    ->where('r.department_id', $sectionId)
                    ->where('r.store_id', $storeId)
                    ->where('r.request_number', $reqNo)
                    ->where('r.status','processed')
                    ->where('department_item_request_details_stroe.product_id', $product_id)
                    ->get(['department_item_request_details_stroe.quantity','department_item_request_details_stroe.unit_id'])
                    ->sum(function($row) use ($product_id){
                        return (float) app(self::class)->ucToBase($product_id, (float)$row->quantity, (int)$row->unit_id);
                    });

                // المرتجعات السابقة (حوِّلها للأصغر)
                $returned_base = SectionStoreReturn::where('section_id',$sectionId)
                    ->where('store_id',$storeId)
                    ->where('request_number',$reqNo)
                    ->where('product_id',$product_id)
                    ->get(['quantity','unit_id'])
                    ->sum(function($r) use ($product_id){
                        return (float) app(self::class)->ucToBase($product_id, (float)$r->quantity, (int)$r->unit_id);
                    });

                $available_base = max(0.0, (float)$issued_base - (float)$returned_base);

                if ($available_base <= 0 || $qty_base > $available_base) {
                    abort(422, 'الكمية المدخلة أكبر من المتاح للإرجاع من هذا الطلب/الصنف (طلبات processed فقط).');
                }

                // زيادة رصيد المخزن المستلم
                $this->incBalance($product_id, $storeId, $batch_no, null, $qty_base);

                // حركة مخزن (دخول) — النوع "return" عندك متوافق
                storemovement::create([
                    'product_id'     => $product_id,
                    'store_id'       => $storeId,
                    'units_id'       => $unit_id,
                    'supplier_id'    => null,
                    'quantity'       => $qty,
                    'batch_no'       => $batch_no,
                    'expiry_date'    => null,
                    'movement_type'  => 'return',
                    'movement_number'=> $code,
                    'reference_no'   => $reqNo,
                    'movement_date'  => $date,
                    'cost_price'     => null,
                    'sale_price'     => null,
                    'notes'          => $notes,
                    'user_id'        => auth()->id(),
                ]);

                // تخزين السطر
                SectionStoreReturn::create([
                    'code'           => $code,
                    'request_number' => $reqNo,
                    'section_id'     => $sectionId,
                    'store_id'       => $storeId,
                    'return_date'    => $date,
                    'product_id'     => $product_id,
                    'unit_id'        => $unit_id,
                    'batch_no'       => $batch_no,
                    'quantity'       => $qty,
                    'notes'          => $notes,
                    'created_by'     => auth()->id(),
                ]);
            }
        });

        return redirect()->route('return_from_section.index')
            ->with('success','تم حفظ مرتجع القسم إلى المخزن (processed فقط) وتحديث الأرصدة والحركات بنجاح.');
    }

    /* ============================ SHOW / DELETE ============================ */

    public function firstIdByCode(Request $request)
    {
        $code = trim($request->get('code',''));
        if ($code==='') return response()->json(['first_id'=>null]);
        $row = SectionStoreReturn::where('code',$code)->orderBy('id')->first();
        return response()->json(['first_id'=>$row?->id]);
    }

    public function show($id)
    {
        $row  = SectionStoreReturn::findOrFail($id);
        $rows = SectionStoreReturn::where('code',$row->code)->get();

        $productIds = $rows->pluck('product_id')->unique();
        $unitIds    = $rows->pluck('unit_id')->unique();
        $sectionId  = $row->section_id;
        $storeId    = $row->store_id;

        $products = productdata::whereIn('id',$productIds)->get(['id','name_ar','name_en','product_code'])->keyBy('id');
        $units    = unit::whereIn('id',$unitIds)->get(['id','name_ar','name_en'])->keyBy('id');
        $section  = HrSection::find($sectionId, ['id','name_ar','name_ne']);
        $store    = storemaindata::find($storeId);

        $out = [
            'code'        => $row->code,
            'return_date' => $row->return_date?->format('Y-m-d'),
            'section'     => $section ? ['id'=>$section->id,'name_ar'=>$section->name_ar,'name_ne'=>$section->name_ne] : null,
            'store'       => $store? ['id'=>$store->id,'name_ar'=>$store->name_ar,'name_en'=>$store->name_en] : null,
            'items'       => [],
        ];
        foreach ($rows as $r) {
            $out['items'][] = [
                'product'        => $products[$r->product_id] ?? null,
                'unit'           => $units[$r->unit_id] ?? null,
                'quantity'       => $r->quantity,
                'batch_no'       => $r->batch_no,
                'request_number' => $r->request_number,
                'notes'          => $r->notes,
            ];
        }
        return response()->json($out);
    }

    public function destroy($id)
    {
        $row  = SectionStoreReturn::findOrFail($id);
        $code = $row->code;

        DB::transaction(function () use ($code) {
            $rows = SectionStoreReturn::where('code',$code)->get();

            foreach ($rows as $r) {
                $product_id = (int) $r->product_id;
                $unit_id    = (int) $r->unit_id;
                $qty        = (float) $r->quantity;
                $batch_no   = $r->batch_no;
                $qty_base   = $this->ucToBase($product_id, $qty, $unit_id);

                // عكس إضافة الرصيد: إنقاص من المخزن المستلم
                $bal = productstorebalance::lockForUpdate()
                    ->where('product_id',$product_id)
                    ->where('store_id',(int)$r->store_id)
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

            // حذف جميع الحركات المرتبطة بهذا الكود
            storemovement::where('movement_number',$code)->delete();
        });

        return response()->json(['success'=>true,'message'=>'تم حذف المستند وعكس الأرصدة والحركات.']);
    }

    /* ============================ AJAX (Select2 + مصادر) ============================ */

    // الأقسام (sections)
    public function ajaxDepartments(Request $request)
    {
        $q = trim($request->get('q',''));
        $rows = HrSection::query()
            ->when($q !== '', fn($qr)=>$qr->where(function($w) use($q){
                $w->where('name_ar','like',"%$q%")
                  ->orWhere('name_ne','like',"%$q%");
            }))
            ->orderBy('name_ar')->limit(50)->get(['id','name_ar','name_ne']);

        return response()->json([
            'results' => $rows->map(fn($d)=>[
                'id'   => $d->id,
                'text' => $d->name_ar ?? $d->name_ne,
            ])
        ]);
    }

    /**
     * المخازن المرتبطة بطلبات هذا القسم (processed فقط)
     * ?department_id= (يقصد بها section_id)
     */
    public function ajaxStores(Request $request)
    {
        $sectionId = (int) $request->get('department_id', 0); // اسم الباراميتر القديم محفوظ للتوافق
        $q         = trim($request->get('q',''));

        $query = storemaindata::query()->select(['id','name_ar','name_en','manual_code']);

        if ($sectionId) {
            $storeIds = departmentItemRequest::where('department_id', $sectionId)
                ->where('status','processed')                 // ✅ فقط processed
                ->whereNotNull('store_id')
                ->distinct()->pluck('store_id')->filter()->values();

            $query->whereIn('id', $storeIds ?: [-1]);
        }

        $rows = $query
            ->when($q !== '', fn($qr)=>$qr->where(function($w) use($q){
                $w->where('name_ar','like',"%$q%")
                  ->orWhere('name_en','like',"%$q%")
                  ->orWhere('manual_code','like',"%$q%");
            }))
            ->orderBy('name_ar')->limit(50)->get();

        return response()->json([
            'results' => $rows->map(fn($s)=>[
                'id'   => $s->id,
                'text' => ($s->manual_code ? $s->manual_code.' - ' : '').($s->name_ar ?? $s->name_en),
            ])
        ]);
    }

    // وحدات الصنف + عامل التحويل
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
     * طلبات القسم (processed فقط) حسب القسم + المخزن
     * Params: department_id (section_id) required, store_id required, q optional
     */
    public function ajaxRequests(Request $request)
    {
        $sectionId = (int) $request->get('department_id',0); // الحفاظ على نفس الاسم
        $storeId   = (int) $request->get('store_id',0);
        $q         = trim($request->get('q',''));

        if (!$sectionId || !$storeId) return response()->json(['results'=>[]]);

        // سطور الطلبات (processed فقط)
        $rows = departmentItemRequestDetail::query()
            ->join('department_item_requests_stores as r', 'r.id','=','department_item_request_details_stroe.request_id')
            ->join('products_data as p', 'p.id','=','department_item_request_details_stroe.product_id')
            ->where('r.department_id',$sectionId)
            ->where('r.store_id',$storeId)
            ->where('r.status','processed') // ✅ فقط processed
            ->when($q !== '', fn($qr)=>$qr->where(function($w) use($q){
                $w->where('r.request_number','like',"%$q%")
                  ->orWhere('p.name_ar','like',"%$q%")
                  ->orWhere('p.name_en','like',"%$q%")
                  ->orWhere('p.product_code','like',"%$q%");
            }))
            ->select([
                'r.request_number',
                'r.request_date',
                'department_item_request_details_stroe.product_id',
                'department_item_request_details_stroe.unit_id',
                'department_item_request_details_stroe.quantity',
                'p.name_ar as product_name_ar',
                'p.name_en as product_name_en',
                'p.product_code',
            ])
            ->orderByDesc('r.request_date')
            ->limit(300)
            ->get();

        if ($rows->isEmpty()) return response()->json(['results'=>[]]);

        // نحسب المتاح للإرجاع لكل (request_number, product)
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->request_number.'|'.$r->product_id;

            $issued_base = $this->ucToBase((int)$r->product_id, (float)$r->quantity, (int)$r->unit_id);
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'request_number' => $r->request_number,
                    'request_date'   => $r->request_date ? date('Y-m-d', strtotime($r->request_date)) : null,
                    'product_id'     => (int) $r->product_id,
                    'product_text'   => (($r->product_code ? $r->product_code.' / ' : '').($r->product_name_ar ?? $r->product_name_en)),
                    'issued_base'    => 0.0,
                ];
            }
            $grouped[$key]['issued_base'] += $issued_base;
        }

        // المرتجعات السابقة لنفس (section/store/request_number/product)
        $retPrev = SectionStoreReturn::where('section_id',$sectionId)
            ->where('store_id',$storeId)
            ->whereIn('request_number', array_unique(array_column($grouped,'request_number')))
            ->whereIn('product_id', array_unique(array_column($grouped,'product_id')))
            ->get(['request_number','product_id','quantity','unit_id']);

        $returned_map = [];
        foreach ($retPrev as $rr) {
            $k = $rr->request_number.'|'.$rr->product_id;
            $returned_map[$k] = ($returned_map[$k] ?? 0) + $this->ucToBase((int)$rr->product_id, (float)$rr->quantity, (int)$rr->unit_id);
        }

        $out = [];
        foreach ($grouped as $k => $g) {
            $ret_base = $returned_map[$k] ?? 0.0;
            $available_base = max(0.0, (float)$g['issued_base'] - (float)$ret_base);
            if ($available_base <= 0) continue;

            $out[] = [
                'request_number' => $g['request_number'],
                'request_date'   => $g['request_date'],
                'product_id'     => $g['product_id'],
                'product_text'   => $g['product_text'],
                'available_base' => $available_base,
            ];
        }

        return response()->json(['results' => array_values($out)]);
    }

    // باتشات من رصيد المخزن المستلم (اختياري للسرعة)
    public function ajaxBatches(Request $request)
    {
        $product_id = (int) $request->get('product_id',0);
        $store_id   = (int) $request->get('store_id',0);
        if (!$product_id || !$store_id) return response()->json(['results'=>[]]);

        $rows = productstorebalance::where('product_id',$product_id)
            ->where('store_id',$store_id)
            ->where('quantity_in_base_unit','>',0)
            ->orderBy('expiry_date')
            ->get(['batch_no','expiry_date','quantity_in_base_unit']);

        return response()->json([
            'results' => $rows->map(fn($r)=>[
                'batch_no'  => $r->batch_no,
                'expiry'    => $r->expiry_date?->format('Y-m-d'),
                'available' => (float)$r->quantity_in_base_unit,
            ])
        ]);
    }
}
