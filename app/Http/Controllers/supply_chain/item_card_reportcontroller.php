<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Models
use App\models\supply_chain\storemovement as Movement;
use App\Models\supply_chain\productstorebalance as Balance;
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\storemaindata as Store;
use App\models\supply_chain\unit as Unit;
use App\models\User as UserModel;

class item_card_reportcontroller extends Controller
{
    /** أنواع الحركات الداخلة التي تُعد "أول إضافة" */
    private function inboundTypes(): array
    {
        return ['direct_addition','normal_addition','return','opening_balance','inventory','adjustment_plus','transfer'];
    }

    public function index(Request $r)
    {
        $action  = $r->get('action');   // print | export_excel | (ajax|html)
        $dataset = $r->get('dataset');  // kpis | batches | (ledger default)

        /* =======================
         *  1) Print / Excel
         * ======================= */
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.item_card_report.print', compact('rows', 'meta'));
        }
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        /* =======================
         *  2) JSON datasets
         * ======================= */
        if ($r->wantsJson() || $r->ajax()) {
            if ($dataset === 'kpis')    return $this->kpisJson($r);
            if ($dataset === 'batches') return $this->batchesJson($r);
            // otherwise: ledger (DataTables server-side)
            return $this->ledgerJson($r);
        }

        /* =======================
         *  3) HTML (الواجهة)
         * ======================= */
        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        $products = Product::select('id','system_code','name_ar','name_en')
            ->orderBy('name_ar')->limit(4000)->get()
            ->map(fn($p)=>[
                'id'=>$p->id,
                'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ($p->system_code ? ' - '.$p->system_code : ''),
            ])->values();

        return view('supply_chain.reports.item_card_report.index', [
            'stores'   => $stores,
            'products' => $products,
        ]);
    }

    /** ========== Ledger (DataTables) ========== */
    private function ledgerJson(Request $r)
    {
        $q = $this->baseQuery($r);

        // بحث عام
        $search = trim((string)($r->input('search.value') ?? $r->get('q')));
        if ($search !== '') {
            $q->where(function($w) use ($search) {
                $w->where('p.system_code', 'like', "%{$search}%")
                  ->orWhere('p.name_ar', 'like', "%{$search}%")
                  ->orWhere('p.name_en', 'like', "%{$search}%")
                  ->orWhere('s.name_ar', 'like', "%{$search}%")
                  ->orWhere('s.name_en', 'like', "%{$search}%")
                  ->orWhere('m.movement_type', 'like', "%{$search}%")
                  ->orWhere('m.reference_no', 'like', "%{$search}%")
                  ->orWhere('m.movement_number', 'like', "%{$search}%")
                  ->orWhere('u.name', 'like', "%{$search}%")
                  ->orWhere('m.batch_no', 'like', "%{$search}%");
            });
        }

        $totalFiltered = (clone $q)->count();

        // ترتيب
        $orderColumnIndex = (int)($r->input('order.0.column', 6));
        $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [
            0  => 'm.id',
            1  => 'p.system_code',
            2  => 'p.name_ar',
            3  => 'm.reference_no',
            4  => 'benef_store_name',
            5  => 'm.movement_type',
            6  => 'm.movement_date',
            7  => 'm.sale_price',
            8  => 'm.cost_price',
            9  => 'un.name_ar',
            10 => 'm.product_store_balance',
            11 => 'm.batch_no',
            12 => 'm.created_at',
            13 => 'm.expiry_date',
            14 => 'u.name',
        ];
        $orderBy = $orderMap[$orderColumnIndex] ?? 'm.movement_date';
        $q->orderBy($orderBy, $orderDir);

        // تقسيم الصفحات
        $start  = (int)$r->get('start', 0);
        $length = (int)$r->get('length', 10);
        if ($length < 1 || $length > 1000) $length = 10;

        $rows = $q->skip($start)->take($length)->get();

        // أول إضافة لكل منتج
        $productIds = $rows->pluck('product_id')->unique()->values()->all();
        $minAdd = $this->firstAdditionsForProducts($productIds);

        // تجهيز الرد
        $data = [];
        foreach ($rows as $row) {
            $productName = trim(($row->p_name_ar ?? '') . ' / ' . ($row->p_name_en ?? ''));
            $unitName    = trim(($row->un_name_ar ?? '') . ' / ' . ($row->un_name_en ?? ''));
            $benefStore  = $row->benef_store_name ?: '—';
            $firstAdd    = $minAdd[$row->product_id] ?? null;

            $data[] = [
                // 0 مسلسل يحسب بالواجهة
                'product_code'      => $row->p_system_code ?? '—',
                'product_name'      => $productName ?: '—',
                'beneficiary_code'  => $row->reference_no ?: '—',
                'beneficiary_store' => $benefStore,
                'movement_type'     => trans('supply_chain_trans.movements_type.' . $row->movement_type, [], app()->getLocale()),
                'movement_date'     => $row->movement_date ? Carbon::parse($row->movement_date)->format('Y-m-d') : '—',
                'sale_price'        => is_null($row->sale_price) ? '—' : number_format((float)$row->sale_price, 2),
                'cost_price'        => is_null($row->cost_price) ? '—' : number_format((float)$row->cost_price, 2),
                'unit_name'         => $unitName ?: '—',
                'current_balance'   => is_null($row->product_store_balance) ? '—' : number_format((float)$row->product_store_balance, 2),
                'batch_no'          => $row->batch_no ?: '—',
                'first_addition_at' => $firstAdd ? Carbon::parse($firstAdd)->format('Y-m-d') : '—',
                'expiry_date'       => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('Y-m-d') : '—',
                'user_name'         => $row->u_name ?: '—',
            ];
        }

        return response()->json([
            'draw'            => intval($r->get('draw')),
            'recordsTotal'    => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    /** ========== KPIs JSON ========== */
private function kpisJson(Request $r)
{
    // ابدأ من نفس الفلاتر لكن استبدل الأعمدة المختارة بأعمدة Aggregates فقط
    $tot = (clone $this->baseQuery($r))
        ->select([
            DB::raw("SUM(CASE WHEN m.quantity > 0 THEN m.quantity ELSE 0 END) AS qty_in"),
            DB::raw("SUM(CASE WHEN m.quantity < 0 THEN ABS(m.quantity) ELSE 0 END) AS qty_out"),
            DB::raw("SUM(CASE WHEN m.quantity > 0 THEN m.quantity * IFNULL(m.cost_price,0) ELSE 0 END) AS val_in"),
            DB::raw("SUM(CASE WHEN m.quantity < 0 THEN ABS(m.quantity) * IFNULL(m.cost_price,0) ELSE 0 END) AS val_out"),
            DB::raw("MIN(CASE WHEN m.expiry_date >= CURDATE() THEN m.expiry_date ELSE NULL END) AS nearest_expiry"),
            DB::raw("SUM(CASE WHEN m.expiry_date IS NOT NULL AND m.expiry_date < CURDATE() THEN 1 ELSE 0 END) AS expired_batches"),
            DB::raw("COUNT(DISTINCT m.batch_no) AS batch_count"),
        ])
        ->first();

    $qty_now = (float)($tot->qty_in ?? 0) - (float)($tot->qty_out ?? 0);
    $val_now = (float)($tot->val_in ?? 0) - (float)($tot->val_out ?? 0);
    $avg_now = $qty_now > 0 ? $val_now / $qty_now : null;

    // آخر تكلفة واردة (اختيار عمود واحد فقط لتفادي أي مشاكل)
    $last_in = (clone $this->baseQuery($r))
        ->select('m.cost_price')
        ->where('m.quantity', '>', 0)
        ->orderBy('m.movement_date', 'desc')
        ->orderBy('m.id', 'desc')
        ->value('m.cost_price');

    return response()->json([
        'qty_now'        => round($qty_now, 2),
        'val_now'        => round($val_now, 2),
        'avg_cost'       => is_null($avg_now) ? null : round($avg_now, 4),
        'last_cost'      => is_null($last_in) ? null : round((float)$last_in, 4),
        'qty_in'         => round((float)($tot->qty_in ?? 0), 2),
        'qty_out'        => round((float)($tot->qty_out ?? 0), 2),
        'nearest_expiry' => $tot->nearest_expiry ? \Illuminate\Support\Carbon::parse($tot->nearest_expiry)->format('Y-m-d') : null,
        'expired_batches'=> (int)($tot->expired_batches ?? 0),
        'batch_count'    => (int)($tot->batch_count ?? 0),
    ]);
}


    /** ========== Batches Summary JSON ========== */
private function batchesJson(Request $r)
{
    $qb = (clone $this->baseQuery($r));

    // مهم: استخدم select([...]) عشان "تستبدل" أي m.* مختارة من baseQuery
    $rows = $qb->select([
            DB::raw('m.store_id'),
            DB::raw('COALESCE(s.name_ar, s.name_en) AS store_name'),
            DB::raw('m.batch_no'),
            DB::raw('m.expiry_date'),
            DB::raw('SUM(m.quantity) AS qty'),
            DB::raw('SUM(m.quantity * IFNULL(m.cost_price,0)) AS value'),
            DB::raw('MAX(m.movement_date) AS last_move'),
        ])
        // استخدم نفس التعبير في GROUP BY بدل alias "store_name" لتفادي 1055
        ->groupBy(
            'm.store_id',
            DB::raw('COALESCE(s.name_ar, s.name_en)'),
            'm.batch_no',
            'm.expiry_date'
        )
        // والترتيب كذلك يفضل يكون على التعبير نفسه
        ->orderByRaw('COALESCE(s.name_ar, s.name_en) ASC')
        ->orderBy('m.batch_no', 'asc')
        ->get();

    $today = \Illuminate\Support\Carbon::today();
    $data = $rows->map(function($r) use ($today){
        $status = 'ok';
        if (!empty($r->expiry_date)) {
            $exp = \Illuminate\Support\Carbon::parse($r->expiry_date);
            if ($exp->lt($today)) $status = 'expired';
            elseif ($exp->diffInDays($today, false) >= -30) $status = 'near';
        }
        return [
            'store_name'  => $r->store_name ?: '—',
            'batch_no'    => $r->batch_no ?: '—',
            'expiry_date' => $r->expiry_date ? \Illuminate\Support\Carbon::parse($r->expiry_date)->format('Y-m-d') : '—',
            'qty'         => number_format((float)$r->qty, 2),
            'value'       => number_format((float)$r->value, 2),
            'last_move'   => $r->last_move ? \Illuminate\Support\Carbon::parse($r->last_move)->format('Y-m-d') : '—',
            'status'      => $status,
        ];
    })->values()->all();

    return response()->json(['data' => $data]);
}


    /** الاستعلام الأساسي مع تعطيل SoftDeletes وإضافة whereNull على alias */
    private function baseQuery(Request $r)
    {
        $q = Movement::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('store_movements as m')
            ->whereNull('m.deleted_at') // مهم حتى مع alias
            ->leftJoin('products_data as p', 'p.id', '=', 'm.product_id')
            ->leftJoin('store_main_date as s', 's.id', '=', 'm.store_id')
            ->leftJoin('units as un', 'un.id', '=', 'm.units_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
            // self-join لإظهار "المخزن المستفيد" في التحويلات
            ->leftJoin('store_movements as m2', function($j){
                $j->on('m2.reference_no', '=', 'm.reference_no')
                  ->on('m2.product_id', '=', 'm.product_id')
                  ->whereColumn('m2.store_id', '!=', 'm.store_id')
                  ->whereNull('m2.deleted_at');
            })
            ->leftJoin('store_main_date as s2', 's2.id', '=', 'm2.store_id')
            ->select([
                'm.*',
                'p.id as product_id',
                'p.system_code as p_system_code',
                'p.name_ar as p_name_ar',
                'p.name_en as p_name_en',
                's.name_ar as s_name_ar',
                's.name_en as s_name_en',
                'un.name_ar as un_name_ar',
                'un.name_en as un_name_en',
                'u.name as u_name',
                DB::raw("COALESCE(s2.name_ar, s2.name_en) as benef_store_name"),
            ]);

        // فلاتر
        $stores = $r->get('store_id');
        if (is_array($stores) && count($stores)) $q->whereIn('m.store_id', $stores);
        elseif (!empty($stores)) $q->where('m.store_id', $stores);

        $products = $r->get('products');
        if (is_array($products) && count($products)) $q->whereIn('m.product_id', $products);

        if ($r->filled('date_from')) $q->whereDate('m.movement_date', '>=', $r->get('date_from'));
        if ($r->filled('date_to'))   $q->whereDate('m.movement_date', '<=', $r->get('date_to'));

        return $q;
    }

    /** أول إضافة لكل منتج (مع احترام soft delete بشكل يدوي) */
    private function firstAdditionsForProducts(array $productIds): array
    {
        if (empty($productIds)) return [];

        $rows = Movement::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->whereNull('deleted_at')
            ->select('product_id', DB::raw('MIN(movement_date) as first_add'))
            ->whereIn('product_id', $productIds)
            ->whereIn('movement_type', $this->inboundTypes())
            ->where('quantity', '>', 0)
            ->groupBy('product_id')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->product_id] = $r->first_add;
        }
        return $map;
    }

    /** تجهيز كل الصفوف للطباعة/الإكسل */
    private function fetchAllForReport(Request $r): array
    {
        $q = $this->baseQuery($r)->orderBy('m.movement_date', 'asc')->orderBy('m.id','asc');
        $rows = $q->limit(200000)->get();

        $productIds = $rows->pluck('product_id')->unique()->values()->all();
        $minAdd = $this->firstAdditionsForProducts($productIds);

        $mapped = $rows->map(function($row) use ($minAdd) {
            $productName = trim(($row->p_name_ar ?? '') . ' / ' . ($row->p_name_en ?? ''));
            $unitName    = trim(($row->un_name_ar ?? '') . ' / ' . ($row->un_name_en ?? ''));
            $benefStore  = $row->benef_store_name ?: '—';
            $firstAdd    = $minAdd[$row->product_id] ?? null;

            return [
                'product_code'      => $row->p_system_code ?? '—',
                'product_name'      => $productName ?: '—',
                'beneficiary_code'  => $row->reference_no ?: '—',
                'beneficiary_store' => $benefStore,
                'movement_type'     => trans('supply_chain_trans.movements_type.' . $row->movement_type, [], app()->getLocale()),
                'movement_date'     => $row->movement_date ? Carbon::parse($row->movement_date)->format('Y-m-d') : '—',
                'sale_price'        => is_null($row->sale_price) ? '—' : number_format((float)$row->sale_price, 2),
                'cost_price'        => is_null($row->cost_price) ? '—' : number_format((float)$row->cost_price, 2),
                'unit_name'         => $unitName ?: '—',
                'current_balance'   => is_null($row->product_store_balance) ? '—' : number_format((float)$row->product_store_balance, 2),
                'batch_no'          => $row->batch_no ?: '—',
                'first_addition_at' => $firstAdd ? Carbon::parse($firstAdd)->format('Y-m-d') : '—',
                'expiry_date'       => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('Y-m-d') : '—',
                'user_name'         => $row->u_name ?: '—',
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped->toArray())];
    }

    /** معلومات الهيدر للطباعة/الإكسل */
    private function buildMeta(Request $r, array $rows)
    {
        $storeNames = [];
        if ($stores = $r->get('store_id')) {
            $ids = is_array($stores)? $stores : [$stores];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        $productNames = [];
        if ($prods = $r->get('products')) {
            $ids = is_array($prods)? $prods : [$prods];
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'        => trans('supply_chain_trans.item_card_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'store_names'  => $storeNames,
            'product_names'=> $productNames,
            'count'        => count($rows),
        ];
    }

    /** تصدير Excel */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_product_code'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_beneficiary_code'),
            trans('supply_chain_trans.table_beneficiary_store'),
            trans('supply_chain_trans.table_movement_type'),
            trans('supply_chain_trans.table_movement_date'),
            trans('supply_chain_trans.table_sale_price'),
            trans('supply_chain_trans.table_cost_price'),
            trans('supply_chain_trans.table_unit'),
            trans('supply_chain_trans.table_ledger_balance'),
            trans('supply_chain_trans.table_batch_no'),
            trans('supply_chain_trans.table_first_addition_at'),
            trans('supply_chain_trans.table_expiry_date'),
            trans('supply_chain_trans.table_user'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['product_code'],
                $r['product_name'],
                $r['beneficiary_code'],
                $r['beneficiary_store'],
                $r['movement_type'],
                $r['movement_date'],
                $r['sale_price'],
                $r['cost_price'],
                $r['unit_name'],
                $r['current_balance'],
                $r['batch_no'],
                $r['first_addition_at'],
                $r['expiry_date'],
                $r['user_name'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','N') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Item_Card_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
