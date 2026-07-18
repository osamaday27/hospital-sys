<?php

namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

use App\Models\Purchases\PurchaseOrder as PO;
use App\Models\Purchases\PurchaseOrderItem as POItem;

use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\productdata as Product;
use App\models\hr\Department;

// محاضر الاستلام الفعلية (مع ملاحظة الnamespace المرسلة)
use app\models\supply_chain\receivinginspecting as ReceivingDoc;
use App\Models\supply_chain\receivinginspectingitem as ReceivingItem;

// خدمة التحويل بين الوحدات (نفس التي اتفقنا عليها)
use App\Services\SupplyChain\UnitConversionService;

class late_delivery_itemscontroller extends Controller
{
    /** Index: HTML view, AJAX (DataTables), print, export_excel */
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('purchases.reports.late_delivery_items.print', compact('rows', 'meta'));
        }

        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // === AJAX ===
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

            // البحث العام
            $search = $r->input('search.value') ?? $r->get('q');
            if ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('purchase_orders.order_number', 'like', "%{$search}%")
                        ->orWhereExists(function ($s) use ($search) {
                            $s->select(DB::raw(1))
                                ->from('suppliers_data as sd')
                                ->whereColumn('sd.id', 'purchase_orders.supplier_id')
                                ->where(function ($x) use ($search) {
                                    $x->where('sd.name_ar', 'like', "%{$search}%")
                                        ->orWhere('sd.name_en', 'like', "%{$search}%")
                                        ->orWhere('sd.code', 'like', "%{$search}%");
                                });
                        })
                        ->orWhereExists(function ($s) use ($search) {
                            $s->select(DB::raw(1))
                                ->from('purchase_order_items as poi')
                                ->join('products_data as p', 'p.id', '=', 'poi.item_id')
                                ->whereColumn('poi.order_id', 'purchase_orders.id')
                                ->where(function ($x) use ($search) {
                                    $x->where('p.system_code', 'like', "%{$search}%")
                                        ->orWhere('p.name_ar', 'like', "%{$search}%")
                                        ->orWhere('p.name_en', 'like', "%{$search}%");
                                });
                        });
                });
            }

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 2)); // التاريخ
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'purchase_orders.id',
                1 => 'purchase_orders.order_number',
                2 => 'purchase_orders.order_date',
                3 => 'purchase_orders.id',
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'purchase_orders.id';

            // IDs مرتبة وفق نفس ترتيب الاستعلام
            $orderedIds = (clone $q)
                ->select('purchase_orders.id')
                ->distinct()
                ->orderBy($orderBy, $orderDir)
                ->pluck('purchase_orders.id')
                ->all();

            // احسب أوامر “المتأخرة” فعليًا قبل التقسيم
            $uc = new UnitConversionService();
            $delayedCountMap = [];    // [order_id => delayed_items_count]
            $delayedOrderedIds = [];  // نفس ترتيب $orderedIds

            foreach (array_chunk($orderedIds, 200) as $chunk) {
                $pos = PO::with([
                    'items.product:id,system_code,name_ar,name_en',
                    'items.unit.smallUnit:id,name_ar,name_en',
                    'items.unit.largeUnit:id,name_ar,name_en',
                    'supplier:id,name_ar,name_en',
                ])
                    ->whereIn('id', $chunk)
                    ->get();

                foreach ($pos as $po) {
                    $cnt = $this->computeDelayedItemsCountForOrder($po, $uc);
                    if ($cnt > 0) {
                        $delayedCountMap[$po->id] = $cnt;
                    }
                }
            }

            // حافظ على نفس ترتيب IDs الأصلي مع فلترة المتأخر فقط
            foreach ($orderedIds as $oid) {
                if (isset($delayedCountMap[$oid])) $delayedOrderedIds[] = $oid;
            }

            $recordsFiltered = count($delayedOrderedIds);

            // تقسيم الصفحة (دعم length = -1 => الكل)
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            $pageIds = ($length === -1) ? $delayedOrderedIds : array_slice($delayedOrderedIds, $start, $length);

            // حمّل سجلات الصفحة مع الحفاظ على نفس الترتيب
            $rows = collect();
            if (!empty($pageIds)) {
                $idsOrderStr = implode(',', $pageIds);
                $rows = PO::with([
                    'items:id,order_id', // لا نحتاج كل التفاصيل هنا
                    'supplier:id,name_ar,name_en',
                ])
                    ->whereIn('id', $pageIds)
                    ->orderByRaw('FIELD(id, ' . $idsOrderStr . ')')
                    ->get();
            }

            $data = [];
            foreach ($rows as $po) {
                $data[] = [
                    'order_number' => $po->order_number,
                    'order_date'   => optional($po->order_date)->format('Y-m-d') ?? (is_string($po->order_date) ? $po->order_date : ''),
                    'items_html'   => view('purchases.reports.late_delivery_items._items_link', [
                        'id'    => $po->id,
                        'count' => $delayedCountMap[$po->id] ?? 0,
                    ])->render(),
                ];
            }

            // ملاحظة مهمة: ارجع الإجمالي الحقيقي للمتأخرين
            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        // فلاتر
        $stores = Store::select('id', 'manual_code', 'name_ar', 'name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        $products = Product::select('id', 'system_code', 'name_ar', 'name_en')
            ->orderBy('name_ar')->limit(2000)->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => ($p->name_ar ?: $p->name_en ?: (string)$p->id) . ' - ' . ($p->system_code ?? ''),
            ])->values();

        $departments = Department::select('id', 'name_ar', 'name_ne')->orderBy('name_ar')->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name_ar ?: $d->name_ne ?: (string)$d->id,
            ])->values();

        return view('purchases.reports.late_delivery_items.index', [
            'stores'      => $stores,
            'products'    => $products,
            'departments' => $departments,
        ]);
    }

    /** صفحة تفاصيل الأصناف المتأخرة داخل أمر توريد */
    public function show(Request $r, $id)
    {
        $po = PO::query()
            ->with([
                'supplier:id,name_ar,name_en',
                'items' => function ($q) {
                    $q->with([
                        'product:id,system_code,name_ar,name_en',
                        'unit.smallUnit:id,name_ar,name_en',
                        'unit.largeUnit:id,name_ar,name_en',
                    ]);
                },
            ])->findOrFail($id);

        $uc      = new UnitConversionService();
        $today   = Carbon::today();
        $dueDate = $po->order_date
            ? Carbon::parse($po->order_date)->copy()->addDays((int)($po->delivery_period ?? 0))
            : null;

        $recvByProduct = $this->fetchOrderReceiptsGrouped($po->id);

        // أصناف متأخرة فقط (وفق الاستلام الفعلي)
        $items = collect($po->items)->map(function ($it) use ($po, $dueDate, $today, $recvByProduct, $uc) {
            // وحدة العرض: small ثم large ثم أصغر وحدة للنظام
            $displayUnitId = $it->unit?->smallUnit?->id
                ?? $it->unit?->largeUnit?->id
                ?? $uc->smallestUnitId((int)$it->item_id);

            $unitName = $it->unit?->smallUnit?->name_ar
                ?? $it->unit?->largeUnit?->name_ar
                ?? '—';

            // نعتبر requested_qty بوحدة العرض المختارة (كما في أمر التوريد)
            $qtyRequested = (float) $it->requested_qty;

            // تلخيص المستلم الفعلي مع تحويل للوحدة المختارة
            $sum = $this->summarizeDeliveredForItem((int)$it->item_id, $displayUnitId, $recvByProduct, $uc);
            $qtyDelivered = $sum['delivered'];
            $lastAccept   = $sum['last_date']; // Carbon|null

            // حُكم التأخير + أيام التأخير
            $delayed  = false;
            $delayDays = 0;
            if ($dueDate) {
                if ($qtyDelivered + 1e-6 >= $qtyRequested) {
                    // اكتملت الكمية ⇒ تأخير لو كان آخر استلام بعد الاستحقاق
                    if ($lastAccept && $lastAccept->gt($dueDate)) {
                        $delayed   = true;
                        $delayDays = $dueDate->diffInDays($lastAccept);
                    }
                } else {
                    // لم تكتمل ⇒ متأخر إن تجاوزنا الاستحقاق
                    if (Carbon::today()->gt($dueDate)) {
                        $delayed   = true;
                        $delayDays = $dueDate->diffInDays(Carbon::today());
                    }
                }
            }

            return [
                'delayed'       => $delayed,
                'code'          => $it->product?->system_code ?? '—',
                'name'          => ($it->product?->name_ar ?: $it->product?->name_en ?: '—'),
                'unit'          => $unitName,
                'qty_req'       => $qtyRequested,
                'qty_delivered' => $qtyDelivered,
                'order_date'    => optional($po->order_date)->format('Y-m-d') ?? (is_string($po->order_date) ? $po->order_date : ''),
                'delivery_date' => ($qtyDelivered + 1e-6 >= $qtyRequested) && $lastAccept ? $lastAccept->format('Y-m-d') : '—',
                'supplier'      => ($po->supplier?->name_ar ?: $po->supplier?->name_en ?: '—'),
                'delay_days'    => $delayDays,
            ];
        })
            ->filter(fn($x) => $x['delayed'])
            ->values();

        $generatedAt = Carbon::now()->format('Y-m-d H:i');

        return view('purchases.reports.late_delivery_items.show', [
            'po'          => $po,
            'items'       => $items,
            'generatedAt' => $generatedAt,
            'dueDate'     => $dueDate?->format('Y-m-d'),
        ]);
    }

    /** هل هناك أي فلاتر مفعلة؟ */
    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('date_from')      ||
            $r->filled('date_to')        ||
            $r->filled('order_no')       ||
            $r->filled('warehouse_id')   ||
            $r->filled('products')       ||
            $r->filled('department_id')  ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي (فلاتر فقط؛ التحديد النهائي للتأخر يُحسب في PHP بالاستلام الفعلي) */
    private function baseQuery(Request $r)
    {
        $q = PO::query()
            ->leftJoin('purchase_requisitions as pr', 'pr.id', '=', 'purchase_orders.requisition_id');

        // التاريخ على order_date
        if ($r->filled('date_from')) {
            $q->whereDate('purchase_orders.order_date', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('purchase_orders.order_date', '<=', $r->get('date_to'));
        }

        // رقم أمر التوريد
        if ($no = $r->get('order_no')) {
            $q->where('purchase_orders.order_number', 'like', "%{$no}%");
        }

        // المخزن (من طلب التوريد المرتبط)
        $warehouse = $r->get('warehouse_id');
        if (is_array($warehouse) && count($warehouse)) {
            $q->whereIn('pr.warehouse_id', $warehouse);
        } elseif (!empty($warehouse)) {
            $q->where('pr.warehouse_id', $warehouse);
        }

        // القسم
        $dept = $r->get('department_id');
        if (is_array($dept) && count($dept)) {
            $q->whereIn('pr.department_id', $dept);
        } elseif (!empty($dept)) {
            $q->where('pr.department_id', $dept);
        }

        // الأصناف
        $products = $r->get('products');
        if (is_array($products) && count($products)) {
            $q->whereExists(function ($sub) use ($products) {
                $sub->select(DB::raw(1))
                    ->from('purchase_order_items as poi2')
                    ->whereColumn('poi2.order_id', 'purchase_orders.id')
                    ->whereIn('poi2.item_id', $products);
            });
        }

        return $q;
    }

    /** إحضار كل النتائج للطباعة/التصدير (مع حساب عدد الأصناف المتأخرة فعليًا) */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('purchase_orders.order_date', 'desc');
        $orders = $q->select('purchase_orders.*')->distinct('purchase_orders.id')->limit(200000)->get();

        // حمل العناصر
        $orders->load([
            'items.product:id,system_code,name_ar,name_en',
            'items.unit.smallUnit:id,name_ar,name_en',
            'items.unit.largeUnit:id,name_ar,name_en',
            'supplier:id,name_ar,name_en',
        ]);

        $uc = new UnitConversionService();

        $mapped = collect();
        foreach ($orders as $po) {
            $delayedCount = $this->computeDelayedItemsCountForOrder($po, $uc);
            if ($delayedCount <= 0) continue;

            $mapped->push([
                'order_number' => $po->order_number,
                'order_date'   => optional($po->order_date)->format('Y-m-d') ?? (is_string($po->order_date) ? $po->order_date : ''),
                'items_count'  => $delayedCount,
            ]);
        }

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    /** معلومات الهيدر */
    private function buildMeta(Request $r, $rows)
    {
        $storeNames = [];
        $productNames = [];
        $deptNames = [];

        if ($warehouse = $r->get('warehouse_id')) {
            $ids = is_array($warehouse) ? $warehouse : [$warehouse];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        if ($products = $r->get('products')) {
            $ids = is_array($products) ? $products : [$products];
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }
        if ($dept = $r->get('department_id')) {
            $ids = is_array($dept) ? $dept : [$dept];
            $deptNames = Department::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }

        return [
            'title'        => trans('purchases.late_delivery_items_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'order_no'     => $r->get('order_no'),
            'store_names'  => $storeNames,
            'product_names' => $productNames,
            'department_names' => $deptNames,
            'count'        => $rows->count(),
        ];
    }

    /** تصدير Excel */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('purchases.late_delivery_items_th_order_no'),
            trans('purchases.late_delivery_items_th_date'),
            trans('purchases.late_delivery_items_th_items'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['order_number'],
                $r['order_date'],
                $r['items_count'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A', 'C') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Late_Delivery_Items_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /* ===================== Helpers (الاستلام الفعلي + التحويل) ===================== */

    /** لطلب محدد: أجلب محاضر الاستلام مجمّعة حسب الصنف */
    private function fetchOrderReceiptsGrouped(int $orderId): array
    {
        $rows = ReceivingItem::query()
            ->join('receiving_inspecting as ri', 'ri.id', '=', 'receiving_inspecting_items.receiving_inspecting_id')
            ->where('ri.purchase_order_id', $orderId)
            ->where('receiving_inspecting_items.accepted_qty', '>', 0)
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

    /** لخانة واحدة (item_id): لخص كمية المستلم بوحدة عرض معينة + أحدث تاريخ استلام */
    private function summarizeDeliveredForItem(int $productId, ?int $displayUnitId, array $recvByProduct, UnitConversionService $uc): array
    {
        $lines = $recvByProduct[$productId] ?? [];
        $sum = 0.0;
        $lastDate = null;

        foreach ($lines as $ln) {
            $qty = (float) ($ln['qty'] ?? 0);
            $fromUnitId = (int) ($ln['unit_id'] ?? 0);

            if ($qty > 0) {
                if ($displayUnitId && $fromUnitId) {
                    try {
                        $sum += $uc->convert($productId, $qty, $fromUnitId, $displayUnitId);
                    } catch (\Throwable $e) {
                        // تعذر التحويل ⇒ أضف كما هو (أفضل من الفقد)
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

    /** احسب عدد الأصناف المتأخرة فعليًا داخل أمر */
    private function computeDelayedItemsCountForOrder(PO $po, UnitConversionService $uc): int
    {
        $dueDate = $po->order_date
            ? Carbon::parse($po->order_date)->copy()->addDays((int)($po->delivery_period ?? 0))
            : null;

        $recvByProduct = $this->fetchOrderReceiptsGrouped($po->id);

        $count = 0;
        foreach ($po->items as $it) {
            $displayUnitId = $it->unit?->smallUnit?->id
                ?? $it->unit?->largeUnit?->id
                ?? $uc->smallestUnitId((int)$it->item_id);

            $qtyRequested = (float) $it->requested_qty;

            $sum = $this->summarizeDeliveredForItem((int)$it->item_id, $displayUnitId, $recvByProduct, $uc);
            $qtyDelivered = $sum['delivered'];
            $lastAccept   = $sum['last_date'];

            $delayed = false;
            if ($dueDate) {
                if ($qtyDelivered + 1e-6 >= $qtyRequested) {
                    if ($lastAccept && $lastAccept->gt($dueDate)) $delayed = true;
                } else {
                    if (Carbon::today()->gt($dueDate)) $delayed = true;
                }
            }

            if ($delayed) $count++;
        }
        return $count;
    }
}
