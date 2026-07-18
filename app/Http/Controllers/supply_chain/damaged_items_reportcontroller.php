<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

// Models
use App\Models\supply_chain\productcategory as Category;
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\unit as Unit;
use App\Services\SupplyChain\UnitConversionService;

class damaged_items_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // ===== Print =====
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.damaged_items_report.print', compact('rows', 'meta'));
        }

        // ===== Export Excel =====
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // ===== AJAX (DataTables) =====
        if ($r->wantsJson() || $r->ajax()) {
            if (!$this->hasAnyFilter($r)) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $q = $this->baseQuery($r);

            // بحث عام
            $search = trim((string)($r->input('search.value') ?? $r->get('q')));
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('r.request_number', 'like', "%{$search}%")
                      ->orWhere('s.manual_code', 'like', "%{$search}%")
                      ->orWhere('s.name_ar', 'like', "%{$search}%")
                      ->orWhere('s.name_en', 'like', "%{$search}%")
                      ->orWhere('p.system_code', 'like', "%{$search}%")
                      ->orWhere('p.name_ar', 'like', "%{$search}%")
                      ->orWhere('p.name_en', 'like', "%{$search}%")
                      ->orWhere('c.name_ar', 'like', "%{$search}%")
                      ->orWhere('c.name_en', 'like', "%{$search}%")
                      ->orWhere('r.reason', 'like', "%{$search}%")
                      ->orWhere('r.status', 'like', "%{$search}%")
                      ->orWhere('u.name', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 8)); // افتراضياً تاريخ الطلب
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0  => 'ri.id',                 // مسلسل
                1  => 'r.request_number',
                2  => 's.manual_code',
                3  => 's.name_ar',
                4  => 'p.system_code',
                5  => 'p.name_ar',
                6  => 'c.name_ar',
                7  => null,                    // unit/qty (محسوبة/عرض)
                8  => 'r.request_date',
                9  => 'r.status',
                10 => 'r.created_at',
                11 => 'u.name',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'r.request_date';
            if ($orderBy) $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();
            [$data] = $this->mapRows($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // ===== HTML (تحميل القوائم) =====
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

        $categories = Category::select('id','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($c)=>['id'=>$c->id,'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id)])->values();

        // حالات الطلب (distinct)
        $statuses = DB::table('damaged_item_requests')->select('status')->distinct()->pluck('status')->filter()->values();

        return view('supply_chain.reports.damaged_items_report.index', [
            'stores'     => $stores,
            'products'   => $products,
            'categories' => $categories,
            'statuses'   => $statuses,
        ]);
    }

    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id') ||
            $r->filled('products') ||
            $r->filled('categories') ||
            $r->filled('statuses') ||
            $r->filled('date_from') ||
            $r->filled('date_to') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي (Query Builder بدون سكوبات) */
    private function baseQuery(Request $r)
    {
        $q = DB::table('damaged_item_request_items as ri')
            ->join('damaged_item_requests as r', 'r.id', '=', 'ri.request_id')
            ->leftJoin('products_data as p', 'p.id', '=', 'ri.product_id')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
            ->leftJoin('store_main_date as s', 's.id', '=', 'r.store_id')
            ->leftJoin('units as un', 'un.id', '=', 'ri.unit_id')
            ->leftJoin('users as u', 'u.id', '=', 'r.user_id');

        // SoftDelete for requests (إن وُجد)
        if (Schema::hasColumn('damaged_item_requests', 'deleted_at')) {
            $q->whereNull('r.deleted_at');
        }

        $q->select([
            'ri.id as ri_id', 'ri.request_id', 'ri.product_id', 'ri.unit_id', 'ri.quantity', 'ri.notes',
            'r.request_number', 'r.store_id', 'r.request_date', 'r.reason', 'r.status', 'r.created_at as r_created_at',
            'p.system_code as p_code', 'p.name_ar as p_name_ar', 'p.name_en as p_name_en',
            'c.name_ar as c_name_ar', 'c.name_en as c_name_en',
            's.manual_code as s_manual_code', 's.name_ar as s_name_ar', 's.name_en as s_name_en',
            'un.name_ar as un_name_ar', 'un.name_en as un_name_en',
            'u.name as u_name',
        ]);

        // فلاتر
        if ($ids = $this->arrify($r->get('store_id'))) {
            $q->whereIn('r.store_id', $ids);
        }
        if ($ids = $this->arrify($r->get('products'))) {
            $q->whereIn('ri.product_id', $ids);
        }
        if ($ids = $this->arrify($r->get('categories'))) {
            $q->whereIn('p.product_category_id', $ids);
        }
        if ($st = $this->arrify($r->get('statuses'))) {
            $q->whereIn('r.status', $st);
        }
        if ($r->filled('date_from')) {
            $q->whereDate('r.request_date', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('r.request_date', '<=', $r->get('date_to'));
        }

        return $q;
    }

    /** تحويل النتائج لصفوف العرض مع التحويل لأصغر وحدة + أسعار أساسية */
    private function mapRows($rows): array
    {
        $svc = app(UnitConversionService::class);

        $baseUnitNameCache = [];   // [product_id => unit_name]
        $basePriceCache    = [];   // [product_id => ['sale'=>?, 'cost'=>?]]
        $factorCache       = [];   // ["pid:fromUnit" => float]

        $data = [];
        foreach ($rows as $row) {
            $pid = (int)($row->product_id ?? 0);
            $fromUnit = (int)($row->unit_id ?? 0);

            // اسم أصغر وحدة
            if ($pid && !array_key_exists($pid, $baseUnitNameCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $baseUnitNameCache[$pid] = $baseId ? (optional(Unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) {
                    $baseUnitNameCache[$pid] = '—';
                }
            }

            // عامل تحويل 1 من وحدة السطر إلى أصغر وحدة (لتحويل الكمية)
            $factorKey = $pid.':'.$fromUnit;
            if (!isset($factorCache[$factorKey])) {
                try {
                    $factorCache[$factorKey] = $pid && $fromUnit ? max(1e-9, $svc->toBase($pid, 1.0, $fromUnit)) : 1.0;
                } catch (\Throwable $e) {
                    $factorCache[$factorKey] = 1.0;
                }
            }
            $qtyBase = (float)$row->quantity * (float)$factorCache[$factorKey];

            // أسعار أساسية (من جدول وحدات الصنف إن وُجدت)
            if ($pid && !array_key_exists($pid, $basePriceCache)) {
                try {
                    $units = $svc->unitsForProduct($pid, 'name_ar');
                    $base  = $units->firstWhere('is_base', true);
                    $basePriceCache[$pid] = [
                        'sale' => $base['sale_per_unit'] ?? null,
                        'cost' => $base['cost_per_unit'] ?? null,
                    ];
                } catch (\Throwable $e) {
                    $basePriceCache[$pid] = ['sale'=>null,'cost'=>null];
                }
            }

            $prodName  = trim(($row->p_name_ar ?? '') . ' / ' . ($row->p_name_en ?? ''));
            $catName   = trim(($row->c_name_ar ?? '') . ' / ' . ($row->c_name_en ?? ''));
            $storeName = trim(($row->s_name_ar ?? '') . ' / ' . ($row->s_name_en ?? ''));

            $data[] = [
                // مسلسل يحسب من الداتا تيبل
                'request_number'   => $row->request_number ?? '—',
                'store_manual_code'=> $row->s_manual_code ?? '—',
                'store_name'       => $storeName ?: '—',
                'product_code'     => $row->p_code ?? '—',
                'product_name'     => $prodName ?: '—',
                'category_name'    => ($catName !== ' / ') ? $catName : '—',
                'unit_name'        => $baseUnitNameCache[$pid] ?? ($row->un_name_ar ?? '—'),
                'quantity'         => number_format($qtyBase, 2),
                'reason'           => $row->reason ?: '—',
                'status'           => $row->status ?: '—',
                'request_date'     => $row->request_date ? Carbon::parse($row->request_date)->format('Y-m-d') : '—',
                'created_at'       => $row->r_created_at ? Carbon::parse($row->r_created_at)->format('Y-m-d H:i') : '—',
                'created_by'       => $row->u_name ?: '—',
                'notes'            => $row->notes ?: '—',
                // أسعار للعرض (اختياري)
                'sale_price'       => is_null($basePriceCache[$pid]['sale']) ? '—' : number_format((float)$basePriceCache[$pid]['sale'], 2),
                'cost_price'       => is_null($basePriceCache[$pid]['cost']) ? '—' : number_format((float)$basePriceCache[$pid]['cost'], 2),
            ];
        }

        return [$data, count($data)];
    }

    /** للطباعة/الإكسل: اجلب كل النتائج (حد أمان كبير) */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [[], $this->buildMeta($r, [])];
        }

        $q = $this->baseQuery($r)->orderBy('r.request_date','desc');
        $rows = $q->limit(200000)->get();
        [$mapped] = $this->mapRows($rows);

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    private function buildMeta(Request $r, array $rows): array
    {
        $storeNames = [];
        if ($ids = $this->arrify($r->get('store_id'))) {
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        $productNames = [];
        if ($ids = $this->arrify($r->get('products'))) {
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }
        $categoryNames = [];
        if ($ids = $this->arrify($r->get('categories'))) {
            $categoryNames = Category::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }
        $statusNames = $this->arrify($r->get('statuses'));

        // لا نستخدم أي partials هنا لتفادي خطأ "View not found"
        return [
            'title'         => trans('supply_chain_trans.damaged_items_report_title'),
            'generated_at'  => Carbon::now()->format('Y-m-d H:i'),
            'date_from'     => $r->get('date_from'),
            'date_to'       => $r->get('date_to'),
            'store_names'   => $storeNames,
            'product_names' => $productNames,
            'category_names'=> $categoryNames,
            'status_names'  => $statusNames,
            'count'         => count($rows),
        ];
    }

    /** تصدير Excel */
    private function exportExcel(array $rows, array $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_request_number'),
            trans('supply_chain_trans.table_store_manual_code'),
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_product_code'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_category_name'),
            trans('supply_chain_trans.table_unit_small'),
            trans('supply_chain_trans.table_quantity'),
            trans('supply_chain_trans.table_reason'),
            trans('supply_chain_trans.table_status'),
            trans('supply_chain_trans.table_request_date'),
            trans('supply_chain_trans.table_created_at'),
            trans('supply_chain_trans.table_created_by'),
            trans('supply_chain_trans.table_notes'),
            trans('supply_chain_trans.table_sale_price'),
            trans('supply_chain_trans.table_cost_price'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['request_number'],
                $r['store_manual_code'],
                $r['store_name'],
                $r['product_code'],
                $r['product_name'],
                $r['category_name'],
                $r['unit_name'],
                $r['quantity'],
                $r['reason'],
                $r['status'],
                $r['request_date'],
                $r['created_at'],
                $r['created_by'],
                $r['notes'],
                $r['sale_price'],
                $r['cost_price'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','P') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Damaged_Items_Report_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function arrify($v): array
    {
        if (is_null($v) || $v === '') return [];
        return is_array($v) ? array_values(array_filter($v, fn($x)=>$x!=='')) : [$v];
    }
}
