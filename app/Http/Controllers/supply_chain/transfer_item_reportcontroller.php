<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use App\models\supply_chain\storetransfer as Transfer;
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as Category;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\unit as Unit;
use App\models\supply_chain\storemovement as Movement;
use App\Services\SupplyChain\UnitConversionService;

class transfer_item_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.transfer_item_report.print', compact('rows', 'meta'));
        }

        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // AJAX (DataTables)
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
                $q->where(function($w) use ($search) {
                    $w->where('p.system_code', 'like', "%{$search}%")
                      ->orWhere('p.name_ar', 'like', "%{$search}%")
                      ->orWhere('p.name_en', 'like', "%{$search}%")
                      ->orWhere('c.name_ar', 'like', "%{$search}%")
                      ->orWhere('c.name_en', 'like', "%{$search}%")
                      ->orWhere('t.batch_no', 'like', "%{$search}%")
                      ->orWhere('s1.name_ar', 'like', "%{$search}%")
                      ->orWhere('s2.name_ar', 'like', "%{$search}%")
                      ->orWhere('t.transfer_number', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 11)); // transfer_date
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0  => 't.id',
                1  => 'p.system_code',
                2  => 'p.name_ar',
                3  => 'c.name_ar',
                4  => null, // sale_price (محسوب)
                5  => null, // cost_price (محسوب)
                6  => 't.batch_no',
                7  => 'un.name_ar', // الوحدة (اسم الوحدة المنفذة، العرض سيكون أصغر وحدة)
                8  => 't.quantity',
                9  => 's1.name_ar',
                10 => 's2.name_ar',
                11 => 't.transfer_date',
                12 => 'u.name',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 't.transfer_date';
            if ($orderBy) $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();

            // تحويل وعرض بالوحدة الأصغر + أسعار
            [$data] = $this->mapRowsWithComputed($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // فلاتر (قوائم)
        $products = Product::select('id','system_code','name_ar','name_en')
            ->orderBy('name_ar')->limit(5000)->get()
            ->map(fn($p)=>[
                'id'=>$p->id,
                'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ($p->system_code ? ' - '.$p->system_code : ''),
            ])->values();

        $categories = Category::select('id','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($c)=>['id'=>$c->id,'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id)])->values();

        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        return view('supply_chain.reports.transfer_item_report.index', [
            'products'   => $products,
            'categories' => $categories,
            'stores'     => $stores,
        ]);
    }

    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('products') ||
            $r->filled('categories') ||
            $r->filled('store_from') ||
            $r->filled('store_to') ||
            $r->filled('date_from') ||
            $r->filled('date_to') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي عبر DB::table لتجنّب مشاكل SoftDeletes مع alias */
    private function baseQuery(Request $r)
    {
        $q = DB::table('store_transfers as t')
            ->leftJoin('products_data as p', 'p.id', '=', 't.product_id')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
            ->leftJoin('units as un', 'un.id', '=', 't.units_id')
            ->leftJoin('store_main_date as s1', 's1.id', '=', 't.store_from_id')
            ->leftJoin('store_main_date as s2', 's2.id', '=', 't.store_to_id')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select([
                't.id','t.transfer_number','t.store_from_id','t.store_to_id','t.transfer_date',
                't.product_id','t.units_id','t.quantity','t.batch_no','t.expiry_date','t.user_id','t.notes',
                'p.system_code as p_code','p.name_ar as p_name_ar','p.name_en as p_name_en','p.product_category_id',
                'c.name_ar as c_name_ar','c.name_en as c_name_en',
                'un.name_ar as un_name_ar','un.name_en as un_name_en',
                's1.name_ar as s1_name_ar','s1.name_en as s1_name_en',
                's2.name_ar as s2_name_ar','s2.name_en as s2_name_en',
                'u.name as u_name',
            ]);

        // فلاتر
        if ($ids = $this->arrify($r->get('products')))   $q->whereIn('t.product_id', $ids);
        if ($cats = $this->arrify($r->get('categories'))) $q->whereIn('p.product_category_id', $cats);
        if ($froms = $this->arrify($r->get('store_from'))) $q->whereIn('t.store_from_id', $froms);
        if ($tos   = $this->arrify($r->get('store_to')))   $q->whereIn('t.store_to_id', $tos);

        if ($r->filled('date_from')) $q->whereDate('t.transfer_date', '>=', $r->get('date_from'));
        if ($r->filled('date_to'))   $q->whereDate('t.transfer_date', '<=', $r->get('date_to'));

        return $q;
    }

    /** تحويل النتائج: أسعار + تحويل كمية إلى الأصغر + أسماء مرتبة */
    private function mapRowsWithComputed($rows): array
    {
        $svc = app(UnitConversionService::class);

        $priceCache = []; // [product_id => ['sale'=>?, 'cost'=>?]]
        $unitCache  = []; // [product_id => 'unit_name']

        $data = [];
        foreach ($rows as $row) {
            $pid = (int)$row->product_id;

            // اسم أصغر وحدة
            if (!array_key_exists($pid, $unitCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $unitCache[$pid] = $baseId ? (optional(Unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) {
                    $unitCache[$pid] = '—';
                }
            }

            // الأسعار من جدول الوحدات (fallback لآخر حركة)
            if (!array_key_exists($pid, $priceCache)) {
                $priceCache[$pid] = $this->getBasePricesForProduct($pid, $svc);
            }

            // تحويل الكمية إلى أصغر وحدة
            $qtyBase = 0.0;
            try {
                $qtyBase = $svc->toBase($pid, (float)$row->quantity, (int)$row->units_id);
            } catch (\Throwable $e) {}

            $productName  = trim(($row->p_name_ar ?? '') . ' / ' . ($row->p_name_en ?? ''));
            $categoryName = trim(($row->c_name_ar ?? '') . ' / ' . ($row->c_name_en ?? ''));
            $storeFrom    = trim(($row->s1_name_ar ?? '') . ' / ' . ($row->s1_name_en ?? ''));
            $storeTo      = trim(($row->s2_name_ar ?? '') . ' / ' . ($row->s2_name_en ?? ''));

            $data[] = [
                'product_code' => $row->p_code ?: '—',
                'product_name' => $productName ?: '—',
                'category_name'=> ($categoryName !== ' / ') ? $categoryName : '—',
                'sale_price'   => is_null($priceCache[$pid]['sale']) ? '—' : number_format((float)$priceCache[$pid]['sale'], 2),
                'cost_price'   => is_null($priceCache[$pid]['cost']) ? '—' : number_format((float)$priceCache[$pid]['cost'], 2),
                'batch_no'     => $row->batch_no ?: '—',
                'unit_name'    => $unitCache[$pid] ?? '—',
                'quantity'     => number_format((float)$qtyBase, 2),
                'store_from'   => $storeFrom ?: '—',
                'store_to'     => $storeTo ?: '—',
                'transfer_date'=> $row->transfer_date ? Carbon::parse($row->transfer_date)->format('Y-m-d') : '—',
                'user_name'    => $row->u_name ?: '—',
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

        // fallback: آخر حركة للصنف (أياً كانت)
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

    /** للطباعة/الإكسل */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [[], $this->buildMeta($r, [])];
        }

        $q = $this->baseQuery($r)->orderBy('t.transfer_date','desc');
        $rows = $q->limit(200000)->get();
        [$mapped] = $this->mapRowsWithComputed($rows);
        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    private function buildMeta(Request $r, array $rows)
    {
        $productNames = [];
        if ($prods = $this->arrify($r->get('products'))) {
            $productNames = Product::whereIn('id', $prods)->limit(50)->pluck('name_ar')->toArray();
        }
        $categoryNames = [];
        if ($cats = $this->arrify($r->get('categories'))) {
            $categoryNames = Category::whereIn('id', $cats)->limit(50)->pluck('name_ar')->toArray();
        }
        $storeFromNames = [];
        if ($froms = $this->arrify($r->get('store_from'))) {
            $storeFromNames = Store::whereIn('id', $froms)->pluck('name_ar')->toArray();
        }
        $storeToNames = [];
        if ($tos = $this->arrify($r->get('store_to'))) {
            $storeToNames = Store::whereIn('id', $tos)->pluck('name_ar')->toArray();
        }

        return [
            'title'          => trans('supply_chain_trans.transfer_item_report_title', [], app()->getLocale()) ?? 'تقرير تحويل الأرصدة',
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'date_from'      => $r->get('date_from'),
            'date_to'        => $r->get('date_to'),
            'product_names'  => $productNames,
            'category_names' => $categoryNames,
            'from_names'     => $storeFromNames,
            'to_names'       => $storeToNames,
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
            trans('supply_chain_trans.table_batch_no'),
            trans('supply_chain_trans.table_unit_small'),
            trans('supply_chain_trans.table_quantity'),
            trans('supply_chain_trans.table_store_from'),
            trans('supply_chain_trans.table_store_to'),
            trans('supply_chain_trans.table_transfer_date'),
            trans('supply_chain_trans.table_user'),
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
                $r['batch_no'],
                $r['unit_name'],
                $r['quantity'],
                $r['store_from'],
                $r['store_to'],
                $r['transfer_date'],
                $r['user_name'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Transfer_Report_' . now()->format('Ymd_His') . '.xlsx';

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
