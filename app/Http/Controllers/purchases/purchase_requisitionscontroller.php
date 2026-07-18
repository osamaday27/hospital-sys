<?php

namespace App\Http\Controllers\purchases;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\hr\Department;
use App\Traits\web\file_storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\models\supply_chain\productdata;
use App\Models\supply_chain\productunit;
use App\Models\purchases\purchaserequisition;
use App\Models\supply_chain\productstorebalance;
use App\Models\supply_chain\productunit as Unit;
use App\Models\purchases\purchaserequisitionitem;
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\supply_chain\productdata as Product;
use App\Models\supply_chain\storemaindata as Warehouse;
use App\Models\supply_chain\productstorebalance as StoreBalance;
use App\Models\supply_chain\storeproductmodel as StoreProduct;

use App\Traits\UnitConversionHelpers;

class purchase_requisitionscontroller extends Controller
{
    use file_storage, UnitConversionHelpers;

    public function index(Request $request)
    {
        $showPrevious = $request->query('previous') == '1';

        if ($request->ajax()) {
            $query = Purchaserequisition::query();

            if ($showPrevious) {
                $query->whereIn('purchase_requisitions.status', ['order_created', 'closed']);
            } else {
                $query->whereNotIn('purchase_requisitions.status', ['order_created', 'closed']);
            }

            $query
                ->leftJoin('store_main_date as w', 'purchase_requisitions.warehouse_id', '=', 'w.id')
                ->leftJoin('hr_department     as d', 'purchase_requisitions.department_id',  '=', 'd.id')
                ->leftJoin('suppliers_data    as s', 'purchase_requisitions.preferred_supplier_id', '=', 's.id')
                ->select([
                    'purchase_requisitions.id',
                    'purchase_requisitions.requisition_number',
                    'purchase_requisitions.requisition_date',
                    'purchase_requisitions.status',
                    'w.name_ar as warehouse_name_ar',
                    'w.name_en as warehouse_name_en',
                    'd.name_ar as department_name_ar',
                    'd.name_ne as department_name_ne',
                    's.name_ar as supplier_name_ar',
                    's.name_en as supplier_name_en',
                ])
                ->orderByDesc('purchase_requisitions.id');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('status_label', function ($row) {
                    switch ($row->status) {
                        case 'pending':
                            $class = 'bg-warning';
                            $text = __('purchases.status_pending');
                            break;
                        case 'receiving_quotes':
                            $class = 'bg-info';
                            $text = __('purchases.status_receiving_quotes');
                            break;
                        case 'order_created':
                            $class = 'bg-primary';
                            $text = __('purchases.status_order_created');
                            break;
                        case 'closed':
                            $class = 'bg-secondary';
                            $text = __('purchases.status_closed');
                            break;
                        default:
                            return $row->status;
                    }
                    return "<span class=\"badge {$class}\">{$text}</span>";
                })
                ->addColumn('warehouse_name', fn($row) => ($row->warehouse_name_ar ?: '-') . ' / ' . ($row->warehouse_name_en ?: '-'))
                ->addColumn('department_name', fn($row) => ($row->department_name_ar ?: '-') . ' / ' . ($row->department_name_ne ?: '-'))
                ->addColumn('supplier_name', fn($row) => ($row->supplier_name_ar ?: '-') . ' / ' . ($row->supplier_name_en ?: '-'))

                ->addColumn('action', function ($row) {
                    $editUrl           = route('purchase_requisitions.edit', $row->id);
                    $deleteBtn         = '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '"><i class="fa fa-trash"></i> ' . __('purchases.delete') . '</button>';
                    $viewBtn           = '<button class="btn btn-info btn-sm view-btn" data-id="' . $row->id . '"><i class="fa fa-eye"></i> ' . __('purchases.details') . '</button>';
                    $printBtn          = '<a href="' . route('purchase_requisitions.print', $row->id) . '" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i> ' . __('purchases.print') . '</a>';
                    $attachBtn         = '<button class="btn btn-warning btn-sm attach-btn" data-id="' . $row->id . '"><i class="fa fa-paperclip"></i> ' . __('purchases.attach') . '</button>';
                    $priceQuotationUrl = route('price_quotations.index', ['requisition_id' => $row->id]);
                    $priceQBtn         = '<a href="' . $priceQuotationUrl . '" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-edit"></i> ' . __('purchases.price_quotations') . '</a>';

                    $locked = in_array($row->status, ['order_created', 'closed']);

                    $buttons = [];
                    if (!$locked) {
                        // الطلبات الجارية (غير السابقة): أظهر التعديل والحذف
                        $buttons[] = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> ' . __('purchases.edit') . '</a>';
                        $buttons[] = $deleteBtn;
                    }
                    // الأزرار المشتركة
                    $buttons[] = $viewBtn;
                    $buttons[] = $printBtn;
                    $buttons[] = $attachBtn;
                    $buttons[] = $priceQBtn;

                    return implode(' ', $buttons);
                })

                ->rawColumns(['status_label', 'action'])
                ->make(true);
        }

        return view('purchases.programs.purchase_requisitions.index', [
            'showPrevious' => $showPrevious
        ]);
    }

    public function print($id)
    {
        $requisition = purchaserequisition::with([
            'warehouse',
            'department',
            'supplier',
            'items.product',
            'items.unit'
        ])->findOrFail($id);

        $movement_summary = (object)[
            'movement_number' => $requisition->requisition_number,
            'movement_date'   => $requisition->requisition_date,
            'store'           => $requisition->warehouse,
            'department'      => $requisition->department,
            'supplier'        => $requisition->supplier,
            'notes'           => $requisition->reason,
        ];

        $movement_items = $requisition->items;
        $title = __('purchases.print_requisition_title');

        return view('purchases.programs.purchase_requisitions.print_purchase_requisition', compact(
            'movement_summary',
            'movement_items',
            'title'
        ));
    }

    public function attachments(Request $request)
    {
        $request->validate([
            'requisition_id' => 'required|exists:purchase_requisitions,id',
            'attachments.*'  => 'required|file|max:5120',
        ]);

        $req = purchaserequisition::findOrFail($request->requisition_id);
        $paths = $this->file_storage($request->file('attachments'), 'purchases/requisitions/attachments');

        $current = is_array($req->attachments) ? $req->attachments : [];
        $req->attachments = array_merge($current, $paths);
        $req->save();

        return response()->json([
            'success' => true,
            'message' => __('purchases.attachments_uploaded'),
            'attachments' => $req->attachments,
        ]);
    }

    public function deleteAttachment(Request $request)
    {
        $request->validate([
            'requisition_id' => 'required|exists:purchase_requisitions,id',
            'file'           => 'required|string'
        ]);

        $requisition = purchaserequisition::findOrFail($request->requisition_id);
        $files = $requisition->attachments ?? [];
        $fileToDelete = $request->file;

        $files = array_values(array_filter($files, fn($f) => $f !== $fileToDelete));

        if (Storage::disk('public')->exists($fileToDelete)) {
            Storage::disk('public')->delete($fileToDelete);
        }

        $requisition->attachments = $files;
        $requisition->save();

        return response()->json(['success' => true, 'message' => __('purchases.attachment_deleted')]);
    }

    // public function create()
    // {
    //     $last   = PurchaseRequisition::withTrashed()->latest('id')->first();
    //     $nextId = $last ? $last->id + 1 : 1;
    //     $number = 'RQ-' . now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

    //     $warehouses  = Warehouse::where('status', 'active')->get();
    //     $departments = Department::all();
    //     $suppliers   = Supplier::where('status', 'active')->get();

    //     $items = collect();
    //     $unitsMap = [];
    //     $productBalances = [];

    //     return view('purchases.programs.purchase_requisitions.create', compact(
    //         'warehouses',
    //         'departments',
    //         'suppliers',
    //         'number',
    //         'items',
    //         'unitsMap',
    //         'productBalances'
    //     ));
    // }

    /**
     * AJAX: أصناف المخزن المختار + وحداتها + أرصدة + حدود (مخزن/صنف)
     */
    public function create()
{
    // رقم استعراضي فقط — الرقم الفعلي سيُولَّد عند الحفظ
    $nextIdPreview = (DB::table('purchase_requisitions')->max('id') ?? 0) + 1;
    $number = 'RQ-' . now()->format('Ymd') . '-' . str_pad($nextIdPreview, 4, '0', STR_PAD_LEFT);

    $warehouses  = Warehouse::where('status', 'active')->get();
    $departments = Department::all();
    $suppliers   = Supplier::where('status', 'active')->get();

    $items = collect();
    $unitsMap = [];
    $productBalances = [];

    return view('purchases.programs.purchase_requisitions.create', compact(
        'warehouses',
        'departments',
        'suppliers',
        'number',
        'items',
        'unitsMap',
        'productBalances'
    ));
}

    public function itemsByStore(Request $request)
    {
        $storeId = (int) $request->query('store_id');
        if (!$storeId) {
            return response()->json([
                'items' => [],
                'unitsMap' => [],
                'productBalances' => [],
                'limitsMap' => [],
                'storeAmountLimits' => ['min' => null, 'max' => null],
            ]);
        }

        // منتجات هذا المخزن
        $productIds = DB::table('store_products')
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->pluck('product_id')
            ->unique()
            ->values();

        // بيانات المنتجات
        $items = Product::query()
            ->select('id', 'system_code', 'name_ar', 'name_en')
            ->whereIn('id', $productIds)
            ->where('status', 'active')
            ->orderBy('name_ar')
            ->get()
            ->map(function ($p) {
                $p->pending_qty = 0;
                $p->current_balance = 0;
                $p->other_stores_balance = 0;
                $p->last_price = 0;
                return $p;
            });

        // وحدات كل منتج
        $units = Unit::query()
            ->whereIn('product_id', $productIds)
            ->with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->get();

        $unitsMap = [];
        foreach ($units as $u) {
            $unitNameAr = optional($u->smallUnit)->name_ar ?? optional($u->largeUnit)->name_ar ?? '-';
            $unitNameEn = optional($u->smallUnit)->name_en ?? optional($u->largeUnit)->name_en ?? '-';
            $unitsMap[$u->product_id][] = [
                'id'    => $u->id,
                'name'  => "{$unitNameAr} / {$unitNameEn}",
                'price' => $u->cost_price,
            ];
        }

        // أرصدة المنتجات على المخازن النشطة
        $storeIds = Warehouse::where('status', 'active')->pluck('id');
        $balances = productstorebalance::whereIn('product_id', $productIds)
            ->whereIn('store_id', $storeIds)
            ->get();

        $productBalances = [];
        foreach ($balances as $bal) {
            $productBalances[$bal->product_id][$bal->store_id] = $bal->quantity;
        }

        // حدود المخزن (قيمة الطلب) ـ حسب الحقول المطلوبة
        $wh = Warehouse::find($storeId);
        $storeAmountLimits = [
            'min' => data_get($wh, 'min_order_quantity'),
            'max' => data_get($wh, 'max_order_quantity'),
        ];

        // حدود كل صنف (من جدول store_products)
        $spRows = StoreProduct::with([
            'orderMinUnit.smallUnit:id,name_ar,name_en',
            'orderMinUnit.largeUnit:id,name_ar,name_en',
            'orderMaxUnit.smallUnit:id,name_ar,name_en',
            'orderMaxUnit.largeUnit:id,name_ar,name_en',
        ])
            ->where('store_id', $storeId)
            ->whereIn('product_id', $productIds)
            ->get();

        $limitsMap = [];
        foreach ($spRows as $sp) {
            $minName = $sp->orderMinUnit
                ? ($sp->orderMinUnit->smallUnit?->name_ar ?? $sp->orderMinUnit->largeUnit?->name_ar)
                : null;
            $maxName = $sp->orderMaxUnit
                ? ($sp->orderMaxUnit->smallUnit?->name_ar ?? $sp->orderMaxUnit->largeUnit?->name_ar)
                : null;

            $limitsMap[$sp->product_id] = [
                'min_qty' => $sp->order_min_qty,
                'min_unit_name' => $minName,
                'max_qty' => $sp->order_max_qty,
                'max_unit_name' => $maxName,
            ];
        }

        return response()->json([
            'items'              => $items,
            'unitsMap'           => $unitsMap,
            'productBalances'    => $productBalances,
            'limitsMap'          => $limitsMap,
            'storeAmountLimits'  => $storeAmountLimits,
        ]);
    }
public function store(Request $request)
{
    $data = $request->validate([
        'requisition_date'       => 'required|date',
        'warehouse_id'           => 'required|exists:store_main_date,id',
        'department_id'          => 'required|exists:hr_department,id',
        'preferred_supplier_id'  => 'nullable|exists:suppliers_data,id',
        'reason'                 => 'nullable|string',
        'items'                  => 'required|array|min:1',
        'items.*.item_id'        => 'required|exists:products_data,id',
        'items.*.unit_id'        => 'required|exists:product_units,id',
        'items.*.requested_qty'  => 'required|integer|min:1',
        'items.*.last_price'     => 'required|numeric|min:0',
        'items.*.notes'          => 'nullable|string',
    ]);

    // منع تكرار (item_id, unit_id)
    $combos = [];
    foreach ($data['items'] as $i) {
        $key = $i['item_id'] . '-' . $i['unit_id'];
        if (isset($combos[$key])) {
            return back()->withInput()->withErrors(['items' => __('purchases.error_duplicate_item_unit')]);
        }
        $combos[$key] = true;
    }

    // فحص الحدود قبل الحفظ
    [$violations, $orderTotals] = $this->validateRequisitionLimits((int)$data['warehouse_id'], $data['items']);
    if (!empty($violations)) {
        return back()
            ->withInput()
            ->with('limitViolations', $violations)
            ->with('orderTotals', $orderTotals)
            ->with('showLimitModal', true);
    }

    DB::transaction(function () use ($data) {

        // 1) أنشئ السجل برقم مؤقت فريد لتفادي قيد UNIQUE
        $tmpNo = 'RQ-TMP-' . now()->format('YmdHisu') . '-' . mt_rand(1000, 9999);

        $req = purchaserequisition::create([
            'requisition_number'     => $tmpNo, // مؤقت وفريد
            'requisition_date'       => $data['requisition_date'],
            'warehouse_id'           => $data['warehouse_id'],
            'department_id'          => $data['department_id'],
            'preferred_supplier_id'  => $data['preferred_supplier_id'] ?? null,
            'reason'                 => $data['reason'] ?? null,
            'status'                 => 'pending',
            'created_by'             => auth()->id(),
        ]);

        // 2) عدّل الرقم النهائي باستخدام الـ id الحقيقي (مضمون التفرد)
        $finalNo = 'RQ-' . now()->format('Ymd') . '-' . str_pad($req->id, 4, '0', STR_PAD_LEFT);
        $req->update(['requisition_number' => $finalNo]);

        // 3) إعداد أرصدة الأصناف
        $warehouseId = $data['warehouse_id'];
        $balances = StoreBalance::whereIn('product_id', collect($data['items'])->pluck('item_id'))->get();
        $productBalances = [];
        foreach ($balances as $bal) {
            $productBalances[$bal->product_id][$bal->store_id] = $bal->quantity;
        }

        // 4) حفظ الأصناف
        foreach ($data['items'] as $i) {
            $current_balance = $productBalances[$i['item_id']][$warehouseId] ?? 0;
            $other_stores_balance = 0;
            if (isset($productBalances[$i['item_id']])) {
                foreach ($productBalances[$i['item_id']] as $store_id => $qty) {
                    if ($store_id != $warehouseId) $other_stores_balance += $qty;
                }
            }

            purchaserequisitionitem::create([
                'requisition_id'       => $req->id,
                'item_id'              => $i['item_id'],
                'unit_id'              => $i['unit_id'],
                'requested_qty'        => $i['requested_qty'],
                'pending_qty'          => $i['requested_qty'],
                'current_balance'      => $current_balance,
                'other_stores_balance' => $other_stores_balance,
                'last_price'           => $i['last_price'],
                'notes'                => $i['notes'] ?? null,
            ]);
        }
    });

    return redirect()->route('purchase_requisitions.index')
        ->with('success', __('purchases.requisition_created'));
}

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'requisition_date'       => 'required|date',
    //         'warehouse_id'           => 'required|exists:store_main_date,id',
    //         'department_id'          => 'required|exists:hr_department,id',
    //         'preferred_supplier_id'  => 'nullable|exists:suppliers_data,id',
    //         'reason'                 => 'nullable|string',
    //         'items'                  => 'required|array|min:1',
    //         'items.*.item_id'        => 'required|exists:products_data,id',
    //         'items.*.unit_id'        => 'required|exists:product_units,id',
    //         'items.*.requested_qty'  => 'required|integer|min:1',
    //         'items.*.last_price'     => 'required|numeric|min:0',
    //         'items.*.notes'          => 'nullable|string',
    //     ]);

    //     // منع التكرار
    //     $combos = [];
    //     foreach ($data['items'] as $i) {
    //         $key = $i['item_id'] . '-' . $i['unit_id'];
    //         if (isset($combos[$key])) {
    //             return back()->withInput()->withErrors(['items' => __('purchases.error_duplicate_item_unit')]);
    //         }
    //         $combos[$key] = true;
    //     }

    //     // فحص الحدود قبل الحفظ
    //     [$violations, $orderTotals] = $this->validateRequisitionLimits((int)$data['warehouse_id'], $data['items']);

    //     if (!empty($violations)) {
    //         return back()
    //             ->withInput()
    //             ->with('limitViolations', $violations)
    //             ->with('orderTotals', $orderTotals)
    //             ->with('showLimitModal', true);
    //     }

    //     // إنشاء الطلب
    //     $last   = purchaserequisition::latest('id')->first();
    //     $nextId = $last ? $last->id + 1 : 1;
    //     $number = 'RQ-' . Carbon::now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

    //     $req = purchaserequisition::create([
    //         'requisition_number'     => $number,
    //         'requisition_date'       => $data['requisition_date'],
    //         'warehouse_id'           => $data['warehouse_id'],
    //         'department_id'          => $data['department_id'],
    //         'preferred_supplier_id'  => $data['preferred_supplier_id'] ?? null,
    //         'reason'                 => $data['reason'] ?? null,
    //         'status'                 => 'pending',
    //         'created_by'             => auth()->id(),
    //     ]);

    //     // أرصدة
    //     $warehouseId = $data['warehouse_id'];
    //     $balances = StoreBalance::whereIn('product_id', collect($data['items'])->pluck('item_id'))->get();
    //     $productBalances = [];
    //     foreach ($balances as $bal) {
    //         $productBalances[$bal->product_id][$bal->store_id] = $bal->quantity;
    //     }

    //     foreach ($data['items'] as $i) {
    //         $current_balance = $productBalances[$i['item_id']][$warehouseId] ?? 0;
    //         $other_stores_balance = 0;
    //         if (isset($productBalances[$i['item_id']])) {
    //             foreach ($productBalances[$i['item_id']] as $store_id => $qty) {
    //                 if ($store_id != $warehouseId) $other_stores_balance += $qty;
    //             }
    //         }

    //         purchaserequisitionitem::create([
    //             'requisition_id'       => $req->id,
    //             'item_id'              => $i['item_id'],
    //             'unit_id'              => $i['unit_id'],
    //             'requested_qty'        => $i['requested_qty'],
    //             'pending_qty'          => $i['requested_qty'],
    //             'current_balance'      => $current_balance,
    //             'other_stores_balance' => $other_stores_balance,
    //             'last_price'           => $i['last_price'],
    //             'notes'                => $i['notes'] ?? null,
    //         ]);
    //     }

    //     return redirect()->route('purchase_requisitions.index')
    //         ->with('success', __('purchases.requisition_created'));
    // }

    public function update(Request $request, $id)
    {
        $req = purchaserequisition::findOrFail($id);

        if (in_array($req->status, ['order_created', 'closed'])) {
            return redirect()
                ->route('purchase_requisitions.index', ['previous' => 1])
                ->with('error', 'لا يمكن تعديل طلب تم إنشـاء أمر شراء له أو تم إغلاقه.');
        }
        $data = $request->validate([
            'requisition_date'       => 'required|date',
            'warehouse_id'           => 'required|exists:store_main_date,id',
            'department_id'          => 'required|exists:hr_department,id',
            'preferred_supplier_id'  => 'nullable|exists:suppliers_data,id',
            'reason'                 => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.item_id'        => 'required|exists:products_data,id',
            'items.*.unit_id'        => 'required|exists:product_units,id',
            'items.*.requested_qty'  => 'required|integer|min:1',
            'items.*.last_price'     => 'required|numeric|min:0',
            'items.*.notes'          => 'nullable|string',
        ]);

        // منع التكرار
        $combos = [];
        foreach ($data['items'] as $i) {
            $key = $i['item_id'] . '-' . $i['unit_id'];
            if (isset($combos[$key])) {
                return back()->withInput()->withErrors(['items' => __('purchases.error_duplicate_item_unit')]);
            }
            $combos[$key] = true;
        }

        // فحص الحدود قبل الحفظ
        [$violations, $orderTotals] = $this->validateRequisitionLimits((int)$data['warehouse_id'], $data['items']);

        if (!empty($violations)) {
            return back()
                ->withInput()
                ->with('limitViolations', $violations)
                ->with('orderTotals', $orderTotals)
                ->with('showLimitModal', true);
        }

        // تحديث الطلب
        $req->update([
            'requisition_date'       => $data['requisition_date'],
            'warehouse_id'           => $data['warehouse_id'],
            'department_id'          => $data['department_id'],
            'preferred_supplier_id'  => $data['preferred_supplier_id'] ?? null,
            'reason'                 => $data['reason'] ?? null,
            'updated_by'             => auth()->id(),
        ]);

        $req->items()->delete();
        $productIds = collect($data['items'])->pluck('item_id');
        $warehouseId = $data['warehouse_id'];

        $balances = productstorebalance::whereIn('product_id', $productIds)->get();
        $productBalances = [];
        foreach ($balances as $bal) {
            $productBalances[$bal->product_id][$bal->store_id] = $bal->quantity;
        }

        foreach ($data['items'] as $i) {
            $current_balance = $productBalances[$i['item_id']][$warehouseId] ?? 0;
            $other_stores_balance = 0;
            if (isset($productBalances[$i['item_id']])) {
                foreach ($productBalances[$i['item_id']] as $store_id => $qty) {
                    if ($store_id != $warehouseId) $other_stores_balance += $qty;
                }
            }

            purchaserequisitionitem::create([
                'requisition_id'       => $req->id,
                'item_id'              => $i['item_id'],
                'unit_id'              => $i['unit_id'],
                'requested_qty'        => $i['requested_qty'],
                'pending_qty'          => $i['requested_qty'],
                'current_balance'      => $current_balance,
                'other_stores_balance' => $other_stores_balance,
                'last_price'           => $i['last_price'],
                'notes'                => $i['notes'] ?? null,
            ]);
        }

        return redirect()->route('purchase_requisitions.index')
            ->with('success', __('purchases.requisition_updated'));
    }

    public function show($id)
    {
        $requisition = purchaserequisition::with([
            'warehouse',
            'department',
            'supplier',
            'items'
        ])->findOrFail($id);

        $details = [
            __('purchases.requisition_number') => $requisition->requisition_number,
            __('purchases.requisition_date')   => $requisition->requisition_date,
            __('purchases.status')             => $requisition->status,
            __('purchases.warehouse')          => $requisition->warehouse ? $requisition->warehouse->name_ar . ' / ' . $requisition->warehouse->name_en : '-',
            __('purchases.department')         => $requisition->department ? $requisition->department->name_ar . ' / ' . $requisition->department->name_ne : '-',
            __('purchases.supplier')           => $requisition->supplier ? $requisition->supplier->name_ar . ' / ' . $requisition->supplier->name_en : '-',
            __('purchases.reason')             => $requisition->reason ?: '-',
        ];

        $itemsTable = '<table class="table table-sm table-bordered"><thead>
            <tr>
              <th>' . __('purchases.item') . '</th>
              <th>' . __('purchases.unit') . '</th>
              <th>' . __('purchases.requested_qty') . '</th>
              <th>' . __('purchases.last_price') . '</th>
            </tr>
            </thead><tbody>';
        foreach ($requisition->items as $item) {
            $itemName = productdata::find($item->item_id);
            $itemNameStr = $itemName ? ($itemName->name_ar . ' / ' . $itemName->name_en) : '-';

            $unitRow = productunit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])->find($item->unit_id);
            $unitName = '-';
            if ($unitRow) {
                $unitName = $unitRow->smallUnit
                    ? ($unitRow->smallUnit->name_ar . ' / ' . $unitRow->smallUnit->name_en)
                    : ($unitRow->largeUnit
                        ? ($unitRow->largeUnit->name_ar . ' / ' . $unitRow->largeUnit->name_en)
                        : '-');
            }

            $itemsTable .= '<tr>
                <td>' . e($itemNameStr) . '</td>
                <td>' . e($unitName) . '</td>
                <td>' . e($item->requested_qty) . '</td>
                <td>' . e(number_format($item->last_price, 2)) . '</td>
              </tr>';
        }
        $itemsTable .= '</tbody></table>';
        $details[__('purchases.items')] = $itemsTable;

        $attachments = [];
        if ($requisition->attachments && is_array($requisition->attachments)) {
            $attachments = $requisition->attachments;
        } elseif ($requisition->attachments && is_string($requisition->attachments)) {
            $attachments = json_decode($requisition->attachments, true) ?: [];
        }

        return response()->json([
            'details' => $details,
            'attachments' => $attachments
        ]);
    }

    public function edit($id)
    {
        $req = purchaserequisition::with([
            'items.product',
            'items.unit.smallUnit',
            'items.unit.largeUnit'
        ])->findOrFail($id);

        if (in_array($req->status, ['order_created', 'closed'])) {
            return redirect()
                ->route('purchase_requisitions.index', ['previous' => 1])
                ->with('error', 'لا يمكن تعديل طلب تم إنشاء أمر شراء له أو تم إغلاقه.');
        }


        $warehouses  = Warehouse::where('status', 'active')->get();
        $departments = Department::all();
        $suppliers   = Supplier::where('status', 'active')->get();

        $storeId = (int) $req->warehouse_id;

        $allowedProductIds = DB::table('store_products')
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->pluck('product_id');

        $allowedProductIds = $allowedProductIds ?: collect([]);

        $items = Product::query()
            ->select('id', 'system_code', 'name_ar', 'name_en')
            ->whereIn('id', $allowedProductIds)
            ->where('status', 'active')
            ->orderBy('name_ar')
            ->get()
            ->map(function ($p) {
                $p->pending_qty          = 0;
                $p->current_balance      = 0;
                $p->other_stores_balance = 0;
                $p->last_price           = 0;
                return $p;
            });

        $editItemIds    = $req->items->pluck('item_id');
        $unitProductIds = $allowedProductIds->merge($editItemIds)->unique();

        $units = Unit::query()
            ->whereIn('product_id', $unitProductIds)
            ->with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->get();

        $unitsMap = [];
        foreach ($units as $u) {
            $nameAr = optional($u->smallUnit)->name_ar ?? optional($u->largeUnit)->name_ar ?? '-';
            $nameEn = optional($u->smallUnit)->name_en ?? optional($u->largeUnit)->name_en ?? '-';
            $unitsMap[$u->product_id][] = [
                'id'    => $u->id,
                'name'  => "$nameAr / $nameEn",
                'price' => $u->cost_price,
            ];
        }

        $storeIds = $warehouses->pluck('id');
        $balances = StoreBalance::whereIn('product_id', $unitProductIds)
            ->whereIn('store_id', $storeIds)
            ->get();

        $productBalances = [];
        foreach ($balances as $bal) {
            $productBalances[$bal->product_id][$bal->store_id] = $bal->quantity;
        }

        $oldItems = $req->items->map(function ($item) use ($unitsMap) {
            $prod = $item->product;
            return [
                'id'                   => $item->item_id,
                'code'                 => $prod->system_code ?? '',
                'name_ar'              => $prod->name_ar    ?? '',
                'name_en'              => $prod->name_en    ?? '',
                'pending_qty'          => $item->pending_qty,
                'requested_qty'        => $item->requested_qty,
                'units'                => $unitsMap[$item->item_id] ?? [],
                'unit_id'              => $item->unit_id,
                'last_price'           => $item->last_price,
                'current_balance'      => $item->current_balance,
                'other_stores_balance' => $item->other_stores_balance,
                'notes'                => $item->notes,
            ];
        })->values();

        return view('purchases.programs.purchase_requisitions.edit', compact(
            'req',
            'warehouses',
            'departments',
            'suppliers',
            'items',
            'unitsMap',
            'productBalances',
            'oldItems'
        ));
    }

    public function destroy($id)
    {
        $req = purchaserequisition::find($id);

        if (!$req) {
            return response()->json([
                'success' => false,
                'message' => __('purchases.requisition_not_found')
            ], 404);
        }

        if (in_array($req->status, ['order_created', 'closed'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف طلب تم إنشاء أمر شراء له أو تم إغلاقه.'
            ], 403);
        }

        $req->items()->delete();
        $req->delete();

        return response()->json([
            'success' => true,
            'message' => __('purchases.requisition_deleted')
        ]);
    }


    /* ============ Helpers للفحص ============ */

    protected function effectiveUnitsIdFromProductUnit(int $productUnitId): ?int
    {
        $pu = Unit::with(['smallUnit:id', 'largeUnit:id'])->find($productUnitId);
        if (!$pu) return null;
        return $pu->smallUnit?->id ?? $pu->largeUnit?->id ?? null;
    }

    protected function toBaseViaProductUnit(int $productId, float $qty, int $productUnitId): ?float
    {
        $unitsId = $this->effectiveUnitsIdFromProductUnit($productUnitId); // units.id
        if (!$unitsId) return null;
        return $this->ucToBase($productId, $qty, $unitsId);
    }

    /**
     * فحص حدود الطلب:
     * - حدود كمية لكل صنف (store_products) — مع ذكر اسم الصنف وكوده والحدود بوحداتها
     * - حدود إجمالي المبلغ للمخزن (min_order_quantity/max_order_quantity) كقيمة طلب
     * يعيد: [violations[], orderTotals[]]
     */
    protected function validateRequisitionLimits(int $warehouseId, array $items): array
    {
        $violations = [];

        $warehouse = Warehouse::find($warehouseId);

        // <<< استخدم الحقول الصحيحة كما طلبت
        $minAmount = data_get($warehouse, 'min_order_quantity'); // إجمالي قيمة الطلب الأدنى (على مستوى المخزن)
        $maxAmount = data_get($warehouse, 'max_order_quantity'); // إجمالي قيمة الطلب الأقصى (على مستوى المخزن)

        // إجمالي المبلغ للطلب
        $totalAmount = 0.0;
        foreach ($items as $i) {
            $totalAmount += (float)$i['requested_qty'] * (float)$i['last_price'];
        }

        // مبلغ المخزن (إن كانت محددة)
        if (!is_null($minAmount) && $minAmount !== '' && $minAmount > 0 && $totalAmount < $minAmount) {
            $violations[] = __('purchases.limit_total_amount_min_store', [
                'total' => number_format($totalAmount, 2),
                'min'   => number_format($minAmount, 2),
            ]);
        }
        if (!is_null($maxAmount) && $maxAmount !== '' && $maxAmount > 0 && $totalAmount > $maxAmount) {
            $violations[] = __('purchases.limit_total_amount_max_store', [
                'total' => number_format($totalAmount, 2),
                'max'   => number_format($maxAmount, 2),
            ]);
        }

        // جلب بيانات الربط بالصنف + أسماء الوحدات + بيانات الصنف نفسه للعرض
        $productIds = collect($items)->pluck('item_id')->unique()->values();

        $spRows = StoreProduct::with([
            'product:id,system_code,name_ar,name_en',
            'orderMinUnit.smallUnit:id,name_ar,name_en',
            'orderMinUnit.largeUnit:id,name_ar,name_en',
            'orderMaxUnit.smallUnit:id,name_ar,name_en',
            'orderMaxUnit.largeUnit:id,name_ar,name_en',
        ])
            ->where('store_id', $warehouseId)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        foreach ($items as $i) {
            $pid     = (int)$i['item_id'];
            $puId    = (int)$i['unit_id'];         // product_units.id
            $qty     = (float)$i['requested_qty'];

            $requestedBase = $this->toBaseViaProductUnit($pid, $qty, $puId);

            if (!isset($spRows[$pid]) || is_null($requestedBase)) {
                continue;
            }

            $sp   = $spRows[$pid];
            $prod = $sp->product;
            $itemLabel = $prod
                ? (($prod->system_code ? $prod->system_code . ' - ' : '') . ($prod->name_ar ?? '') . ' / ' . ($prod->name_en ?? ''))
                : ('#' . $pid);

            // حد أدنى
            if (!is_null($sp->order_min_qty) && !is_null($sp->order_min_unit_id)) {
                $minBase = $this->toBaseViaProductUnit($pid, (float)$sp->order_min_qty, (int)$sp->order_min_unit_id);

                $minUnitName = $sp->orderMinUnit
                    ? ($sp->orderMinUnit->smallUnit?->name_ar ?? $sp->orderMinUnit->largeUnit?->name_ar)
                    : null;

                if (!is_null($minBase) && $requestedBase < $minBase) {
                    $violations[] = __('purchases.limit_item_min_qty_verbose', [
                        'item'    => $itemLabel,
                        'min_val' => (string)$sp->order_min_qty,
                        'min_unit' => (string)($minUnitName ?? ''),
                    ]);
                }
            }

            // حد أقصى
            if (!is_null($sp->order_max_qty) && !is_null($sp->order_max_unit_id)) {
                $maxBase = $this->toBaseViaProductUnit($pid, (float)$sp->order_max_qty, (int)$sp->order_max_unit_id);

                $maxUnitName = $sp->orderMaxUnit
                    ? ($sp->orderMaxUnit->smallUnit?->name_ar ?? $sp->orderMaxUnit->largeUnit?->name_ar)
                    : null;

                if (!is_null($maxBase) && $requestedBase > $maxBase) {
                    $violations[] = __('purchases.limit_item_max_qty_verbose', [
                        'item'    => $itemLabel,
                        'max_val' => (string)$sp->order_max_qty,
                        'max_unit' => (string)($maxUnitName ?? ''),
                    ]);
                }
            }
        }

        $orderTotals = [
            'total_amount' => $totalAmount,
            'min_amount'   => $minAmount,
            'max_amount'   => $maxAmount,
        ];

        return [$violations, $orderTotals];
    }
}
