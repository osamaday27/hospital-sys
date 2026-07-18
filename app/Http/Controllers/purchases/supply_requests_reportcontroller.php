<?php

namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

// ====== Models ======
use App\Models\purchases\purchaserequisition as Requisition;
use App\Models\purchases\purchaserequisitionitem as RequisitionItem;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\productdata as Product;
use App\models\hr\Department;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\supply_chain\productstorebalance as Balance;

// ====== Services (للتحويل بين الوحدات) ======
use App\Services\SupplyChain\UnitConversionService;

class supply_requests_reportcontroller extends Controller
{
    /** Index: HTML view, AJAX (DataTables), print, export_excel */
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // === Print view for list ===
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('purchases.reports.supply_requests_report.print', compact('rows', 'meta'));
        }

        // === Export Excel for list ===
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

            // البحث العام
            $search = $r->input('search.value') ?? $r->get('q');
            if ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('requisition_number', 'like', "%{$search}%")
                      ->orWhere('reason', 'like', "%{$search}%")
                      ->orWhereHas('warehouse', fn($wh) =>
                            $wh->where('name_ar','like',"%{$search}%")
                               ->orWhere('name_en','like',"%{$search}%")
                               ->orWhere('manual_code','like',"%{$search}%"))
                      ->orWhereHas('department', fn($d) =>
                            $d->where('name_ar','like',"%{$search}%")
                              ->orWhere('name_ne','like',"%{$search}%"))
                      ->orWhereHas('creator', fn($u) =>
                            $u->where('name','like',"%{$search}%"))
                      ->orWhereHas('items.item', fn($p) =>
                            $p->where('system_code','like',"%{$search}%")
                              ->orWhere('name_ar','like',"%{$search}%")
                              ->orWhere('name_en','like',"%{$search}%"));
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب آمن
            $orderColumnIndex = (int)($r->input('order.0.column', 2)); // التاريخ افتراضياً
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'id',                    // مسلسل
                1 => 'requisition_number',    // رقم الطلب
                2 => 'requisition_date',      // التاريخ
                3 => 'status',                // الحالة
                4 => 'reason',                // السبب
                5 => 'created_by',            // المستخدم
                6 => 'id',                    // الأصناف (ثابت)
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'id';
            $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            // جلب الصفحة
            $rows = $q->skip($start)->take($length)->get();

            $data = [];
            foreach ($rows as $row) {
                $data[] = [
                    'requisition_number' => $row->requisition_number,
                    'requisition_date'   => optional($row->requisition_date)->format('Y-m-d') ?? (is_string($row->requisition_date) ? $row->requisition_date : ''),
                    'status'             => $row->status,
                    'reason'             => $row->reason,
                    'creator'            => $row->creator?->name ?? '—',
                    'items_html'         => view('purchases.reports.supply_requests_report._items_link', [
                        'id' => $row->id,
                        'count' => $row->items_count ?? $row->items->count(),
                    ])->render(),
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

        $departments = Department::select('id','name_ar','name_ne')->orderBy('name_ar')->get()
            ->map(fn($d)=>[
                'id'=>$d->id,
                'name'=> $d->name_ar ?: $d->name_ne ?: (string)$d->id,
            ])->values();

        $statuses = ['pending','receiving_quotes','order_created','closed'];

        return view('purchases.reports.supply_requests_report.index', [
            'stores'      => $stores,
            'products'    => $products,
            'departments' => $departments,
            'statuses'    => $statuses,
        ]);
    }

    /** صفحة تفاصيل الأصناف لكل طلب (تبويب جديد + طباعة) */
    public function show(Request $r, $id)
    {
        $req = Requisition::with([
            'warehouse:id,manual_code,name_ar,name_en',
            'department:id,name_ar,name_ne',
            'supplier:id,name_ar,name_en',
            'creator:id,name',
            'items' => function($qi){
                $qi->with([
                    'item:id,system_code,name_ar,name_en',
                    'unit.smallUnit:id,name_ar,name_en',
                    'unit.largeUnit:id,name_ar,name_en',
                ]);
            },
        ])->findOrFail($id);

        $uc = new UnitConversionService();

        // الرصيد الحالي (المخزن المحدد) + الرصيد بكل المخازن، نفس وحدة العرض
        $items = $req->items->map(function($it) use ($req, $uc){
            // وحدة العرض: نحاول small ثم large، وإلا أصغر وحدة أساس
            $displayUnitId = $it->unit?->smallUnit?->id
                           ?? $it->unit?->largeUnit?->id
                           ?? $uc->smallestUnitId((int)$it->item_id);

            $unitName = $it->unit?->smallUnit?->name_ar
                      ?? $it->unit?->largeUnit?->name_ar
                      ?? null;

            // الرصيد في مخزن الطلب
            $balanceStore = $this->calcCurrentBalanceAccurate(
                (int)$it->item_id,
                (int)$req->warehouse_id,
                $displayUnitId,
                $uc
            );

            // الرصيد في جميع المخازن (بدون فلتر store_id)
            $balanceAll = $this->calcCurrentBalanceAccurate(
                (int)$it->item_id,
                null, // all stores
                $displayUnitId,
                $uc
            );

            return [
                'code'         => $it->item?->system_code ?? '—',
                'name'         => ($it->item?->name_ar ?: $it->item?->name_en ?: '—'),
                'unit'         => $unitName ?: '—',
                'qty'          => (float)$it->requested_qty,
                'price'        => $it->last_price !== null ? (float)$it->last_price : null,
                'supplier'     => ($req->supplier?->name_ar ?: $req->supplier?->name_en ?: '—'),
                'balance'      => (float)$balanceStore,
                'balance_all'  => (float)$balanceAll,
                'notes'        => $it->notes,
            ];
        });

        $generatedAt = Carbon::now()->format('Y-m-d H:i');

        return view('purchases.reports.supply_requests_report.show', [
            'req'         => $req,
            'items'       => $items,
            'generatedAt' => $generatedAt,
        ]);
    }

    /** هل هناك أي فلاتر مفعلة؟ */
    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('date_from')      ||
            $r->filled('date_to')        ||
            $r->filled('requisition_no') ||
            $r->filled('warehouse_id')   ||
            $r->filled('products')       ||
            $r->filled('department_id')  ||
            $r->filled('status')         ||
            $r->filled('statuses')       ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي */
    private function baseQuery(Request $r)
    {
        $q = Requisition::query()
            ->with([
                'warehouse:id,manual_code,name_ar,name_en',
                'department:id,name_ar,name_ne',
                'creator:id,name',
                'items:id,requisition_id',
            ])
            ->withCount('items');

        // التاريخ (على requisition_date)
        if ($r->filled('date_from')) {
            $q->whereDate('requisition_date', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('requisition_date', '<=', $r->get('date_to'));
        }

        // رقم الطلب
        if ($no = $r->get('requisition_no')) {
            $q->where('requisition_number', 'like', "%{$no}%");
        }

        // المخزن
        $warehouse = $r->get('warehouse_id');
        if (is_array($warehouse) && count($warehouse)) {
            $q->whereIn('warehouse_id', $warehouse);
        } elseif (!empty($warehouse)) {
            $q->where('warehouse_id', $warehouse);
        }

        // القسم
        $dept = $r->get('department_id');
        if (is_array($dept) && count($dept)) {
            $q->whereIn('department_id', $dept);
        } elseif (!empty($dept)) {
            $q->where('department_id', $dept);
        }

        // الحالة
        $sts = $r->get('statuses') ?? $r->get('status');
        if (is_array($sts) && count($sts)) {
            $q->whereIn('status', $sts);
        } elseif (!empty($sts)) {
            $q->where('status', $sts);
        }

        // الأصناف
        $products = $r->get('products');
        if (is_array($products) && count($products)) {
            $q->whereHas('items', function ($w) use ($products) {
                $w->whereIn('item_id', $products);
            });
        }

        return $q;
    }

    /** إحضار كل النتائج للطباعة/التصدير (قائمة الطلبات) */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('requisition_date', 'desc');
        $rows = $q->limit(200000)->get();

        $mapped = $rows->map(function ($row) {
            return [
                'requisition_number' => $row->requisition_number,
                'requisition_date'   => optional($row->requisition_date)->format('Y-m-d') ?? (is_string($row->requisition_date) ? $row->requisition_date : ''),
                'status'             => $row->status,
                'reason'             => $row->reason,
                'creator'            => $row->creator?->name ?? '—',
                'items_count'        => $row->items_count ?? $row->items->count(),
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    /** معلومات الهيدر للطباعة/التصدير */
    private function buildMeta(Request $r, $rows)
    {
        $storeNames = [];
        $productNames = [];
        $deptNames = [];

        if ($warehouse = $r->get('warehouse_id')) {
            $ids = is_array($warehouse)? $warehouse : [$warehouse];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }
        if ($products = $r->get('products')) {
            $ids = is_array($products)? $products : [$products];
            $productNames = Product::whereIn('id', $ids)->limit(50)->pluck('name_ar')->toArray();
        }
        if ($dept = $r->get('department_id')) {
            $ids = is_array($dept)? $dept : [$dept];
            $deptNames = Department::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }

        $statuses = $r->get('statuses') ?? $r->get('status');
        $statuses = is_array($statuses) ? $statuses : (empty($statuses) ? [] : [$statuses]);

        return [
            'title'        => trans('purchases.supply_requests_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'requisition_no' => $r->get('requisition_no'),
            'store_names'  => $storeNames,
            'product_names'=> $productNames,
            'department_names'=> $deptNames,
            'statuses'     => $statuses,
            'count'        => $rows->count(),
        ];
    }

    /** تصدير Excel (قائمة الطلبات) */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('purchases.supply_requests_report_th_no'),
            trans('purchases.supply_requests_report_th_date'),
            trans('purchases.supply_requests_report_th_status'),
            trans('purchases.supply_requests_report_th_reason'),
            trans('purchases.supply_requests_report_th_creator'),
            trans('purchases.supply_requests_report_th_items'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['requisition_number'],
                $r['requisition_date'],
                $r['status'],
                $r['reason'],
                $r['creator'],
                $r['items_count'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','F') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Supply_Requests_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * حساب رصيد صنف بدقة (مجمّع على جميع الباتشات)
     * - يحوّل كل سطر من product_store_balances إلى "الوحدة الأصغر" ثم يجمع
     * - ثم يحوّل الناتج إلى وحدة الهدف (إن زُوّدت)
     * - $storeId=null ⇒ جميع المخازن
     */
    private function calcCurrentBalanceAccurate(int $productId, ?int $storeId, ?int $targetUnitId, UnitConversionService $uc): float
    {
        // اجلب صفوف الرصيد المطلوبة
        $q = Balance::query()->where('product_id', $productId);
        if ($storeId) $q->where('store_id', $storeId);

        // نحسب بتحويل كل سطر للأساس لتجنّب أي عدم اتساق بعمود quantity_in_base_unit
        $rows = $q->get(['units_id','quantity','quantity_in_base_unit']);

        $sumBase = 0.0;
        foreach ($rows as $r) {
            $unitsId = (int) ($r->units_id ?? 0);
            $qty     = (float) ($r->quantity ?? 0);

            if ($unitsId && $qty > 0) {
                try {
                    $sumBase += $uc->toBase($productId, $qty, $unitsId);
                } catch (\Throwable $e) {
                    $sumBase += (float) ($r->quantity_in_base_unit ?? 0);
                }
            } else {
                $sumBase += (float) ($r->quantity_in_base_unit ?? 0);
            }
        }

        // تحويل إلى وحدة الهدف إن لزم
        if ($targetUnitId) {
            try {
                return (float) $uc->fromBase($productId, $sumBase, $targetUnitId);
            } catch (\Throwable $e) {
                return (float) $sumBase;
            }
        }

        return (float) $sumBase;
    }
}
