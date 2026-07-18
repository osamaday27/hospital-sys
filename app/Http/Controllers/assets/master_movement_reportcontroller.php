<?php

namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

// Assets models
use App\models\assets\assetitem;
use App\models\assets\assetcategory;
use App\models\assets\assetbrand;
use App\models\assets\assetmodel;
use App\models\assets\assetlocation;

// External refs (store/supplier)
use App\Models\supply_chain\storemaindata as StoreMainData;
use App\Models\Suppliers\suppliersdata as SuppliersData;

// Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class master_movement_reportcontroller extends Controller
{
    /** ================= Entry ================= */
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('assets.reports.master_movement.print', compact('rows','meta'));
        }

        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        if ($r->wantsJson() || $r->ajax()) {
            if (!$this->hasAnyFilter($r) && $r->get('no_data')) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $draw   = (int) $r->get('draw', 1);
            $start  = (int) $r->get('start', 0);
            $length = (int) $r->get('length', 10);
            $search = trim((string) data_get($r->all(), 'search.value', ''));

            $q = $this->baseQuery($r);

            if ($search !== '') {
                $q->where(function($x) use ($search){
                    $x->where('ai.code','like',"%{$search}%")
                      ->orWhere('ai.name_ar','like',"%{$search}%")
                      ->orWhere('ai.name_en','like',"%{$search}%")
                      ->orWhere('ai.serial_no','like',"%{$search}%")
                      ->orWhere('ac.name_ar','like',"%{$search}%")
                      ->orWhere('ac.name_en','like',"%{$search}%")
                      ->orWhere('ab.name','like',"%{$search}%")
                      ->orWhere('am.name','like',"%{$search}%")
                      ->orWhere(DB::raw("COALESCE(al.name_ar, al.name_en)"),'like',"%{$search}%")
                      ->orWhere(DB::raw("COALESCE(st.name_ar, st.name_en)"),'like',"%{$search}%")
                      ->orWhere(DB::raw("COALESCE(sp.name_ar, sp.name_en)"),'like',"%{$search}%");
                });
            }

            // ترتيب
            $orderColIdx = (int) data_get($r->all(), 'order.0.column', 0);
            $orderDir    = data_get($r->all(), 'order.0.dir', 'asc');
            $cols = [
                0=>'ai.code', 1=>'name', 2=>'category_name', 3=>'brand_name', 4=>'model_name',
                5=>'ai.serial_no', 6=>'location_name', 7=>'custodian_name',
                8=>'store_name', 9=>'supplier_name',
                10=>'ai.purchase_date', 11=>'ai.cost', 12=>'ai.status',
                13=>'addition_source', 14=>'last_movement_at',
                15=>'qty_total', 16=>'qty_assigned', 17=>'qty_available', 18=>'nbv'
            ];
            $orderBy = $cols[$orderColIdx] ?? 'ai.code';
            $q->orderBy($orderBy, $orderDir);

            $recordsFiltered = (clone $q)->count();
            $rows = $q->skip($start)->take($length)->get();

            $data = $rows->map(function($r){
                return [
                    'code'            => $r->code,
                    'name'            => $r->name,
                    'category'        => $r->category_name,
                    'brand'           => $r->brand_name,
                    'model'           => $r->model_name,
                    'serial'          => $r->serial_no,
                    'location'        => $r->location_name,
                    'custodian'       => $r->custodian_name ?: '—',
                    'store'           => $r->store_name ?: '—',
                    'supplier'        => $r->supplier_name ?: '—',
                    'purchase_date'   => $r->purchase_date ? Carbon::parse($r->purchase_date)->format('Y-m-d') : '—',
                    'cost'            => number_format((float)$r->cost, 2),
                    'status'          => $this->statusLabel($r->status),
                    'addition_source' => $this->additionSourceLabel($r->addition_source),
                    'last_movement'   => $this->formatLastMovement($r->last_status, $r->last_assignable, $r->last_movement_at, $r->last_movement_qty),
                    'qty_total'       => (int) $r->qty_total,
                    'qty_assigned'    => (int) $r->qty_assigned,
                    'qty_available'   => (int) $r->qty_available,
                    'nbv'             => number_format((float)$r->nbv, 2),
                ];
            })->values();

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        // HTML
        $filters = $this->getFiltersData();
        return view('assets.reports.master_movement.index', compact('filters'));
    }

    /** ============== Base Query ============== */
protected function baseQuery(Request $r)
{
    // أول تحويل للأصل (لتحديد مصدر الإضافة: direct vs conversion)
    $firstConv = DB::table('asset_conversions as cv')
        ->select('cv.asset_item_id', DB::raw('MIN(cv.conversion_date) as first_conv_at'))
        ->whereNull('cv.deleted_at')
        ->groupBy('cv.asset_item_id');

    // إجمالي كميات التحويلات (نستخدمها كبديل للـ ai.qty إن لم تكن موجودة)
    $convQty = DB::table('asset_conversions as c')
        ->select('c.asset_item_id', DB::raw('SUM(c.qty_in_base) as total_conv_qty'))
        ->whereNull('c.deleted_at')
        ->groupBy('c.asset_item_id');

    // أحدث حركة إسناد + كميتها
    $lastAssign = DB::table('asset_assignments as la')
        ->select(
            'la.asset_id',
            'la.assignable_name',
            'la.status',
            'la.qty',
            DB::raw('COALESCE(la.returned_at, la.assigned_at, la.created_at) as mv_at')
        )
        ->whereNull('la.deleted_at')
        ->whereIn('la.id', function($q){
            $q->from('asset_assignments as x')
              ->select(DB::raw('MAX(x.id)'))
              ->whereNull('x.deleted_at')
              ->groupBy('x.asset_id');
        });

    // مجموع الكميات المفتوحة حاليًا (assigned)
    $openAssignQty = DB::table('asset_assignments as oa')
        ->select('oa.asset_id', DB::raw('SUM(oa.qty) as open_assigned_qty'))
        ->whereNull('oa.deleted_at')
        ->where('oa.status','assigned')
        ->groupBy('oa.asset_id');

    $q = DB::table('asset_items as ai')
        ->leftJoin('asset_categories as ac','ac.id','=','ai.category_id')
        ->leftJoin('asset_brands as ab','ab.id','=','ai.brand_id')
        ->leftJoin('asset_models as am','am.id','=','ai.model_id')
        ->leftJoin('asset_locations as al','al.id','=','ai.location_id')

        // ملاحظة: غيّر اسم الجدول هنا لو مختلف عندك
        ->leftJoin('store_main_date as st','st.id','=','ai.store_id')
        ->leftJoin('suppliers_data as sp','sp.id','=','ai.supplier_id')

        ->leftJoinSub($firstConv, 'fc', 'fc.asset_item_id','=','ai.id')
        ->leftJoinSub($convQty,   'cq', 'cq.asset_item_id','=','ai.id')
        ->leftJoinSub($lastAssign,'la', 'la.asset_id','=','ai.id')
        ->leftJoinSub($openAssignQty,'oq','oq.asset_id','=','ai.id')
        ->whereNull('ai.deleted_at');

    $q->select([
        'ai.id','ai.code','ai.serial_no','ai.purchase_date','ai.cost','ai.status',
        DB::raw("COALESCE(ai.name_ar, ai.name_en) as name"),
        DB::raw("COALESCE(ai.net_book_value, ai.cost - ai.accumulated_depreciation) as nbv"),

        'ac.name_ar as category_name',
        'ab.name    as brand_name',
        'am.name    as model_name',
        DB::raw("COALESCE(al.name_ar, al.name_en) as location_name"),
        DB::raw("COALESCE(st.name_ar, st.name_en) as store_name"),
        DB::raw("COALESCE(sp.name_ar, sp.name_en) as supplier_name"),

        // مصدر الإضافة
        DB::raw("CASE WHEN fc.first_conv_at IS NULL THEN 'direct' ELSE 'conversion' END as addition_source"),

        // آخر حركة + كميتها
        DB::raw("la.assignable_name as last_assignable"),
        DB::raw("la.status as last_status"),
        DB::raw("la.mv_at as last_movement_at"),
        DB::raw("la.qty   as last_movement_qty"),

        // ✅ المسؤول الحالي: لو آخر سجل إسناد حالته assigned نعرض اسمه، خلاف ذلك NULL
        DB::raw("CASE WHEN la.status = 'assigned' THEN la.assignable_name ELSE NULL END as custodian_name"),

        // الكميات
        DB::raw("COALESCE(ai.qty, cq.total_conv_qty, 1) as qty_total"),
        DB::raw("COALESCE(oq.open_assigned_qty, 0)    as qty_assigned"),
        DB::raw("GREATEST(COALESCE(ai.qty, cq.total_conv_qty, 1) - COALESCE(oq.open_assigned_qty, 0), 0) as qty_available"),
    ]);

    /* ===== الفلاتر ===== */
    if ($f = $r->get('purchase_from')) $q->whereDate('ai.purchase_date','>=',$f);
    if ($t = $r->get('purchase_to'))   $q->whereDate('ai.purchase_date','<=',$t);

    if ($ids = $this->arr($r->get('category_ids'))) $q->whereIn('ai.category_id',$ids);
    if ($ids = $this->arr($r->get('brand_ids')))    $q->whereIn('ai.brand_id',$ids);
    if ($ids = $this->arr($r->get('model_ids')))    $q->whereIn('ai.model_id',$ids);
    if ($ids = $this->arr($r->get('location_ids'))) $q->whereIn('ai.location_id',$ids);

    if ($ids = $this->arr($r->get('store_ids')))    $q->whereIn('ai.store_id',$ids);
    if ($ids = $this->arr($r->get('supplier_ids'))) $q->whereIn('ai.supplier_id',$ids);

    if ($st  = $this->arr($r->get('status')))       $q->whereIn('ai.status',$st);

    // مصدر الإضافة
    if ($src = $r->get('addition_source')) {
        if ($src === 'direct')     $q->whereNull('fc.first_conv_at');
        if ($src === 'conversion') $q->whereNotNull('fc.first_conv_at');
    }

    // تكلفة و NBV
    if ($min = $r->get('cost_min')) $q->where('ai.cost','>=',(float)$min);
    if ($max = $r->get('cost_max')) $q->where('ai.cost','<=',(float)$max);
    if ($min = $r->get('nbv_min'))  $q->where(DB::raw("COALESCE(ai.net_book_value, ai.cost - ai.accumulated_depreciation)"),'>=',(float)$min);
    if ($max = $r->get('nbv_max'))  $q->where(DB::raw("COALESCE(ai.net_book_value, ai.cost - ai.accumulated_depreciation)"),'<=' ,(float)$max);

    // فلاتر الكمية (اختياري)
    if ($min = $r->get('qty_min'))  $q->having('qty_total','>=',(int)$min);
    if ($max = $r->get('qty_max'))  $q->having('qty_total','<=',(int)$max);

    return $q;
}

    /** Data for filters lists */
    protected function getFiltersData(): array
    {
        $statusKeys = ['in_service','maintenance','retired','scrapped'];

        return [
            'categories' => assetcategory::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'brands'     => assetbrand::query()->orderBy('name')->get(['id','name']),
            'models'     => assetmodel::query()->orderBy('name')->get(['id','name','brand_id']),
            'locations'  => assetlocation::query()->orderBy('name_ar')->get(['id','name_ar','name_en']),
            'statuses'   => $statusKeys,
            'stores'     => StoreMainData::select('id','manual_code','name_ar','name_en')->orderBy('name_ar')->get(),
            'suppliers'  => SuppliersData::select('id','name_ar','name_en','code')->orderBy('name_ar')->get(),
        ];
    }

    protected function hasAnyFilter(Request $r): bool
    {
        return (bool) collect([
            'purchase_from','purchase_to','category_ids','brand_ids','model_ids','location_ids',
            'store_ids','supplier_ids','status','addition_source',
            'cost_min','cost_max','nbv_min','nbv_max','qty_min','qty_max','search.value'
        ])->some(fn($k)=> filled($r->get($k)));
    }

    private function formatLastMovement(?string $status, ?string $name, $at, $qty = null): string
    {
        $date = $at ? Carbon::parse($at)->format('Y-m-d') : '—';
        $qtxt = $qty !== null ? " • Qty: {$qty}" : '';
        if (!$status) return '—';
        if ($status === 'assigned') return "Assign → {$name} ({$date}{$qtxt})";
        if ($status === 'returned') return "Return ({$date}{$qtxt})";
        return "{$status} ({$date}{$qtxt})";
    }

    protected function fetchAllForReport(Request $r): array
    {
        $q = $this->baseQuery($r)->orderBy('ai.code');

        $rows = $q->get()->map(function($r){
            return [
                'code'            => $r->code,
                'name'            => $r->name,
                'category'        => $r->category_name,
                'brand'           => $r->brand_name,
                'model'           => $r->model_name,
                'serial'          => $r->serial_no,
                'location'        => $r->location_name,
                'custodian'       => $r->custodian_name ?: '—',
                'store'           => $r->store_name ?: '—',
                'supplier'        => $r->supplier_name ?: '—',
                'purchase_date'   => $r->purchase_date ? Carbon::parse($r->purchase_date)->format('Y-m-d') : '—',
                'cost'            => (float)$r->cost,
                'status'          => $this->statusLabel($r->status),
                'addition_source' => $this->additionSourceLabel($r->addition_source),
                'last_movement'   => $this->formatLastMovement($r->last_status, $r->last_assignable, $r->last_movement_at, $r->last_movement_qty),
                'qty_total'       => (int) $r->qty_total,
                'qty_assigned'    => (int) $r->qty_assigned,
                'qty_available'   => (int) $r->qty_available,
                'nbv'             => (float)$r->nbv,
            ];
        });

        $meta = $this->buildMeta($r, $rows);
        return [$rows, $meta];
    }

    protected function buildMeta(Request $r, $rows): array
    {
        $catNames = $this->pluckNames(assetcategory::class, 'name_ar', $r->get('category_ids'));
        $brdNames = $this->pluckNames(assetbrand::class, 'name',    $r->get('brand_ids'));
        $modNames = $this->pluckNames(assetmodel::class, 'name',    $r->get('model_ids'));
        $locNames = $this->pluckNames(assetlocation::class, 'name_ar', $r->get('location_ids'), 'name_en');

        $storeNames = $this->pluckNames(StoreMainData::class, 'name_ar', $r->get('store_ids'), 'name_en');
        $suppNames  = $this->pluckNames(SuppliersData::class, 'name_ar', $r->get('supplier_ids'), 'name_en');

        $statusLabels = [];
        if ($statusValues = $r->get('status')) {
            $statusValues = is_array($statusValues) ? $statusValues : explode(',', $statusValues);
            foreach ($statusValues as $sv) $statusLabels[] = $this->statusLabel($sv);
        }

        $additionSourceLabel = null;
        if ($src = $r->get('addition_source')) $additionSourceLabel = $this->additionSourceLabel($src);

        return [
            'title'           => trans('assets.master_movement_title'),
            'generated_at'    => Carbon::now()->format('Y-m-d H:i'),
            'purchase_from'   => $r->get('purchase_from'),
            'purchase_to'     => $r->get('purchase_to'),
            'category_names'  => $catNames,
            'brand_names'     => $brdNames,
            'model_names'     => $modNames,
            'location_names'  => $locNames,
            'store_names'     => $storeNames,
            'supplier_names'  => $suppNames,
            'status_labels'   => $statusLabels,
            'addition_source' => $additionSourceLabel,
            'cost_min'        => $r->get('cost_min'),
            'cost_max'        => $r->get('cost_max'),
            'nbv_min'         => $r->get('nbv_min'),
            'nbv_max'         => $r->get('nbv_max'),
            'qty_min'         => $r->get('qty_min'),
            'qty_max'         => $r->get('qty_max'),
            'count'           => $rows->count(),
        ];
    }

    private function pluckNames($modelClass, $mainCol, $ids, $altCol=null): array
    {
        if (!$ids) return [];
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        $q = $modelClass::whereIn('id', $ids)->limit(500);
        $list = $q->get([$mainCol, $altCol])->map(function($x) use ($mainCol, $altCol){
            return $x->{$mainCol} ?: ($altCol ? $x->{$altCol} : null);
        })->filter()->values()->toArray();
        return $list;
    }

    protected function exportExcel($rows, $meta)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('assets.col_code'),
            trans('assets.col_name'),
            trans('assets.col_category'),
            trans('assets.col_brand'),
            trans('assets.col_model'),
            trans('assets.col_serial'),
            trans('assets.col_location'),
            trans('assets.col_custodian'),
            trans('assets.col_store'),
            trans('assets.col_supplier'),
            trans('assets.col_purchase_date'),
            trans('assets.col_cost'),
            trans('assets.col_status'),
            trans('assets.col_addition_source'),
            trans('assets.col_last_movement'),
            trans('assets.col_qty_total'),
            trans('assets.col_qty_assigned'),
            trans('assets.col_qty_available'),
            trans('assets.col_nbv'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A'.$rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['code'], $r['name'], $r['category'], $r['brand'], $r['model'],
                $r['serial'], $r['location'], $r['custodian'], $r['store'], $r['supplier'],
                $r['purchase_date'], number_format((float)$r['cost'],2),
                $r['status'], $r['addition_source'], $r['last_movement'],
                $r['qty_total'], $r['qty_assigned'], $r['qty_available'],
                number_format((float)$r['nbv'],2),
            ]], NULL, 'A'.$rowIndex);
            $rowIndex++;
        }

        foreach (range('A','T') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Master_Movement_'.now()->format('Ymd_His').'.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /** ===== Helpers ===== */
    private function arr($val): array
    {
        if (is_null($val) || $val==='') return [];
        if (is_array($val)) return array_filter($val, fn($x)=>$x!=='' && $x!==null);
        return array_filter(explode(',', $val), fn($x)=>$x!=='');
    }

    private function statusLabel(?string $status): string
    {
        $map = [
            'in_service'  => trans('assets.status_in_service'),
            'maintenance' => trans('assets.status_maintenance'),
            'retired'     => trans('assets.status_retired'),
            'scrapped'    => trans('assets.status_scrapped'),
        ];
        return $map[$status] ?? ($status ?? '—');
    }

    private function additionSourceLabel(?string $src): string
    {
        if ($src === 'conversion') return trans('assets.source_conversion');
        if ($src === 'direct')     return trans('assets.source_direct');
        return '—';
    }
}
