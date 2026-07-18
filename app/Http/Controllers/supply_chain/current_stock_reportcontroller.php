<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

use App\models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as Category;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\productstorebalance as Balance;
use App\Models\supply_chain\unit as Unit;
use App\Services\SupplyChain\UnitConversionService;

class current_stock_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // Print
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.current_stock_report.print', compact('rows', 'meta'));
        }

        // Export Excel
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
            $orderColumnIndex = (int)($r->input('order.0.column', 9)); // افتراضياً آخر حركة
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0  => null,                 // مسلسل
                1  => 's.name_ar',          // المخزن
                2  => 'p.system_code',      // كود الصنف
                3  => 'p.name_ar',          // اسم الصنف
                4  => 'c.name_ar',          // المجموعة
                5  => null,                 // سعر البيع (محسوب)
                6  => null,                 // سعر التكلفة (محسوب)
                7  => null,                 // الوحدة (أصغر)
                8  => 'b.batch_no',         // الباتش
                9  => 'lm.last_movement',   // آخر حركة
                10 => 'b.expiry_date',      // الانتهاء
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'lm.last_movement';
            if ($orderBy) $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $pageRows = $q->skip($start)->take($length)->get();

            // تحويل النتائج إلى صفوف العرض (مع التحويل لأصغر وحدة + الأسعار)
            [$data] = $this->mapRows($pageRows);

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // HTML (قوائم الفلاتر)
        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code'=>$s->manual_code,
            ])->values();

        $products = Product::select('id','system_code','name_ar','name_en')
            ->orderBy('name_ar')->limit(4000)->get()
            ->map(fn($p)=>[
                'id'=>$p->id,
                'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id).($p->system_code ? ' - '.$p->system_code : ''),
            ])->values();

        $categories = Category::select('id','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($c)=>['id'=>$c->id,'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id)])->values();

        return view('supply_chain.reports.current_stock_report.index', [
            'stores'     => $stores,
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id') ||
            $r->filled('products') ||
            $r->filled('categories') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي (Query Builder لتفادي مشاكل softdelete aliases) */
    private function baseQuery(Request $r)
    {
        // Subquery: آخر حركة لكل (product_id, store_id, batch_no)
        $lm = DB::table('store_movements')
            ->selectRaw('product_id, store_id, batch_no, MAX(movement_date) AS last_movement')
            ->groupBy('product_id','store_id','batch_no');

        $q = DB::table('product_store_balances as b')
            ->leftJoin('products_data as p', 'p.id', '=', 'b.product_id')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
            ->leftJoin('store_main_date as s', 's.id', '=', 'b.store_id')
            ->leftJoinSub($lm, 'lm', function ($join) {
                $join->on('lm.product_id', '=', 'b.product_id')
                     ->on('lm.store_id', '=', 'b.store_id')
                     ->on('lm.batch_no', '=', 'b.batch_no');
            });

        if (Schema::hasColumn('product_store_balances', 'deleted_at')) {
            $q->whereNull('b.deleted_at');
        }

        $q->select([
            'b.id as b_id','b.product_id','b.store_id','b.batch_no','b.expiry_date',
            'b.quantity_in_base_unit','b.units_id','b.quantity',
            'p.system_code as p_code','p.name_ar as p_name_ar','p.name_en as p_name_en',
            'c.name_ar as c_name_ar','c.name_en as c_name_en',
            's.manual_code as s_manual_code','s.name_ar as s_name_ar','s.name_en as s_name_en',
            DB::raw('lm.last_movement as last_movement'),
        ]);

        // فلاتر
        if ($ids = $this->arrify($r->get('store_id'))) {
            $q->whereIn('b.store_id', $ids);
        }
        if ($ids = $this->arrify($r->get('products'))) {
            $q->whereIn('b.product_id', $ids);
        }
        if ($ids = $this->arrify($r->get('categories'))) {
            $q->whereIn('p.product_category_id', $ids);
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

            // اسم أصغر وحدة
            if ($pid && !array_key_exists($pid, $baseUnitNameCache)) {
                try {
                    $baseId = $svc->smallestUnitId($pid);
                    $baseUnitNameCache[$pid] = $baseId ? (optional(Unit::find($baseId))->name_ar ?? '—') : '—';
                } catch (\Throwable $e) {
                    $baseUnitNameCache[$pid] = '—';
                }
            }

            // الكمية بالوحدة الأساسية
            $qtyBase = (float)($row->quantity_in_base_unit ?? 0.0);
            if ($qtyBase <= 0.0) {
                $fromUnit = (int)($row->units_id ?? 0);
                $factorKey = $pid.':'.$fromUnit;
                if (!isset($factorCache[$factorKey])) {
                    try {
                        $factorCache[$factorKey] = $pid && $fromUnit ? max(1e-9, $svc->toBase($pid, 1.0, $fromUnit)) : 1.0;
                    } catch (\Throwable $e) {
                        $factorCache[$factorKey] = 1.0;
                    }
                }
                $qtyBase = (float)($row->quantity ?? 0.0) * (float)$factorCache[$factorKey];
            }

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
                'store_manual_code'=> $row->s_manual_code ?? '—',
                'store_name'       => $storeName ?: '—',
                'product_code'     => $row->p_code ?? '—',
                'product_name'     => $prodName ?: '—',
                'category_name'    => ($catName !== ' / ') ? $catName : '—',
                'sale_price'       => is_null($basePriceCache[$pid]['sale']) ? '—' : number_format((float)$basePriceCache[$pid]['sale'], 2),
                'cost_price'       => is_null($basePriceCache[$pid]['cost']) ? '—' : number_format((float)$basePriceCache[$pid]['cost'], 2),
                'unit_name'        => $baseUnitNameCache[$pid] ?? '—',
                'batch_no'         => $row->batch_no ?: '—',
                'quantity'         => number_format($qtyBase, 2),
                'last_movement'    => $row->last_movement ? Carbon::parse($row->last_movement)->format('Y-m-d') : '—',
                'expiry_date'      => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('Y-m-d') : '—',
            ];
        }

        return [$data, count($data)];
    }

    /** للطباعة/الإكسل */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [[], $this->buildMeta($r, [])];
        }
        $q = $this->baseQuery($r)->orderBy('lm.last_movement','desc');
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

        return [
            'title'          => trans('supply_chain_trans.current_stock_report_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
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
            trans('supply_chain_trans.table_store_manual_code'),
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_product_code'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_category_name'),
            trans('supply_chain_trans.table_sale_price'),
            trans('supply_chain_trans.table_cost_price'),
            trans('supply_chain_trans.table_unit_small'),
            trans('supply_chain_trans.table_batch_no'),
            trans('supply_chain_trans.table_quantity'),
            trans('supply_chain_trans.table_last_movement_date'),
            trans('supply_chain_trans.table_expiry_date'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['store_manual_code'],
                $r['store_name'],
                $r['product_code'],
                $r['product_name'],
                $r['category_name'],
                $r['sale_price'],
                $r['cost_price'],
                $r['unit_name'],
                $r['batch_no'],
                $r['quantity'],
                $r['last_movement'],
                $r['expiry_date'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Current_Stock_Report_' . now()->format('Ymd_His') . '.xlsx';

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
