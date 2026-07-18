<?php

namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

// مصادر البيانات
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as ProductCategory;
use App\Models\supply_chain\Unit as UnitModel;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\models\hr\Department;

// محاضر الاستلام الفعلي
use App\Models\supply_chain\ReceivingInspecting as ReceivingDoc;
use App\Models\supply_chain\ReceivingInspectingItem as ReceivingItem;

// خدمة التحويل بين الوحدات (التي حفظناها من قبل)
use App\Services\SupplyChain\UnitConversionService;

class most_purchased_itemscontroller extends Controller
{
    /** Index: HTML view, AJAX (DataTables), print, export_excel */
public function index(Request $r)
{
    $action = $r->get('action'); // print | export_excel | (ajax|html)

    // طباعة
    if ($action === 'print') {
        [$rows, $meta] = $this->fetchAllForReport($r);
        return view('purchases.reports.most_purchased_items.print', compact('rows','meta'));
    }

    // تصدير اكسل
    if ($action === 'export_excel') {
        [$rows, $meta] = $this->fetchAllForReport($r);
        return $this->exportExcel($rows, $meta);
    }

    // استدعاء AJAX
    if ($r->wantsJson() || $r->ajax()) {
        if (!$this->hasAnyFilter($r)) {
            return response()->json([
                'draw' => intval($r->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        // نجمع الكميات لكل صنف (بالوحدة الصغرى) + نفلتر ونرتب
        [$aggMap, $baseUnitIds] = $this->aggregateMostPurchased($r); // [product_id => qty_base], [product_id=>unit_id]
        $productIds = array_keys($aggMap);

        // بحث عام على الكود/الاسم
        $search = trim($r->input('search.value') ?? $r->get('q'));
        if ($search !== '') {
            $matchedIds = Product::whereIn('id', $productIds)
                ->where(function($q) use ($search){
                    $q->where('system_code','like',"%{$search}%")
                      ->orWhere('name_ar','like',"%{$search}%")
                      ->orWhere('name_en','like',"%{$search}%");
                })->pluck('id')->all();

            $aggMap = array_intersect_key($aggMap, array_flip($matchedIds));
        }

        // ✅ ترتيب: الأكثر شراءً (تنازلي) — استبدال aruksort بـ arsort
        arsort($aggMap, SORT_NUMERIC);

        $allIdsSorted    = array_keys($aggMap);
        $recordsFiltered = count($allIdsSorted);

        // تقسيم صفحات (ندعم عرض الكل length=-1)
        $start   = (int)$r->get('start', 0);
        $length  = (int)$r->get('length', 25);
        $pageIds = ($length === -1) ? $allIdsSorted : array_slice($allIdsSorted, $start, $length);

        // جهز بيانات الصفحة
        $products = Product::whereIn('id', $pageIds)->get(['id','system_code','name_ar','name_en']);
        $units    = UnitModel::whereIn('id', array_values(array_intersect_key($baseUnitIds, array_flip($pageIds))))
                    ->get(['id','name_ar','name_en'])->keyBy('id');

        // حافظ على ترتيب الصفحة
        $orderIndex = array_flip($pageIds);
        $products = $products->sortBy(function($p) use ($orderIndex){ return $orderIndex[$p->id] ?? 999999; })->values();

        $data = [];
        foreach ($products as $p) {
            $baseUId  = $baseUnitIds[$p->id] ?? null;
            $u        = $baseUId ? ($units[$baseUId] ?? null) : null;
            $unitName = $u ? ($u->name_ar ?: $u->name_en) : '—';

            $data[] = [
                // 0 مسلسل (يُحسب بالواجهة)
                'product_code' => $p->system_code ?? '—',
                'product_name' => ($p->name_ar ?: $p->name_en ?: '—'),
                'unit_name'    => $unitName,
                'qty'          => number_format((float)($aggMap[$p->id] ?? 0), 2),
            ];
        }

        return response()->json([
            'draw'            => intval($r->get('draw')),
            'recordsTotal'    => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    // تحميل القوائم للفلاتر
    $suppliers = Supplier::select('id','name_ar','name_en','code')->orderBy('name_ar')->limit(5000)->get()
        ->map(fn($s)=>[
            'id'=>$s->id,
            'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id) . ($s->code ? " - {$s->code}" : ''),
        ])->values();

    $stores = Store::select('id','manual_code','name_ar','name_en')
        ->orderBy('name_ar')->get()
        ->map(fn($s)=>[
            'id'=>$s->id,
            'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
            'manual_code' => $s->manual_code,
        ])->values();

    $products = Product::select('id','system_code','name_ar','name_en')
        ->orderBy('name_ar')->limit(5000)->get()
        ->map(fn($p)=>[
            'id'=>$p->id,
            'name'=> ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ' - ' . ($p->system_code ?? ''),
        ])->values();

    $categories = \App\Models\supply_chain\productcategory::select('id','name_ar','name_en')->orderBy('name_ar')->get()
        ->map(fn($c)=>[
            'id'=>$c->id,
            'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id),
        ])->values();

    $departments = Department::select('id','name_ar','name_ne')->orderBy('name_ar')->get()
        ->map(fn($d)=>[
            'id'=>$d->id,
            'name'=> ($d->name_ar ?: $d->name_ne ?: (string)$d->id),
        ])->values();

    return view('purchases.reports.most_purchased_items.index', compact('suppliers','stores','products','categories','departments'));
}


    /** هل هناك فلاتر؟ */
    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('date_from')      ||
            $r->filled('date_to')        ||
            $r->filled('suppliers')      ||
            $r->filled('warehouse_id')   ||
            $r->filled('products')       ||
            $r->filled('categories')     ||
            $r->filled('department_id')  ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي (receiving_inspecting_items + receiving_inspecting + po + pr + products) مع تطبيق الفلاتر */
    private function baseReceivingQuery(Request $r)
    {
        $q = ReceivingItem::query()
            ->join('receiving_inspecting as ri','ri.id','=','receiving_inspecting_items.receiving_inspecting_id')
            ->leftJoin('purchase_orders as po','po.id','=','ri.purchase_order_id')
            ->leftJoin('purchase_requisitions as pr','pr.id','=','po.requisition_id')
            ->join('products_data as p','p.id','=','receiving_inspecting_items.product_id');

        // التاريخ (على document_date)
        if ($r->filled('date_from')) {
            $q->whereDate('ri.document_date','>=',$r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('ri.document_date','<=',$r->get('date_to'));
        }

        // المورد
        $suppliers = $r->get('suppliers');
        if (is_array($suppliers) && count($suppliers)) {
            $q->whereIn('ri.supplier_id', $suppliers);
        } elseif (!empty($suppliers)) {
            $q->where('ri.supplier_id', $suppliers);
        }

        // المخازن
        $stores = $r->get('warehouse_id');
        if (is_array($stores) && count($stores)) {
            $q->whereIn('ri.store_id', $stores);
        } elseif (!empty($stores)) {
            $q->where('ri.store_id', $stores);
        }

        // الأقسام
        $dept = $r->get('department_id');
        if (is_array($dept) && count($dept)) {
            $q->whereIn('pr.department_id', $dept);
        } elseif (!empty($dept)) {
            $q->where('pr.department_id', $dept);
        }

        // الأصناف
        $prods = $r->get('products');
        if (is_array($prods) && count($prods)) {
            $q->whereIn('receiving_inspecting_items.product_id', $prods);
        }

        // المجموعات
        $cats = $r->get('categories');
        if (is_array($cats) && count($cats)) {
            $q->whereIn('p.product_category_id', $cats);
        } elseif (!empty($cats)) {
            $q->where('p.product_category_id', $cats);
        }

        return $q;
    }

    /**
     * جمع الكميات المقبولة لكل صنف، محوّلة إلى أصغر وحدة للصنف.
     * نستخدم تجميع SQL على (product_id, accepted_unit_id) لتقليل الصفوف ثم نطبق التحويل في PHP.
     * @return array [$aggMap, $baseUnitIds]
     */
    private function aggregateMostPurchased(Request $r): array
    {
        $q = $this->baseReceivingQuery($r)
            ->where('receiving_inspecting_items.accepted_qty','>',0);

        // تجميع أولي
        $grouped = $q->groupBy('receiving_inspecting_items.product_id','receiving_inspecting_items.accepted_unit_id')
            ->get([
                'receiving_inspecting_items.product_id',
                'receiving_inspecting_items.accepted_unit_id',
                DB::raw('SUM(receiving_inspecting_items.accepted_qty) as qty_sum')
            ]);

        $uc = new UnitConversionService();

        $aggMap = [];      // product_id => total_in_base
        $baseUnitIds = []; // product_id => base_unit_id
        foreach ($grouped as $row) {
            $pid = (int) $row->product_id;
            $fromU = (int) ($row->accepted_unit_id ?? 0);
            $qty   = (float) $row->qty_sum;

            if (!isset($baseUnitIds[$pid])) {
                $baseUnitIds[$pid] = $uc->smallestUnitId($pid);
            }
            $baseU = $baseUnitIds[$pid];

            if ($qty > 0) {
                try {
                    if ($fromU && $baseU) {
                        $converted = $uc->convert($pid, $qty, $fromU, $baseU);
                    } elseif ($baseU) {
                        $converted = $qty; // نفترض أنها بالفعل بالأساس لو لم تتوفر وحدة
                    } else {
                        $converted = $qty; // fallback
                    }
                } catch (\Throwable $e) {
                    $converted = $qty; // fallback
                }

                $aggMap[$pid] = ($aggMap[$pid] ?? 0) + $converted;
            }
        }

        return [$aggMap, $baseUnitIds];
    }

    /** تجهيز كل النتائج للطباعة/التصدير */
private function fetchAllForReport(Request $r): array
{
    if (!$this->hasAnyFilter($r)) {
        return [collect(), $this->buildMeta($r, collect())];
    }

    [$aggMap, $baseUnitIds] = $this->aggregateMostPurchased($r);

    // ✅ ترتيب تنازلي حسب الكمية — استبدال aruksort بـ arsort
    arsort($aggMap, SORT_NUMERIC);
    $sortedIds = array_keys($aggMap);

    $products = Product::whereIn('id', $sortedIds)->get(['id','system_code','name_ar','name_en'])->keyBy('id');
    $units    = UnitModel::whereIn('id', array_values($baseUnitIds))->get(['id','name_ar','name_en'])->keyBy('id');

    $rows = collect();
    foreach ($sortedIds as $pid) {
        $p = $products[$pid] ?? null;
        if (!$p) continue;

        $unitId   = $baseUnitIds[$pid] ?? null;
        $u        = $unitId ? ($units[$unitId] ?? null) : null;
        $unitName = $u ? ($u->name_ar ?: $u->name_en) : '—';

        $rows->push([
            'product_code' => $p->system_code ?? '—',
            'product_name' => ($p->name_ar ?: $p->name_en ?: '—'),
            'unit_name'    => $unitName,
            'qty'          => number_format((float)($aggMap[$pid] ?? 0), 2),
        ]);
    }

    return [$rows, $this->buildMeta($r, $rows)];
}


    /** ميتا الهيدر للطباعة/التصدير */
    private function buildMeta(Request $r, $rows)
    {
        $supplierNames = [];
        $storeNames = [];
        $productNames = [];
        $categoryNames = [];
        $deptNames = [];

        if ($suppliers = $r->get('suppliers')) {
            $ids = is_array($suppliers)? $suppliers : [$suppliers];
            $supplierNames = Supplier::whereIn('id', $ids)->limit(100)->pluck('name_ar')->toArray();
        }
        if ($warehouse = $r->get('warehouse_id')) {
            $ids = is_array($warehouse)? $warehouse : [$warehouse];
            $storeNames = Store::whereIn('id', $ids)->limit(100)->pluck('name_ar')->toArray();
        }
        if ($products = $r->get('products')) {
            $ids = is_array($products)? $products : [$products];
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }
        if ($cats = $r->get('categories')) {
            $ids = is_array($cats)? $cats : [$cats];
            $categoryNames = ProductCategory::whereIn('id', $ids)->limit(100)->pluck('name_ar')->toArray();
        }
        if ($dept = $r->get('department_id')) {
            $ids = is_array($dept)? $dept : [$dept];
            $deptNames = Department::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }

        return [
            'title'          => trans('purchases.most_purchased_items_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'date_from'      => $r->get('date_from'),
            'date_to'        => $r->get('date_to'),
            'supplier_names' => $supplierNames,
            'store_names'    => $storeNames,
            'product_names'  => $productNames,
            'category_names' => $categoryNames,
            'department_names'=> $deptNames,
            'count'          => $rows->count(),
        ];
    }

    /** تصدير إكسل */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('purchases.most_purchased_items_th_code'),
            trans('purchases.most_purchased_items_th_name'),
            trans('purchases.most_purchased_items_th_unit'),
            trans('purchases.most_purchased_items_th_qty'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['product_code'],
                $r['product_name'],
                $r['unit_name'],
                $r['qty'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','D') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Most_Purchased_Items_' . now()->format('Ymd_His') . '.xlsx';

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

/** small helper: array_u k sort by closure */
if (!function_exists('aruksort')) {
    function aruksort(array &$array, callable $cmp): bool {
        $keys = array_keys($array);
        usort($keys, function($a,$b) use ($cmp){ return $cmp($a,$b); });
        $new = [];
        foreach ($keys as $k) $new[$k] = $array[$k];
        $array = $new;
        return true;
    }
}
