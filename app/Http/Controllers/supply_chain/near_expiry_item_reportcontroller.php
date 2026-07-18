<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\supply_chain\productstorebalance as Balance;
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as Category;
use App\Models\supply_chain\storemaindata as Store;
use App\Services\SupplyChain\UnitConversionService;

class near_expiry_item_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.near_expiry_item_report.print', compact('rows','meta'));
        }

        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

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
                $q->where(function($w) use ($search){
                    $w->where('p.system_code','like',"%{$search}%")
                      ->orWhere('p.name_ar','like',"%{$search}%")
                      ->orWhere('p.name_en','like',"%{$search}%")
                      ->orWhere('c.name_ar','like',"%{$search}%")
                      ->orWhere('c.name_en','like',"%{$search}%")
                      ->orWhere('b.batch_no','like',"%{$search}%")
                      ->orWhere('s.name_ar','like',"%{$search}%")
                      ->orWhere('s.name_en','like',"%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 11)); // expiry_date
            $orderDir = strtolower($r->input('order.0.dir','asc')) === 'desc' ? 'desc' : 'asc';
            $orderMap = [
                0  => 'b.id',
                1  => 'p.system_code',
                2  => 'p.name_ar',
                3  => 'c.name_ar',
                4  => null, // sale_price (محسوب)
                5  => null, // cost_price (محسوب)
                6  => null, // unit (محسوب)
                7  => 'b.batch_no',
                8  => 'b.quantity_in_base_unit',
                9  => 's.name_ar',
                10 => 'first_add.min_date', // تاريخ الإضافة (من أول حركة)
                11 => 'b.expiry_date',
                12 => 'u.name',            // المستخدم المنشئ
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'b.expiry_date';
            if ($orderBy) $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();

            // تحضير صفوف العرض (الأسعار + الوحدة الصغرى)
            [$data] = $this->mapRows($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // القوائم للفلاتر
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
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id) . ($s->manual_code ? ' - '.$s->manual_code : ''),
            ])->values();

        return view('supply_chain.reports.near_expiry_item_report.index', [
            'products'   => $products,
            'categories' => $categories,
            'stores'     => $stores,
        ]);
    }

    private function hasAnyFilter(Request $r): bool
    {
        // نعتبر التقرير صالحًا لو حُددت مدة الأيام (افتراضي 30 بالواجهة) أو أي فلتر آخر
        return (bool)(
            $r->filled('days') ||
            $r->filled('products') ||
            $r->filled('categories') ||
            $r->filled('store_id') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    private function baseQuery(Request $r)
    {
        $today = Carbon::today()->toDateString();
        $days  = (int)($r->get('days', 30));
        if ($days < 1) $days = 30;
        $toDate = Carbon::today()->addDays($days)->toDateString();

        // subquery لأول حركة موجبة (تاريخ الإضافة والمستخدم)
        $smHasDeleted = Schema::hasColumn('store_movements','deleted_at');

        $subMin = DB::table('store_movements as sm1')
            ->select('sm1.product_id','sm1.store_id','sm1.batch_no', DB::raw('MIN(sm1.movement_date) as min_date'))
            ->where('sm1.quantity','>',0);
        if ($smHasDeleted) $subMin->whereNull('sm1.deleted_at');
        $subMin->groupBy('sm1.product_id','sm1.store_id','sm1.batch_no');

        $q = DB::table('product_store_balances as b')
            ->leftJoin('products_data as p','p.id','=','b.product_id')
            ->leftJoin('product_categories as c','c.id','=','p.product_category_id')
            ->leftJoin('store_main_date as s','s.id','=','b.store_id')
            ->leftJoinSub($subMin, 'first_add', function($join){
                $join->on('first_add.product_id','=','b.product_id')
                     ->on('first_add.store_id','=','b.store_id')
                     ->on('first_add.batch_no','=','b.batch_no');
            })
            ->leftJoin('store_movements as m2', function($join){
                $join->on('m2.product_id','=','b.product_id')
                     ->on('m2.store_id','=','b.store_id')
                     ->on('m2.batch_no','=','b.batch_no')
                     ->on('m2.movement_date','=','first_add.min_date');
            })
            ->leftJoin('users as u','u.id','=','m2.user_id')
            ->whereNotNull('b.expiry_date')
            ->whereDate('b.expiry_date','>=',$today)
            ->whereDate('b.expiry_date','<=',$toDate)
            ->where(function($w){
                // كمية > 0 (من عمود الأساس إن وجد)
                $w->where('b.quantity_in_base_unit','>',0)
                  ->orWhere('b.quantity','>',0);
            })
            ->select([
                'b.id',
                'b.product_id','b.store_id','b.batch_no',
                'b.quantity_in_base_unit','b.units_id','b.quantity',
                'b.expiry_date',
                'p.system_code','p.name_ar as p_name_ar','p.name_en as p_name_en',
                'c.name_ar as c_name_ar','c.name_en as c_name_en',
                's.name_ar as s_name_ar','s.name_en as s_name_en',
                DB::raw('first_add.min_date as created_at'),
                'u.name as created_by',
            ]);

        // فلاتر
        if ($ids = $this->arrify($r->get('products')))    $q->whereIn('b.product_id', $ids);
        if ($cats = $this->arrify($r->get('categories')))  $q->whereIn('p.product_category_id', $cats);
        if ($stores = $this->arrify($r->get('store_id')))  $q->whereIn('b.store_id', $stores);

        return $q;
    }

    private function mapRows($rows): array
    {
        $svc = app(UnitConversionService::class);
        $priceCache = []; // pid => ['sale'=>?, 'cost'=>?]
        $unitCache  = []; // pid => 'unit name (smallest)'

        $data = [];
        foreach ($rows as $r) {
            $pid = (int)$r->product_id;

            // اسم الوحدة الصغرى
            if (!array_key_exists($pid, $unitCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $unitCache[$pid] = $baseId ? (optional(\App\Models\supply_chain\unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) { $unitCache[$pid] = '—'; }
            }

            // أسعار من جدول الوحدات
            if (!array_key_exists($pid, $priceCache)) {
                try {
                    $units = $svc->unitsForProduct($pid, 'name_ar');
                    $base  = $units->firstWhere('is_base', true);
                    $priceCache[$pid] = [
                        'sale' => $base['sale_per_unit'] ?? null,
                        'cost' => $base['cost_per_unit'] ?? null,
                    ];
                } catch (\Throwable $e) {
                    $priceCache[$pid] = ['sale'=>null,'cost'=>null];
                }
            }

            $prodName  = trim(($r->p_name_ar ?? '') . ' / ' . ($r->p_name_en ?? ''));
            $catName   = trim(($r->c_name_ar ?? '') . ' / ' . ($r->c_name_en ?? ''));
            $storeName = trim(($r->s_name_ar ?? '') . ' / ' . ($r->s_name_en ?? ''));

            $qtyBase = (float)($r->quantity_in_base_unit ?? 0);
            if ($qtyBase <= 0 && $r->quantity && $r->units_id) {
                try {
                    $qtyBase = $svc->toBase((int)$r->product_id, (float)$r->quantity, (int)$r->units_id);
                } catch (\Throwable $e) {}
            }

            $data[] = [
                'product_code'  => $r->system_code ?: '—',
                'product_name'  => $prodName ?: '—',
                'category_name' => ($catName !== ' / ') ? $catName : '—',
                'sale_price'    => is_null($priceCache[$pid]['sale']) ? '—' : number_format((float)$priceCache[$pid]['sale'], 2),
                'cost_price'    => is_null($priceCache[$pid]['cost']) ? '—' : number_format((float)$priceCache[$pid]['cost'], 2),
                'unit_name'     => $unitCache[$pid] ?? '—',
                'batch_no'      => $r->batch_no ?: '—',
                'quantity'      => number_format(max(0, (float)$qtyBase), 2),
                'store_name'    => $storeName ?: '—',
                'created_at'    => $r->created_at ? Carbon::parse($r->created_at)->format('Y-m-d') : '—',
                'expiry_date'   => $r->expiry_date ? Carbon::parse($r->expiry_date)->format('Y-m-d') : '—',
                'created_by'    => $r->created_by ?: '—',
            ];
        }

        return [$data, count($data)];
    }

    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) return [[], $this->buildMeta($r, [])];

        $q = $this->baseQuery($r)->orderBy('b.expiry_date','asc');
        $rows = $q->limit(200000)->get();
        [$mapped] = $this->mapRows($rows);
        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    private function buildMeta(Request $r, array $rows)
    {
        $storeNames = [];
        if ($ids = $this->arrify($r->get('store_id'))) {
            $storeNames = Store::whereIn('id',$ids)->pluck('name_ar')->toArray();
        }
        $productNames = [];
        if ($ids = $this->arrify($r->get('products'))) {
            $productNames = Product::whereIn('id',$ids)->limit(50)->pluck('name_ar')->toArray();
        }
        $categoryNames = [];
        if ($ids = $this->arrify($r->get('categories'))) {
            $categoryNames = Category::whereIn('id',$ids)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'          => trans('supply_chain_trans.near_expiry_item_report_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'days'           => (int)$r->get('days', 30),
            'store_names'    => $storeNames,
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
            trans('supply_chain_trans.table_batch_no'),
            trans('supply_chain_trans.table_quantity'),
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_created_at'),
            trans('supply_chain_trans.table_expiry_date'),
            trans('supply_chain_trans.table_created_by'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A'.$rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['product_code'],
                $r['product_name'],
                $r['category_name'],
                $r['sale_price'],
                $r['cost_price'],
                $r['unit_name'],
                $r['batch_no'],
                $r['quantity'],
                $r['store_name'],
                $r['created_at'],
                $r['expiry_date'],
                $r['created_by'],
            ]], NULL, 'A'.$rowIndex);
            $rowIndex++;
        }

        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Near_Expiry_Items_' . now()->format('Ymd_His') . '.xlsx';

        ob_start(); $writer->save('php://output'); $excelOutput = ob_get_clean();

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
