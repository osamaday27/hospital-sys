<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

// ====== Models ======
use App\Models\supply_chain\productstorebalance as Balance;
use App\Models\supply_chain\productdata as Product;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\productunit as ProductUnit;
use App\Models\supply_chain\Unit as UnitModel;
use App\Models\supply_chain\productcategory as ProductCategory;

class warehouse_balance_reportcontroller extends Controller
{
    /** Index: handles HTML view, AJAX (DataTables), print, export_excel */
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // === Print view ===
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.warehouse_balance_report.print', compact('rows', 'meta'));
        }

        // === Export Excel ===
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // === AJAX (DataTables) ===
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

            // البحث العام (q أو DataTables search.value)
            $search = $r->input('search.value') ?? $r->get('q');
            if ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('batch_no', 'like', "%{$search}%")
                      ->orWhere('quantity_in_base_unit', 'like', "%{$search}%")
                      ->orWhereHas('store', function ($ws) use ($search) {
                          $ws->where('manual_code', 'like', "%{$search}%")
                             ->orWhere('name_ar', 'like', "%{$search}%")
                             ->orWhere('name_en', 'like', "%{$search}%");
                      })
                      ->orWhereHas('product', function ($wp) use ($search) {
                          $wp->where('system_code', 'like', "%{$search}%")
                             ->orWhere('name_ar', 'like', "%{$search}%")
                             ->orWhere('name_en', 'like', "%{$search}%");
                      });
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب آمن
            $orderColumnIndex = (int)($r->input('order.0.column', 7)); // quantity default
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'id',                      // مسلسل (لا يستخدم)
                1 => 'store_id',                // كود المخزن اليدوي (سنرتب على store_id)
                2 => 'store_id',                // اسم المخزن
                3 => 'product_id',              // system_code (على product_id)
                4 => 'product_id',              // اسم الصنف
                5 => 'product_id',              // المجموعة
                6 => 'batch_no',                // الباتش
                7 => 'quantity_in_base_unit',   // الكمية
                8 => 'units_id',                // الوحدة الصغرى (تقريب بترتيب units_id)
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'id';
            $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            // جلب الصفحة
            $rows = $q->skip($start)->take($length)->get();

            // تجهيز أسماء المجموعات مرة واحدة
            $categoryNames = $this->collectCategoryNames($rows);

            // تجهيز الوحدة الصغرى بكفاءة (تحميل علاقات product_units + وحدات)
            [$smallestUnitNamesAr, $smallestUnitNamesEn] = $this->collectSmallestUnitsNames($rows);

            // تجهيز الرد
            $data = [];
            foreach ($rows as $row) {
                $storeName = trim(($row->store->name_ar ?? '') . ' / ' . ($row->store->name_en ?? ''));
                $productName = trim(($row->product->name_ar ?? '') . ' / ' . ($row->product->name_en ?? ''));
                $categoryName = $categoryNames[$row->product->product_category_id ?? 0] ?? '—';
                $smallUnit = trim(($smallestUnitNamesAr[$row->id] ?? '') . ' / ' . ($smallestUnitNamesEn[$row->id] ?? ''));

                $data[] = [
                    // 0 مسلسل يحسب في الواجهة
                    'store_manual_code' => $row->store->manual_code ?? '—',
                    'store_name'        => $storeName,
                    'product_code'      => $row->product->system_code ?? '—',
                    'product_name'      => $productName,
                    'category_name'     => $categoryName,
                    'batch_no'          => $row->batch_no ?? '—',
                    'qty_base'          => number_format((float)$row->quantity_in_base_unit, 2),
                    'unit_small'        => $smallUnit ?: '—',
                ];
            }

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // تحميل القوائم للفلاتر
        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        $products = Product::select('id','system_code','name_ar','name_en')
            ->orderBy('name_ar')->limit(2000)->get()
            ->map(fn($p)=>[
                'id'=>$p->id,
                'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ' - ' . ($p->system_code ?? ''),
            ])->values();

        return view('supply_chain.reports.warehouse_balance_report.index', [
            'stores'   => $stores,
            'products' => $products,
        ]);
    }

    /** هل هناك أي فلاتر مفعلة؟ */
    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id')   ||
            $r->filled('products')   ||
            $r->filled('date_from')  ||
            $r->filled('date_to')    ||
            $r->filled('q')          ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي */
    private function baseQuery(Request $r)
    {
        $q = Balance::query()
            ->with([
                'store:id,manual_code,name_ar,name_en',
                'product:id,system_code,name_ar,name_en,product_category_id',
                'unit:id,name_ar,name_en',
            ]);

        // المتعدد: المخازن
        $stores = $r->get('store_id');
        if (is_array($stores) && count($stores)) {
            $q->whereIn('store_id', $stores);
        } elseif (!empty($stores)) {
            $q->where('store_id', $stores);
        }

        // المتعدد: الأصناف
        $products = $r->get('products');
        if (is_array($products) && count($products)) {
            $q->whereIn('product_id', $products);
        }

        // التاريخ على created_at
        if ($r->filled('date_from')) {
            $q->whereDate('created_at', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('created_at', '<=', $r->get('date_to'));
        }

        return $q;
    }

    /** تجهيز أسماء المجموعات لدفعة الصفوف الحالية */
    private function collectCategoryNames($rows): array
    {
        $categoryIds = [];
        foreach ($rows as $row) {
            if (!empty($row->product?->product_category_id)) {
                $categoryIds[] = (int)$row->product->product_category_id;
            }
        }
        $categoryIds = array_values(array_unique($categoryIds));
        if (empty($categoryIds)) return [];

        $cats = ProductCategory::whereIn('id', $categoryIds)->get(['id','name_ar','name_en']);
        $out  = [];
        foreach ($cats as $c) {
            $out[$c->id] = trim(($c->name_ar ?? '') . ' / ' . ($c->name_en ?? ''));
        }
        return $out;
    }

    /**
     * إيجاد اسم أصغر وحدة لكل صف (بأقل استعلامات ممكنة)
     * نحمّل product_units لكل المنتجات الظاهرة، ثم نبني خريطة سريعة للوصول للـ leaf.
     */
    private function collectSmallestUnitsNames($rows): array
    {
        $productIds = [];
        foreach ($rows as $row) {
            if (!empty($row->product_id)) $productIds[] = (int)$row->product_id;
        }
        $productIds = array_values(array_unique($productIds));
        if (empty($productIds)) return [[], []];

        $rels = ProductUnit::whereIn('product_id', $productIds)
                ->whereNotNull('unit_small_id')
                ->get(['product_id','unit_large_id','unit_small_id','conversion_rate']);

        // بنبني لكل منتج: قائمة large->small
        $graph = [];
        $allSmall = [];
        $allLarge = [];
        foreach ($rels as $r) {
            $graph[$r->product_id][$r->unit_large_id] = $r->unit_small_id;
            $allSmall[$r->product_id][$r->unit_small_id] = true;
            $allLarge[$r->product_id][$r->unit_large_id] = true;
        }

        // نحسب leaf الافتراضي لكل منتج (small - large)
        $leafByProduct = [];
        foreach ($productIds as $pid) {
            $smallSet = array_keys($allSmall[$pid] ?? []);
            $largeSet = array_keys($allLarge[$pid] ?? []);
            $leafs = array_diff($smallSet, $largeSet);
            $leafByProduct[$pid] = !empty($leafs) ? (int)reset($leafs) : null; // لو فشل: null
        }

        // الآن نحسب لكل صف: أصغر وحدة بالتحرك من units_id للأسفل إن وُجد، وإلا leafByProduct
        $unitIdsNeeded = [];
        $smallestIdPerRow = [];
        foreach ($rows as $row) {
            $pid = (int)$row->product_id;
            $start = $row->units_id ? (int)$row->units_id : null;

            $smallestUnitId = $this->followToLeaf($graph[$pid] ?? [], $start);
            if (!$smallestUnitId) $smallestUnitId = $leafByProduct[$pid] ?? null;

            if ($smallestUnitId) {
                $smallestIdPerRow[$row->id] = $smallestUnitId;
                $unitIdsNeeded[$smallestUnitId] = true;
            }
        }

        // جلب أسماء الوحدات المطلوبة مرة واحدة
        $units = [];
        if (!empty($unitIdsNeeded)) {
            $units = UnitModel::whereIn('id', array_keys($unitIdsNeeded))
                        ->get(['id','name_ar','name_en'])
                        ->keyBy('id');
        }

        $namesAr = [];
        $namesEn = [];
        foreach ($smallestIdPerRow as $rid => $uid) {
            $u = $units[$uid] ?? null;
            $namesAr[$rid] = $u->name_ar ?? null;
            $namesEn[$rid] = $u->name_en ?? null;
        }

        return [$namesAr, $namesEn];
    }

    /** يتحرك من وحدة كبيرة إلى أصغر ورقة بالاعتماد على خريطة large->small */
    private function followToLeaf(array $map, ?int $start): ?int
    {
        if (!$start) return null;
        $current = $start;
        $guard   = 0;
        while (isset($map[$current]) && $guard < 20) {
            $current = (int)$map[$current];
            $guard++;
        }
        return $current ?: null;
    }

    /** إحضار كل النتائج للطباعة/التصدير */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('id', 'desc');

        // حد أمان اختياري
        $rows = $q->limit(200000)->get();

        // Prepare category names & smallest units for all rows
        $categoryNames = $this->collectCategoryNames($rows);
        [$smallAr, $smallEn] = $this->collectSmallestUnitsNames($rows);

        // تحويل مبسّط
        $mapped = $rows->map(function ($row) use ($categoryNames, $smallAr, $smallEn) {
            $storeName   = trim(($row->store->name_ar ?? '') . ' / ' . ($row->store->name_en ?? ''));
            $productName = trim(($row->product->name_ar ?? '') . ' / ' . ($row->product->name_en ?? ''));
            $category    = $categoryNames[$row->product->product_category_id ?? 0] ?? '—';
            $smallUnit   = trim(($smallAr[$row->id] ?? '') . ' / ' . ($smallEn[$row->id] ?? ''));

            return [
                'store_manual_code' => $row->store->manual_code ?? '—',
                'store_name'        => $storeName,
                'product_code'      => $row->product->system_code ?? '—',
                'product_name'      => $productName,
                'category_name'     => $category,
                'batch_no'          => $row->batch_no ?? '—',
                'qty_base'          => number_format((float)$row->quantity_in_base_unit, 2),
                'unit_small'        => $smallUnit ?: '—',
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    /** معلومات الهيدر للطباعة/التصدير */
    private function buildMeta(Request $r, $rows)
    {
        $storeNames = [];
        $productNames = [];

        if ($stores = $r->get('store_id')) {
            $ids = is_array($stores)? $stores : [$stores];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        if ($products = $r->get('products')) {
            $ids = is_array($products)? $products : [$products];
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }

        return [
            'title'        => trans('supply_chain_trans.warehouse_balance_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'store_names'  => $storeNames,
            'product_names'=> $productNames,
            'count'        => $rows->count(),
        ];
    }

    /** تصدير Excel */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_store_manual_code'),
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_product_code'),
            trans('supply_chain_trans.table_product_name'),
            trans('supply_chain_trans.table_category_name'),
            trans('supply_chain_trans.table_batch_no'),
            trans('supply_chain_trans.table_qty_base'),
            trans('supply_chain_trans.table_unit_small'),
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
                $r['batch_no'],
                $r['qty_base'],
                $r['unit_small'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','H') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Warehouse_Balances_' . now()->format('Ymd_His') . '.xlsx';

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
