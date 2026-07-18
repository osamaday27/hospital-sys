<?php

namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

// النماذج
use App\Models\Purchases\PurchaseOrder as PO;
use App\Models\Purchases\PurchaseOrderItem as POItem;

use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\productdata as Product;
use App\Models\supply_chain\productcategory as ProductCategory;
use App\models\hr\Department;
use App\Models\Suppliers\suppliersdata as Supplier;

use App\Models\supply_chain\ReceivingInspectingItem as ReceivingItem;
use App\Models\supply_chain\Unit as UnitModel;

// خدمة التحويل
use App\Services\SupplyChain\UnitConversionService;

class supply_orders_reportcontroller extends Controller
{
    /** Index: HTML view, AJAX (DataTables), print, export_excel */
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // طباعة القائمة
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('purchases.reports.supply_orders_report.print', compact('rows','meta'));
        }

        // تصدير إكسل
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

            // بحث عام
            $search = $r->input('search.value') ?? $r->get('q');
            if ($search) {
                $q->where(function($w) use ($search){
                    $w->where('purchase_orders.order_number','like',"%{$search}%")
                      ->orWhereExists(function($s) use ($search){
                          $s->select(DB::raw(1))
                            ->from('suppliers_data as sd')
                            ->whereColumn('sd.id','purchase_orders.supplier_id')
                            ->where(function($x) use ($search){
                                $x->where('sd.name_ar','like',"%{$search}%")
                                  ->orWhere('sd.name_en','like',"%{$search}%")
                                  ->orWhere('sd.code','like',"%{$search}%");
                            });
                      })
                      ->orWhereExists(function($s) use ($search){
                          $s->select(DB::raw(1))
                            ->from('purchase_order_items as poi')
                            ->join('products_data as p','p.id','=','poi.item_id')
                            ->whereColumn('poi.order_id','purchase_orders.id')
                            ->where(function($x) use ($search){
                                $x->where('p.system_code','like',"%{$search}%")
                                  ->orWhere('p.name_ar','like',"%{$search}%")
                                  ->orWhere('p.name_en','like',"%{$search}%");
                            });
                      });
                });
            }

            // ترتيب آمن
            $orderColumnIndex = (int)($r->input('order.0.column', 2)); // التاريخ
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'purchase_orders.id',
                1 => 'purchase_orders.order_number',
                2 => 'purchase_orders.order_date',
                3 => 'purchase_orders.id',
                4 => 'purchase_orders.id',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'purchase_orders.id';

            // اجلب IDs مرتبة
            $orderedIds = (clone $q)
                ->select('purchase_orders.id')
                ->distinct()
                ->orderBy($orderBy, $orderDir)
                ->pluck('purchase_orders.id')
                ->all();

            // احسب الحالة الفعلية + فلترة حسب الحالة لو مطلوبة
            $uc = new UnitConversionService();
            $statusFilter = $this->normalizeStatuses($r->get('status'));

            $computed = $this->computeOrdersStatusAndSupplier($orderedIds, $uc); // [id => ['status'=>..., 'supplier'=>...]]
            $filteredIds = [];
            foreach ($orderedIds as $oid) {
                if (!isset($computed[$oid])) continue;
                $st = $computed[$oid]['status'] ?? 'pending';
                if (empty($statusFilter) || in_array($st, $statusFilter, true)) {
                    $filteredIds[] = $oid;
                }
            }

            $recordsFiltered = count($filteredIds);

            // تقسيم الصفحات (دعم length=-1)
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            $pageIds = ($length === -1) ? $filteredIds : array_slice($filteredIds, $start, $length);

            // حمّل سجلات الصفحة بالترتيب
            $rows = collect();
            if (!empty($pageIds)) {
                $idsOrderStr = implode(',', $pageIds);
                $rows = PO::with(['supplier:id,name_ar,name_en'])
                    ->whereIn('id', $pageIds)
                    ->orderByRaw('FIELD(id, '.$idsOrderStr.')')
                    ->get(['id','order_number','order_date','supplier_id','requisition_id']);
            }

            // أعمدة الجدول
            $data = [];
            foreach ($rows as $po) {
                $info = $computed[$po->id] ?? ['status'=>'pending','items_count'=>0];
                $supplierName = $po->supplier?->name_ar ?: $po->supplier?->name_en ?: '—';

                $data[] = [
                    'order_number' => $po->order_number,
                    'order_date'   => optional($po->order_date)->format('Y-m-d') ?? (is_string($po->order_date) ? $po->order_date : ''),
                    'supplier'     => $supplierName,
                    'status'       => trans('purchases.supply_orders_report_status_'.$info['status']) ?? $info['status'],
                    'items_html'   => view('purchases.reports.supply_orders_report._items_link', [
                        'id'    => $po->id,
                        'count' => $info['items_count'] ?? 0,
                    ])->render(),
                ];
            }

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        // ===== تحميل القوائم للفلاتر =====
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

        $categories = ProductCategory::select('id','name_ar','name_en')->orderBy('name_ar')->get()
            ->map(fn($c)=>[
                'id'=>$c->id,
                'name'=> ($c->name_ar ?: $c->name_en ?: (string)$c->id),
            ])->values();

        $departments = Department::select('id','name_ar','name_ne')->orderBy('name_ar')->get()
            ->map(fn($d)=>[
                'id'=>$d->id,
                'name'=> ($d->name_ar ?: $d->name_ne ?: (string)$d->id),
            ])->values();

        $statuses = [
            'pending' => trans('purchases.supply_orders_report_status_pending') ?? 'Pending',
            'partial' => trans('purchases.supply_orders_report_status_partial') ?? 'Partial',
            'closed'  => trans('purchases.supply_orders_report_status_closed')  ?? 'Closed',
        ];

        return view('purchases.reports.supply_orders_report.index', compact(
            'suppliers','stores','products','categories','departments','statuses'
        ));
    }

    /** صفحة تفاصيل الأصناف لكل أمر */
    public function show(Request $r, $id)
    {
        $po = PO::with([
                'supplier:id,name_ar,name_en',
                'items' => function($q){
                    $q->with([
                        'product:id,system_code,name_ar,name_en,product_category_id',
                        'unit.smallUnit:id,name_ar,name_en',
                        'unit.largeUnit:id,name_ar,name_en',
                    ]);
                },
            ])->findOrFail($id);

        $uc = new UnitConversionService();

        // اجلب مخزن الطلب (من requisition)
        $warehouseId = DB::table('purchase_requisitions')->where('id', $po->requisition_id)->value('warehouse_id');
        $warehouse   = $warehouseId ? Store::find($warehouseId) : null;
        $warehouseName = $warehouse?->name_ar ?: $warehouse?->name_en ?: '—';

        // تجميع استلام فعلي للصنف داخل الأمر
        $recvByProduct = $this->fetchOrderReceiptsGrouped($po->id);

        // حضّر الأصناف
        $items = collect($po->items)->map(function($it) use ($uc, $recvByProduct, $warehouseName) {
            // وحدة العرض: small ثم large ثم أصغر وحدة
            $displayUnitId = $it->unit?->smallUnit?->id
                          ?? $it->unit?->largeUnit?->id
                          ?? $uc->smallestUnitId((int)$it->item_id);

            $unitName = $it->unit?->smallUnit?->name_ar
                      ?? $it->unit?->largeUnit?->name_ar
                      ?? '—';

            $qtyRequested = (float) $it->requested_qty;

            // المستلم المحوّل لنفس وحدة العرض
            $sum = $this->summarizeDeliveredForItem((int)$it->item_id, $displayUnitId, $recvByProduct, $uc);
            $qtyDelivered = $sum['delivered'];
            $qtyRemain    = max(0.0, $qtyRequested - $qtyDelivered);

            return [
                'code'         => $it->product?->system_code ?? '—',
                'name'         => ($it->product?->name_ar ?: $it->product?->name_en ?: '—'),
                'unit'         => $unitName,
                'qty_req'      => $qtyRequested,
                'qty_delivered'=> $qtyDelivered,
                'qty_remain'   => $qtyRemain,
                'store'        => $warehouseName,
            ];
        })->values();

        $generatedAt = Carbon::now()->format('Y-m-d H:i');

        // حالة الأمر (مجمّع في أصغر وحدة)
        $agg = $this->computeOrderAggregatesBase($po->id, $po->items, $uc);
        $status = $agg['status'];

        return view('purchases.reports.supply_orders_report.show', [
            'po'          => $po,
            'items'       => $items,
            'generatedAt' => $generatedAt,
            'warehouse'   => $warehouseName,
            'status'      => $status,
        ]);
    }

    /* ===================== فلاتر وقاعدة الاستعلام ===================== */

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
            $r->filled('status')         ||
            ($r->input('search.value'))
        );
    }

    private function baseQuery(Request $r)
    {
        $q = PO::query()
            ->leftJoin('purchase_requisitions as pr','pr.id','=','purchase_orders.requisition_id')
            ->leftJoin('purchase_order_items as poi','poi.order_id','=','purchase_orders.id')
            ->leftJoin('products_data as p','p.id','=','poi.item_id');

        // التاريخ على order_date
        if ($r->filled('date_from')) {
            $q->whereDate('purchase_orders.order_date','>=',$r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('purchase_orders.order_date','<=',$r->get('date_to'));
        }

        // المورد
        $suppliers = $r->get('suppliers');
        if (is_array($suppliers) && count($suppliers)) {
            $q->whereIn('purchase_orders.supplier_id', $suppliers);
        } elseif (!empty($suppliers)) {
            $q->where('purchase_orders.supplier_id', $suppliers);
        }

        // المخزن (من requisition)
        $stores = $r->get('warehouse_id');
        if (is_array($stores) && count($stores)) {
            $q->whereIn('pr.warehouse_id', $stores);
        } elseif (!empty($stores)) {
            $q->where('pr.warehouse_id', $stores);
        }

        // القسم
        $dept = $r->get('department_id');
        if (is_array($dept) && count($dept)) {
            $q->whereIn('pr.department_id', $dept);
        } elseif (!empty($dept)) {
            $q->where('pr.department_id', $dept);
        }

        // الأصناف
        $prods = $r->get('products');
        if (is_array($prods) && count($prods)) {
            $q->whereIn('poi.item_id', $prods);
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

    private function normalizeStatuses($st): array
    {
        if (is_array($st)) return array_values(array_intersect($st, ['pending','partial','closed']));
        if (empty($st)) return [];
        return in_array($st, ['pending','partial','closed'], true) ? [$st] : [];
    }

    /* ===================== تجميعات/حسابات ===================== */

    /** تلخيص الاستلامات للأمر: product_id => [ ['qty'=>..,'unit_id'=>..,'date'=>Carbon] ... ] */
    private function fetchOrderReceiptsGrouped(int $orderId): array
    {
        $rows = ReceivingItem::query()
            ->join('receiving_inspecting as ri','ri.id','=','receiving_inspecting_items.receiving_inspecting_id')
            ->where('ri.purchase_order_id', $orderId)
            ->where('receiving_inspecting_items.accepted_qty','>',0)
            ->get([
                'receiving_inspecting_items.product_id',
                'receiving_inspecting_items.accepted_qty',
                'receiving_inspecting_items.accepted_unit_id',
                'ri.document_date',
            ]);

        $byProduct = [];
        foreach ($rows as $r) {
            $pid = (int) $r->product_id;
            $byProduct[$pid][] = [
                'qty'     => (float) $r->accepted_qty,
                'unit_id' => (int) ($r->accepted_unit_id ?? 0),
                'date'    => $r->document_date ? Carbon::parse($r->document_date) : null,
            ];
        }
        return $byProduct;
    }

    /** لخانة واحدة داخل أمر: لخص كمية المستلم بوحدة عرض معينة + أحدث تاريخ استلام */
    private function summarizeDeliveredForItem(int $productId, ?int $displayUnitId, array $recvByProduct, UnitConversionService $uc): array
    {
        $lines = $recvByProduct[$productId] ?? [];
        $sum = 0.0; $lastDate = null;

        foreach ($lines as $ln) {
            $qty = (float) ($ln['qty'] ?? 0);
            $fromUnitId = (int) ($ln['unit_id'] ?? 0);

            if ($qty > 0) {
                if ($displayUnitId && $fromUnitId) {
                    try {
                        $sum += $uc->convert($productId, $qty, $fromUnitId, $displayUnitId);
                    } catch (\Throwable $e) {
                        $sum += $qty;
                    }
                } else {
                    $sum += $qty;
                }
            }

            $dt = $ln['date'];
            if ($dt && (!$lastDate || $dt->gt($lastDate))) {
                $lastDate = $dt;
            }
        }

        return ['delivered' => $sum, 'last_date' => $lastDate];
    }

    /** تجميعات بالأصغر وحدة لكل أمر (للاستخدام بحساب الحالة) */
    private function computeOrderAggregatesBase(int $orderId, $itemsCollection, UnitConversionService $uc): array
    {
        $items = collect($itemsCollection);
        $recv  = $this->fetchOrderReceiptsGrouped($orderId);

        $reqBase = 0.0;
        $delBase = 0.0;

        foreach ($items as $it) {
            $pid = (int) $it->item_id;
            // أصغر وحدة للصنف
            $baseU = $uc->smallestUnitId($pid);

            // المطلوبة -> حوّل من وحدة السطر إلى الأصغر
            $reqQty = (float) $it->requested_qty;
            if ($reqQty > 0 && $it->unit_id && $baseU) {
                try {
                    $reqBase += $uc->convert($pid, $reqQty, (int)$it->unit_id, $baseU);
                } catch (\Throwable $e) {
                    $reqBase += $reqQty;
                }
            } else {
                $reqBase += $reqQty;
            }

            // المستلم -> من accepted_qty/accepted_unit_id إلى الأصغر
            foreach ($recv[$pid] ?? [] as $ln) {
                $qty = (float) ($ln['qty'] ?? 0);
                $fromUnit = (int) ($ln['unit_id'] ?? 0);
                if ($qty > 0 && $fromUnit && $baseU) {
                    try {
                        $delBase += $uc->convert($pid, $qty, $fromUnit, $baseU);
                    } catch (\Throwable $e) {
                        $delBase += $qty;
                    }
                } else {
                    $delBase += $qty;
                }
            }
        }

        $status = 'pending';
        if ($delBase > 0 && $delBase + 1e-6 < $reqBase) $status = 'partial';
        if ($delBase + 1e-6 >= $reqBase && $reqBase > 0) $status = 'closed';
        if ($reqBase <= 0 && $delBase > 0) $status = 'closed';

        return ['req_base'=>$reqBase,'del_base'=>$delBase,'status'=>$status];
    }

    /** احسب حالة الأمر + عدد الأصناف (للقائمة) بشكل مجمع */
    private function computeOrdersStatusAndSupplier(array $orderIds, UnitConversionService $uc): array
    {
        if (empty($orderIds)) return [];

        // حمّل العناصر والموردين
        $orders = PO::with([
                'supplier:id,name_ar,name_en',
                'items:id,order_id,item_id,unit_id,requested_qty',
            ])
            ->whereIn('id', $orderIds)->get(['id','supplier_id']);

        $out = [];
        foreach ($orders as $po) {
            $agg = $this->computeOrderAggregatesBase($po->id, $po->items, $uc);
            $out[$po->id] = [
                'status'      => $agg['status'],
                'items_count' => $po->items->count(),
                'supplier'    => $po->supplier?->name_ar ?: $po->supplier?->name_en ?: '—',
            ];
        }
        return $out;
    }

    /* ===================== طباعة/تصدير ===================== */

    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('purchase_orders.order_date','desc');
        $ids = $q->select('purchase_orders.id')->distinct()->limit(200000)->pluck('purchase_orders.id')->all();

        $uc = new UnitConversionService();
        $statusFilter = $this->normalizeStatuses($r->get('status'));
        $computed = $this->computeOrdersStatusAndSupplier($ids, $uc);

        $filtered = [];
        foreach ($ids as $oid) {
            $st = $computed[$oid]['status'] ?? 'pending';
            if (empty($statusFilter) || in_array($st, $statusFilter, true)) {
                $filtered[] = $oid;
            }
        }

        // حمّل التفاصيل المطلوبة للعرض
        $rows = PO::with(['supplier:id,name_ar,name_en'])
            ->whereIn('id', $filtered)
            ->orderBy('order_date','desc')
            ->get(['id','order_number','order_date','supplier_id']);

        $mapped = $rows->map(function($po) use ($computed){
            return [
                'order_number' => $po->order_number,
                'order_date'   => optional($po->order_date)->format('Y-m-d') ?? (is_string($po->order_date) ? $po->order_date : ''),
                'supplier'     => $po->supplier?->name_ar ?: $po->supplier?->name_en ?: '—',
                'status'       => trans('purchases.supply_orders_report_status_'.($computed[$po->id]['status'] ?? 'pending'))
                                  ?? ($computed[$po->id]['status'] ?? 'pending'),
                'items_count'  => $computed[$po->id]['items_count'] ?? 0,
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    private function buildMeta(Request $r, $rows)
    {
        $supplierNames = [];
        $storeNames = [];
        $productNames = [];
        $categoryNames = [];
        $deptNames = [];
        $statuses = $this->normalizeStatuses($r->get('status'));

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
            'title'          => trans('purchases.supply_orders_report_title'),
            'generated_at'   => Carbon::now()->format('Y-m-d H:i'),
            'date_from'      => $r->get('date_from'),
            'date_to'        => $r->get('date_to'),
            'supplier_names' => $supplierNames,
            'store_names'    => $storeNames,
            'product_names'  => $productNames,
            'category_names' => $categoryNames,
            'department_names'=> $deptNames,
            'statuses'       => $statuses,
            'count'          => $rows->count(),
        ];
    }

    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('purchases.supply_orders_report_th_order_no'),
            trans('purchases.supply_orders_report_th_date'),
            trans('purchases.supply_orders_report_th_supplier'),
            trans('purchases.supply_orders_report_th_status'),
            trans('purchases.supply_orders_report_th_items'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['order_number'],
                $r['order_date'],
                $r['supplier'],
                $r['status'],
                $r['items_count'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','E') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Supply_Orders_' . now()->format('Ymd_His') . '.xlsx';

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
