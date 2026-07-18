<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Models
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as Category;
use App\models\supply_chain\storemovement as Movement;
use App\Models\supply_chain\unit as Unit;
use App\Services\SupplyChain\UnitConversionService;

class item_data_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

if ($action === 'print') {
    // هذه القيم من رابط الطباعة (يمكن تغييرها من الواجهة)
    $perPage = min(max((int)$r->get('pp', 5000), 1000), 20000); // الافتراضي 5000, حد أعلى 20000
    $page    = max(1, (int)$r->get('page', 1));

    // زِد مهلة التنفيذ للطباعة فقط (حتى لا تؤثر على باقي الصفحات)
    @ini_set('max_execution_time', '300'); // 5 دقائق
    @set_time_limit(300);

    [$rows, $meta] = $this->fetchPaginatedForPrint($r, $perPage, $page);
    return view('supply_chain.reports.item_data_report.print', compact('rows', 'meta'));
}


        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        if ($r->wantsJson() || $r->ajax()) {
            $q = $this->baseQuery($r);

            // بحث عام
            $search = trim((string)($r->input('search.value') ?? $r->get('q')));
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('p.system_code', 'like', "%{$search}%")
                      ->orWhere('p.name_ar', 'like', "%{$search}%")
                      ->orWhere('p.name_en', 'like', "%{$search}%")
                      ->orWhere('p.description', 'like', "%{$search}%")
                      ->orWhere('c.name_ar', 'like', "%{$search}%")
                      ->orWhere('c.name_en', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 8));
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'p.id',
                1 => 'p.system_code',
                2 => 'p.name_ar',
                3 => 'c.name_ar',
                4 => null, // sale_price (محسوب)
                5 => null, // cost_price (محسوب)
                6 => null, // unit (محسوب)
                7 => 'p.description',
                8 => 'p.created_at',
                9 => 'u.name',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'p.created_at';
            if ($orderBy) $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();

            // تحويل النتائج إلى صفوف العرض
            [$data] = $this->mapRowsWithComputed($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // القوائم (للفلاتر)
        $products = Product::withTrashed()
            ->select('id','system_code','name_ar','name_en')
            ->orderBy('name_ar')->limit(5000)->get()
            ->map(fn($p)=>[
                'id'=>$p->id,
                'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ($p->system_code ? ' - '.$p->system_code : ''),
            ])->values();

        $categories = Category::withTrashed()
            ->select('id','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($c)=>['id'=>$c->id,'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id)])->values();

        return view('supply_chain.reports.item_data_report.index', [
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    /** الاستعلام الأساسي بدون سكوبات (تلافياً لمشكلة alias مع SoftDeletes) */
    private function baseQuery(Request $r)
    {
        $hasCreatedBy = Schema::hasColumn('products_data', 'created_by');

        $q = DB::table('products_data as p')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id');

        if ($hasCreatedBy) {
            $q->leftJoin('users as u', 'u.id', '=', 'p.created_by');
        }

        $select = [
            'p.id','p.system_code','p.name_ar','p.name_en','p.product_category_id',
            'p.description','p.created_at',
            'c.name_ar as c_name_ar','c.name_en as c_name_en',
        ];
        if ($hasCreatedBy) {
            $select[] = 'u.name as u_name';
        }

        $q->select($select);

        // فلاتر
        if ($ids = $this->arrify($r->get('products'))) {
            $q->whereIn('p.id', $ids);
        }
        if ($cats = $this->arrify($r->get('categories'))) {
            $q->whereIn('p.product_category_id', $cats);
        }

        return $q;
    }

    /** يحسب الأسعار/الوحدة الأصغر ويبني صفوف الجدول */
    private function mapRowsWithComputed($rows): array
    {
        $svc = app(UnitConversionService::class);

        $priceCache = []; // [product_id => ['sale'=>?, 'cost'=>?]]
        $unitCache  = []; // [product_id => 'unit_name']

        $data = [];
        foreach ($rows as $row) {
            $pid = (int)$row->id;

            // اسم أصغر وحدة
            if (!array_key_exists($pid, $unitCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $unitCache[$pid] = $baseId ? (optional(Unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) {
                    $unitCache[$pid] = '—';
                }
            }

            // الأسعار (جدول الوحدات ثم fallback لأخر حركة)
            if (!array_key_exists($pid, $priceCache)) {
                $priceCache[$pid] = $this->getBasePricesForProduct($pid, $svc);
            }

            $productName  = trim(($row->name_ar ?? '') . ' / ' . ($row->name_en ?? ''));
            $categoryName = trim(($row->c_name_ar ?? '') . ' / ' . ($row->c_name_en ?? ''));

            $data[] = [
                'product_code' => $row->system_code ?: '—',
                'product_name' => $productName ?: '—',
                'category_name'=> ($categoryName !== ' / ') ? $categoryName : '—',
                'sale_price'   => is_null($priceCache[$pid]['sale']) ? '—' : number_format((float)$priceCache[$pid]['sale'], 2),
                'cost_price'   => is_null($priceCache[$pid]['cost']) ? '—' : number_format((float)$priceCache[$pid]['cost'], 2),
                'unit_name'    => $unitCache[$pid] ?? '—',
                'description'  => $row->description ?: '—',
                'created_at'   => ($row->created_at instanceof \Carbon\Carbon ? $row->created_at : Carbon::parse($row->created_at))->format('Y-m-d H:i'),
                'created_by'   => property_exists($row, 'u_name') ? ($row->u_name ?: '—') : '—',
            ];
        }

        return [$data, count($data)];
    }

    private function getBasePricesForProduct(int $product_id, UnitConversionService $svc): array
    {
        try {
            $units = $svc->unitsForProduct($product_id, 'name_ar');
            $base  = $units->firstWhere('is_base', true);
            if ($base && (!is_null($base['sale_per_unit']) || !is_null($base['cost_per_unit']))) {
                return [
                    'sale' => $base['sale_per_unit'] ?? null,
                    'cost' => $base['cost_per_unit'] ?? null,
                ];
            }
        } catch (\Throwable $e) {}

        $last = Movement::query()
            ->select(['product_id','units_id','sale_price','cost_price','movement_date'])
            ->where('product_id', $product_id)
            ->orderBy('movement_date','desc')
            ->first();

        if ($last) {
            try {
                $unitFactor = max(1e-9, $svc->toBase($product_id, 1.0, (int)$last->units_id));
                $sale = is_null($last->sale_price) ? null : ((float)$last->sale_price / $unitFactor);
                $cost = is_null($last->cost_price) ? null : ((float)$last->cost_price / $unitFactor);
                return ['sale'=>$sale, 'cost'=>$cost];
            } catch (\Throwable $e) {
                return ['sale'=>null, 'cost'=>null];
            }
        }
        return ['sale'=>null, 'cost'=>null];
    }

    private function fetchAllForReport(Request $r): array
    {
        $q = $this->baseQuery($r)->orderBy('p.created_at','desc');
        $rows = $q->limit(200000)->get();
        [$mapped] = $this->mapRowsWithComputed($rows);
        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    private function buildMeta(Request $r, array $rows)
    {
        $productNames = [];
        if ($prods = $this->arrify($r->get('products'))) {
            $productNames = Product::withTrashed()->whereIn('id', $prods)->limit(50)->pluck('name_ar')->toArray();
        }
        $categoryNames = [];
        if ($cats = $this->arrify($r->get('categories'))) {
            $categoryNames = Category::withTrashed()->whereIn('id', $cats)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'          => trans('supply_chain_trans.item_data_report_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'product_names'  => $productNames,
            'category_names' => $categoryNames,
            'count'          => count($rows),
        ];
    }

    private function exportExcel(array $rows, array $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_product_code'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_category_name'),
            trans('supply_chain_trans.table_sale_price'),
            trans('supply_chain_trans.table_cost_price'),
            trans('supply_chain_trans.table_unit_small'),
            trans('supply_chain_trans.table_description'),
            trans('supply_chain_trans.table_created_at'),
            trans('supply_chain_trans.table_created_by'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['product_code'],
                $r['product_name'],
                $r['category_name'],
                $r['sale_price'],
                $r['cost_price'],
                $r['unit_name'],
                $r['description'],
                $r['created_at'],
                $r['created_by'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','I') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Item_Data_Report_' . now()->format('Ymd_His') . '.xlsx';

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
    private function fetchPaginatedForPrint(Request $r, int $perPage, int $page): array
{
    // الاستعلام الأساسي نفسه
    $qBase = $this->baseQuery($r);

    // إجمالي العد (سريع مع الفهارس)
    $total = (clone $qBase)->count();

    // جلب صفحة واحدة فقط
    $offset = ($page - 1) * $perPage;
    $rows   = $qBase->orderBy('p.created_at','desc')->skip($offset)->take($perPage)->get();

    // تحويل للعرض
    [$mapped] = $this->mapRowsWithComputed($rows);

    // ميتا مخصّصة للطباعة المقسّمة
    $meta = $this->buildMeta($r, $mapped);
    $meta['per_page']    = $perPage;
    $meta['page']        = $page;
    $meta['total']       = $total;
    $meta['total_pages'] = (int) max(1, ceil($total / $perPage));

    return [$mapped, $meta];
}

}
