<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Models (بعضها بالـ alias بسبب اختلاف الـ namespaces)
use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as Category;
use App\Models\supply_chain\storemaindata as Store;
use App\models\supply_chain\storemovement as Movement;
use App\Models\supply_chain\unit as Unit;
use App\Services\SupplyChain\UnitConversionService;

class expired_item_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.expired_item_report.print', compact('rows', 'meta'));
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
                      ->orWhere('c.name_ar', 'like', "%{$search}%")
                      ->orWhere('c.name_en', 'like', "%{$search}%")
                      ->orWhere('s.name_ar', 'like', "%{$search}%")
                      ->orWhere('s.name_en', 'like', "%{$search}%")
                      ->orWhere('b.batch_no', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 11)); // expiry_date افتراضي
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0  => 'b.id',                // #
                1  => 'p.system_code',       // كود الصنف
                2  => 'p.name_ar',           // اسم الصنف
                3  => 'c.name_ar',           // المجموعة
                4  => null,                  // sale_price (محسوب)
                5  => null,                  // cost_price (محسوب)
                6  => 'b.batch_no',          // الباتش
                7  => null,                  // الوحدة الصغرى (محسوب)
                8  => null,                  // الكمية بالقاعدة (محسوب)
                9  => 's.name_ar',           // المخزن
                10 => null,                  // تاريخ الإضافة (محسوب من أول حركة موجبة)
                11 => 'b.expiry_date',       // تاريخ الانتهاء
                12 => null,                  // المستخدم (محسوب)
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'b.expiry_date';
            if ($orderBy) {
                $q->orderBy($orderBy, $orderDir);
            } else {
                $q->orderBy('b.expiry_date', 'desc');
            }

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();

            // تجهيز صفوف العرض مع عمليات التحويل/الأسعار/أول حركة إضافة
            [$data] = $this->mapRowsWithComputed($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // القوائم (فلاتر)
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

        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        return view('supply_chain.reports.expired_item_report.index', [
            'products'   => $products,
            'categories' => $categories,
            'stores'     => $stores,
        ]);
    }

    /** الاستعلام الأساسي: DB::table لتفادي مشاكل SoftDeletes alias */
    private function baseQuery(Request $r)
    {
        $today = Carbon::today()->toDateString();

        $q = DB::table('product_store_balances as b')
            ->leftJoin('products_data as p', 'p.id', '=', 'b.product_id')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
            ->leftJoin('store_main_date as s', 's.id', '=', 'b.store_id')
            ->leftJoin('units as un', 'un.id', '=', 'b.units_id')
            ->whereNotNull('b.expiry_date')
            ->whereDate('b.expiry_date', '<', $today)
            ->where(function($w){
                $w->where('b.quantity_in_base_unit', '>', 0)
                  ->orWhere('b.quantity', '>', 0);
            })
            ->select([
                'b.id','b.product_id','b.store_id','b.batch_no','b.expiry_date',
                'b.units_id','b.quantity','b.quantity_in_base_unit','b.created_at as b_created_at',
                'p.system_code','p.name_ar as p_name_ar','p.name_en as p_name_en','p.product_category_id',
                'c.name_ar as c_name_ar','c.name_en as c_name_en',
                's.manual_code as s_manual_code','s.name_ar as s_name_ar','s.name_en as s_name_en',
                'un.name_ar as un_name_ar','un.name_en as un_name_en',
            ]);

        // فلاتر (اختيارية)
        if ($ids = $this->arrify($r->get('products'))) {
            $q->whereIn('b.product_id', $ids);
        }
        if ($cats = $this->arrify($r->get('categories'))) {
            $q->whereIn('p.product_category_id', $cats);
        }
        if ($stores = $this->arrify($r->get('store_id'))) {
            $q->whereIn('b.store_id', $stores);
        }

        return $q;
    }

    /** تجهيز صفوف العرض: تحويلات/أسعار/أول حركة إضافة */
    private function mapRowsWithComputed($rows): array
    {
        $svc = app(UnitConversionService::class);

        // Cacheات لتقليل عدد الاستعلامات
        $unitBaseNameCache = []; // product_id => unit_name
        $priceCache        = []; // product_id => ['sale'=>?, 'cost'=>?]

        // جهّز مفاتيح ثلاثية لاستخراج أول حركة موجبة دفعة واحدة
        $triples = [];
        foreach ($rows as $r) {
            $pid = (int)$r->product_id;
            $sid = (int)$r->store_id;
            $bn  = (string)($r->batch_no ?? '');
            $triples[$this->makeKey($sid, $pid, $bn)] = ['store_id'=>$sid,'product_id'=>$pid,'batch_no'=>$bn];
        }
        $firstInboundMap = $this->prefetchFirstInboundInfos(array_values($triples)); // key => ['date'=>..., 'user_name'=>...]

        $data = [];
        foreach ($rows as $r) {
            $pid = (int)$r->product_id;

            // وحدة أصغر اسمًا
            if (!array_key_exists($pid, $unitBaseNameCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $unitBaseNameCache[$pid] = $baseId ? (optional(Unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) {
                    $unitBaseNameCache[$pid] = '—';
                }
            }

            // الكمية بالوحدة الأصغر
            $qtyBase = (float)($r->quantity_in_base_unit ?? 0);
            if ($qtyBase <= 0 && $r->quantity && $r->units_id) {
                try {
                    $qtyBase = $svc->toBase($pid, (float)$r->quantity, (int)$r->units_id);
                } catch (\Throwable $e) {
                    $qtyBase = 0.0;
                }
            }

            // أسعار للوحدة الأصغر
            if (!array_key_exists($pid, $priceCache)) {
                $priceCache[$pid] = $this->getBasePricesForProduct($pid, $svc);
            }

            // اسم الصنف/المجموعة/المخزن
            $productName  = trim(($r->p_name_ar ?? '') . ' / ' . ($r->p_name_en ?? ''));
            $categoryName = trim(($r->c_name_ar ?? '') . ' / ' . ($r->c_name_en ?? ''));
            $storeName    = trim(($r->s_name_ar ?? '') . ' / ' . ($r->s_name_en ?? ''));

            // أول إضافة
            $k = $this->makeKey((int)$r->store_id, (int)$r->product_id, (string)($r->batch_no ?? ''));
            $first = $firstInboundMap[$k] ?? ['date'=>null,'user_name'=>null];

            $data[] = [
                'product_code'  => $r->system_code ?: '—',
                'product_name'  => $productName ?: '—',
                'category_name' => ($categoryName !== ' / ') ? $categoryName : '—',
                'sale_price'    => is_null($priceCache[$pid]['sale']) ? '—' : number_format((float)$priceCache[$pid]['sale'], 2),
                'cost_price'    => is_null($priceCache[$pid]['cost']) ? '—' : number_format((float)$priceCache[$pid]['cost'], 2),
                'batch_no'      => $r->batch_no ?: '—',
                'unit_name'     => $unitBaseNameCache[$pid] ?? '—',
                'quantity'      => number_format(max(0, (float)$qtyBase), 2),
                'store_name'    => $storeName ?: '—',
                'created_at'    => $first['date'] ? Carbon::parse($first['date'])->format('Y-m-d') : '—',
                'expiry_date'   => $r->expiry_date ? Carbon::parse($r->expiry_date)->format('Y-m-d') : '—',
                'created_by'    => $first['user_name'] ?: '—',
            ];
        }

        return [$data, count($data)];
    }

    /** استرجاع أسعار “أصغر وحدة” من product_units أو fallback لآخر حركة للصنف */
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

    /** جلب أول حركة موجبة لكل (store_id, product_id, batch_no) دفعة واحدة */
    private function prefetchFirstInboundInfos(array $triples): array
    {
        if (empty($triples)) return [];

        $productIds = array_values(array_unique(array_map(fn($t)=> (int)$t['product_id'], $triples)));
        $storeIds   = array_values(array_unique(array_map(fn($t)=> (int)$t['store_id'],   $triples)));
        $batches    = array_values(array_unique(array_map(fn($t)=> (string)$t['batch_no'], $triples)));

        // subquery: أول تاريخ حركة موجبة
        $sub = DB::table('store_movements as m')
            ->select('m.product_id','m.store_id','m.batch_no', DB::raw('MIN(m.movement_date) as first_date'))
            ->whereIn('m.product_id', $productIds)
            ->whereIn('m.store_id', $storeIds)
            ->whereIn('m.batch_no', $batches)
            ->where('m.quantity', '>', 0)
            ->groupBy('m.product_id','m.store_id','m.batch_no');

        $rows = DB::table(DB::raw("({$sub->toSql()}) as x"))
            ->mergeBindings($sub)
            ->leftJoin('store_movements as m2', function($j){
                $j->on('m2.product_id','=','x.product_id')
                  ->on('m2.store_id','=','x.store_id')
                  ->on('m2.batch_no','=','x.batch_no')
                  ->on('m2.movement_date','=','x.first_date');
            })
            ->leftJoin('users as u', 'u.id', '=', 'm2.user_id')
            ->select([
                'x.product_id','x.store_id','x.batch_no','x.first_date',
                'u.name as u_name'
            ])->get();

        $map = [];
        foreach ($rows as $r) {
            $k = $this->makeKey((int)$r->store_id, (int)$r->product_id, (string)($r->batch_no ?? ''));
            $map[$k] = [
                'date'      => $r->first_date,
                'user_name' => $r->u_name,
            ];
        }
        return $map;
    }

    /** الطباعة/الإكسل */
    private function fetchAllForReport(Request $r): array
    {
        $q = $this->baseQuery($r)->orderBy('b.expiry_date','desc');
        $rows = $q->limit(200000)->get(); // حد أمان
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
        $storeNames = [];
        if ($stores = $this->arrify($r->get('store_id'))) {
            $storeNames = Store::whereIn('id', $stores)->pluck('name_ar')->toArray();
        }

        return [
            'title'          => trans('supply_chain_trans.expired_item_report_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'product_names'  => $productNames,
            'category_names' => $categoryNames,
            'store_names'    => $storeNames,
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
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_created_at'),
            trans('supply_chain_trans.table_expiry_date'),
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
                $r['batch_no'],
                $r['unit_name'],
                $r['quantity'],
                $r['store_name'],
                $r['created_at'],
                $r['expiry_date'],
                $r['created_by'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Expired_Items_' . now()->format('Ymd_His') . '.xlsx';

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

    private function makeKey(int $store_id, int $product_id, string $batch_no): string
    {
        return $store_id.'|'.$product_id.'|'.$batch_no;
    }
}
