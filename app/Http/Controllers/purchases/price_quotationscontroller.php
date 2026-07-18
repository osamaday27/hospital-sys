<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchases\PriceQuotation;
use App\Models\Purchases\PriceQuotationItem;
use App\Models\Purchases\PurchaseRequisition;
use App\Models\Supply_Chain\ProductData;
use App\Models\Supply_Chain\ProductUnit;
use App\Models\Suppliers\Suppliersdata;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// للتحويل بين الوحدات
use App\Services\SupplyChain\UnitConversionService;

class price_quotationscontroller extends Controller
{
    /** حوّل product_units.id إلى units.id مع مراعاة قاعدة العرض في إعدادات المخزن:
     *  - للعنصر نفسه في جدول product_units عند اختيار الوحدة في عروض الأسعار: نأخذ الصغيرة إن وُجدت، وإلا الكبيرة.
     */
    protected function unitIdFromProductUnitForPQ(ProductUnit $pu): ?int
    {
        if ($pu->unit_small_id) return (int)$pu->unit_small_id;
        if ($pu->unit_large_id) return (int)$pu->unit_large_id;
        return null;
    }

    /** حوّل product_units.id إلى units.id مع مراعاة قاعدة (أول سجل كبير، والباقي صغير إن وُجد) كما في إعدادات المخزن */
    protected function unitIdFromProductUnitForStoreLimits(ProductUnit $pu, int $firstPuIdForProduct): ?int
    {
        if ($pu->id === $firstPuIdForProduct) {
            // أول سجل: الكبيرة
            return $pu->unit_large_id ? (int)$pu->unit_large_id : ($pu->unit_small_id ? (int)$pu->unit_small_id : null);
        }
        // غير الأول: الصغيرة إن وُجدت، وإلا الكبيرة
        if ($pu->unit_small_id) return (int)$pu->unit_small_id;
        if ($pu->unit_large_id) return (int)$pu->unit_large_id;
        return null;
    }

    /** ابنِ خريطة حدود الأصناف للمخزن: product_id => [min_qty,min_unit_name,max_qty,max_unit_name] */
    protected function buildProductLimitsMap(int $storeId, array $productIds): array
    {
        if (empty($productIds)) return [];

        // سطور ربط المخزن بالأصناف
        $rows = DB::table('store_products')
            ->where('store_id', $storeId)
            ->whereIn('product_id', $productIds)
            ->get([
                'product_id',
                'order_min_qty',
                'order_min_unit_id',
                'order_max_qty',
                'order_max_unit_id',
            ]);

        if ($rows->isEmpty()) return [];

        // اجمع كل product_units.id اللازمة
        $puIds = [];
        foreach ($rows as $r) {
            if ($r->order_min_unit_id) $puIds[] = (int)$r->order_min_unit_id;
            if ($r->order_max_unit_id) $puIds[] = (int)$r->order_max_unit_id;
        }
        $puIds = array_values(array_unique($puIds));

        // احضر product_units لهذه المنتجات، واحسب أول record لكل منتج
        $allPu = ProductUnit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->whereIn('product_id', $productIds)
            ->orderBy('product_id')->orderBy('id')
            ->get(['id', 'product_id', 'unit_large_id', 'unit_small_id']);

        $firstPuByProduct = [];
        foreach ($allPu as $pu) {
            if (!isset($firstPuByProduct[$pu->product_id])) {
                $firstPuByProduct[$pu->product_id] = $pu->id;
            }
        }

        // خريطة للوصول السريع
        $puById = $allPu->keyBy('id');

        $out = [];
        foreach ($rows as $r) {
            $pid = (int)$r->product_id;

            $minName = $maxName = null;

            if ($r->order_min_unit_id && isset($puById[$r->order_min_unit_id])) {
                $pu = $puById[$r->order_min_unit_id];
                // قاعدة "أول سجل كبير والباقي صغير"
                $unitId = $this->unitIdFromProductUnitForStoreLimits($pu, $firstPuByProduct[$pid] ?? $pu->id);
                $minName = $pu->smallUnit && $unitId === (int)$pu->unit_small_id
                    ? ($pu->smallUnit->name_ar . ' / ' . $pu->smallUnit->name_en)
                    : ($pu->largeUnit ? ($pu->largeUnit->name_ar . ' / ' . $pu->largeUnit->name_en) : null);
            }
            if ($r->order_max_unit_id && isset($puById[$r->order_max_unit_id])) {
                $pu = $puById[$r->order_max_unit_id];
                $unitId = $this->unitIdFromProductUnitForStoreLimits($pu, $firstPuByProduct[$pid] ?? $pu->id);
                $maxName = $pu->smallUnit && $unitId === (int)$pu->unit_small_id
                    ? ($pu->smallUnit->name_ar . ' / ' . $pu->smallUnit->name_en)
                    : ($pu->largeUnit ? ($pu->largeUnit->name_ar . ' / ' . $pu->largeUnit->name_en) : null);
            }

            $out[$pid] = [
                'min_qty'        => is_null($r->order_min_qty) ? null : (float)$r->order_min_qty,
                'min_unit_name'  => $minName,
                'max_qty'        => is_null($r->order_max_qty) ? null : (float)$r->order_max_qty,
                'max_unit_name'  => $maxName,
                // للوحدة الأساسية سنحوّل بالتحقق فقط
            ];
        }

        return $out;
    }

    /** تحقّق الحدود: يرجع [violations[], orderTotals[]] ويوقف الحفظ لو في مخالفات */
    protected function checkQuotationLimits(array $data, PurchaseRequisition $req): array
    {
        $storeId   = (int) $req->warehouse_id;

        // حدود قيمة الطلب على مستوى المخزن (نفس أسماء الحقول المطلوبة)
        $warehouse = DB::table('store_main_date')->where('id', $storeId)->first(['min_order_quantity', 'max_order_quantity']);
        $minAmount = $warehouse ? ($warehouse->min_order_quantity ?? null) : null;
        $maxAmount = $warehouse ? ($warehouse->max_order_quantity ?? null) : null;

        // إجمالي قبل الضريبة (مقارنة بقيمة المخزن)
        $totalAmount = 0.0;
        $n = count($data['product_id']);
        for ($i = 0; $i < $n; $i++) {
            $totalAmount += ((float)$data['quantity'][$i]) * ((float)$data['price'][$i]);
        }

        $violations = [];

        // اسم وكود المنتج لرسالة أوضح
        $productIds = array_map('intval', $data['product_id']);
        $prodRows = ProductData::whereIn('id', $productIds)->get(['id', 'system_code', 'name_ar', 'name_en'])->keyBy('id');

        // خريطة حدود الأصناف (للعرض + المقارنة)
        $limitsMap = $this->buildProductLimitsMap($storeId, $productIds);

        // نحمّل product_units للوحدات المطلوبة في البنود كلها
        $usedPuIds = array_map('intval', $data['unit_id']);
        $puRows = ProductUnit::whereIn('id', $usedPuIds)->get(['id', 'product_id', 'unit_large_id', 'unit_small_id'])->keyBy('id');

        // أيضاً وحدات حدود المخزن للصنف (لكي نحوّل الحدود للوحدة الأساسية)
        $limitPuIds = [];
        foreach ($limitsMap as $pid => $L) {
            $row = DB::table('store_products')->where('store_id', $storeId)->where('product_id', $pid)->first([
                'order_min_unit_id',
                'order_max_unit_id'
            ]);
            if ($row) {
                if ($row->order_min_unit_id) $limitPuIds[] = (int)$row->order_min_unit_id;
                if ($row->order_max_unit_id) $limitPuIds[] = (int)$row->order_max_unit_id;
            }
        }
        if ($limitPuIds) {
            $morePu = ProductUnit::whereIn('id', array_values(array_unique($limitPuIds)))->get(['id', 'product_id', 'unit_large_id', 'unit_small_id'])->keyBy('id');
            // دمج
            $puRows = $puRows->union($morePu)->keyBy('id');
        }

        // لحساب "أول سجل" لكل منتج (قاعدة الكبيرة أولاً في إعدادات المخزن)
        $allPuForProducts = ProductUnit::whereIn('product_id', $productIds)->orderBy('product_id')->orderBy('id')->get(['id', 'product_id', 'unit_large_id', 'unit_small_id']);
        $firstPuByProduct = [];
        foreach ($allPuForProducts as $pu) {
            if (!isset($firstPuByProduct[$pu->product_id])) {
                $firstPuByProduct[$pu->product_id] = $pu->id;
            }
        }

        // خدمة التحويل
        /** @var UnitConversionService $svc */
        $svc = app(UnitConversionService::class);

        // تحقق كميات البنود مقابل حدود الصنف (بعد التحويل للأصغر)
        for ($i = 0; $i < $n; $i++) {
            $pid   = (int)$data['product_id'][$i];
            $puId  = (int)$data['unit_id'][$i];
            $qty   = (float)$data['quantity'][$i];

            if ($qty < 0) continue; // تم التحقق validation، احتياطًا

            $prod = $prodRows[$pid] ?? null;
            $label = $prod ? ($prod->system_code . ' - ' . trim(($prod->name_ar ?? '') . ' / ' . ($prod->name_en ?? ''))) : ('#' . $pid);

            // وحدة البند (PQ): الصغيرة إن وجدت
            $pu = $puRows[$puId] ?? null;
            if (!$pu) continue;

            $lineUnitId = $this->unitIdFromProductUnitForPQ($pu);
            if (!$lineUnitId) continue;

            // الكمية بالأصغر
            try {
                $qtyBase = $svc->toBase($pid, $qty, $lineUnitId);
            } catch (\Throwable $e) {
                continue; // لا يوجد مسار تحويل
            }

            // حدود الصنف (إن وُجدت)
            $sp = DB::table('store_products')->where('store_id', $storeId)->where('product_id', $pid)->first([
                'order_min_qty',
                'order_min_unit_id',
                'order_max_qty',
                'order_max_unit_id'
            ]);
            if (!$sp) continue;

            // الحد الأدنى
            if (!is_null($sp->order_min_qty) && $sp->order_min_qty >= 0 && $sp->order_min_unit_id) {
                $minPu = $puRows[(int)$sp->order_min_unit_id] ?? null;
                if ($minPu) {
                    $minUnitId = $this->unitIdFromProductUnitForStoreLimits($minPu, $firstPuByProduct[$pid] ?? $minPu->id);
                    if ($minUnitId) {
                        try {
                            $minBase = $svc->toBase($pid, (float)$sp->order_min_qty, $minUnitId);
                            if ($qtyBase < $minBase) {
                                // عرض الحد بوحدته كما في buildProductLimitsMap
                                $limText = isset($limitsMap[$pid]) && $limitsMap[$pid]['min_qty'] && $limitsMap[$pid]['min_unit_name']
                                    ? number_format($limitsMap[$pid]['min_qty'], 6) . ' ' . $limitsMap[$pid]['min_unit_name']
                                    : number_format((float)$sp->order_min_qty, 6);
                                $violations[] = "الكمية المطلوبة للصنف ({$label}) أقل من الحد الأدنى المسموح: {$limText}.";
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }
            // الحد الأقصى
            if (!is_null($sp->order_max_qty) && $sp->order_max_qty >= 0 && $sp->order_max_unit_id) {
                $maxPu = $puRows[(int)$sp->order_max_unit_id] ?? null;
                if ($maxPu) {
                    $maxUnitId = $this->unitIdFromProductUnitForStoreLimits($maxPu, $firstPuByProduct[$pid] ?? $maxPu->id);
                    if ($maxUnitId) {
                        try {
                            $maxBase = $svc->toBase($pid, (float)$sp->order_max_qty, $maxUnitId);
                            if ($qtyBase > $maxBase) {
                                $limText = isset($limitsMap[$pid]) && $limitsMap[$pid]['max_qty'] && $limitsMap[$pid]['max_unit_name']
                                    ? number_format($limitsMap[$pid]['max_qty'], 6) . ' ' . $limitsMap[$pid]['max_unit_name']
                                    : number_format((float)$sp->order_max_qty, 6);
                                $violations[] = "الكمية المطلوبة للصنف ({$label}) تتجاوز الحد الأقصى المسموح: {$limText}.";
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }
        }

        // تحقق مبلغ المخزن (خاص بالمخزن)
        if (!is_null($minAmount) && (float)$minAmount > 0 && $totalAmount < (float)$minAmount) {
            $violations[] = 'إجمالي مبلغ العرض أقل من الحد الأدنى المسموح للمخزن.';
        }
        if (!is_null($maxAmount) && (float)$maxAmount > 0 && $totalAmount > (float)$maxAmount) {
            $violations[] = 'إجمالي مبلغ العرض يتجاوز الحد الأقصى المسموح للمخزن.';
        }

        $orderTotals = [
            'total_amount' => $totalAmount,
            'min_amount'   => is_null($minAmount) ? null : (float)$minAmount,
            'max_amount'   => is_null($maxAmount) ? null : (float)$maxAmount,
        ];

        return [$violations, $orderTotals, $limitsMap];
    }
    protected function isReqLocked(?PurchaseRequisition $req): bool
    {
        return $req && in_array($req->status, ['closed']);
    }

    public function index(Request $request)
    {
        $requisitionId = $request->query('requisition_id');

        if ($request->ajax()) {
            $query = PriceQuotation::with(['supplier', 'requisition'])
                ->when($requisitionId, fn($q) => $q->where('requisition_id', $requisitionId))
                ->select([
                    'id',
                    'quotation_number',
                    'start_date',
                    'end_date',
                    'supplier_id',
                    'total_offer',
                    'total_tax',
                    'final_price_including_tax',
                    'requisition_id'
                ])->orderByDesc('created_at');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('supplier_name', fn($row) => optional($row->supplier)->name_ar . ' / ' . optional($row->supplier)->name_en)
                ->addColumn('total_offer', fn($row) => number_format($row->total_offer, 2))
                ->addColumn('total_tax', fn($row) => number_format($row->total_tax, 2))
                ->addColumn('final_price', fn($row) => number_format($row->final_price_including_tax, 2))
                ->addColumn('requisition_number', fn($row) => optional($row->requisition)->requisition_number)
                ->addColumn('actions', function ($row) {
                    $editUrl = route('price_quotations.edit', $row->id);
                    $deleteBtn = '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '"><i class="fa fa-trash"></i> ' . __('purchases.delete') . '</button>';
                    $printBtn  = '<a href="' . route('price_quotations.print', $row->id) . '" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i> ' . __('purchases.print') . '</a>';
                    $purchaseOrderBtn = '<a href="' . route('purchase_order.create', ['quotation_id' => $row->id]) . '" class="btn btn-success btn-sm"><i class="fa fa-shopping-cart"></i> ' . __('purchases.create_purchase_order') . '</a>';
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> ' . __('purchases.edit') . '</a>';
                    $detailsBtn = '<button class="btn btn-info btn-sm view-quotation-btn" data-id="' . $row->id . '"><i class="fa fa-eye"></i> ' . __('purchases.details') . '</button>';

                    $locked = $row->requisition && in_array($row->requisition->status, ['closed']);

                    $buttons = [];
                    if (!$locked) {
                        // مسموح فقط عندما الطلب غير مقفل
                        $buttons[] = $editBtn;
                        $buttons[] = $deleteBtn;
                        $buttons[] = $purchaseOrderBtn;
                    }
                    // أزرار العرض/الطباعة تبقى دائمًا
                    $buttons[] = $printBtn;
                    $buttons[] = $detailsBtn;

                    return implode(' ', $buttons);
                })

                ->rawColumns(['actions'])
                ->make(true);
        }

        $requisition = PurchaseRequisition::findOrFail($requisitionId);
        return view('purchases.programs.price_quotations.index', compact('requisition'));
    }

    /** مقارنة العروض (AJAX) */
    public function compare(Request $request)
    {
        $requisitionId = $request->query('requisition_id');
        if (!$requisitionId) return response()->json(['data' => []]);

        $list = PriceQuotation::with('supplier')
            ->where('requisition_id', $requisitionId)
            ->get(['id', 'quotation_number', 'supplier_id', 'final_price_including_tax', 'total_tax', 'delivery_period', 'end_date', 'start_date']);

        if ($list->isEmpty()) return response()->json(['data' => []]);

        $today = Carbon::today()->format('Y-m-d');
        $minPrice = $list->min('final_price_including_tax') ?: 0;
        $validPeriods = $list->pluck('delivery_period')->filter(fn($v) => is_numeric($v) && $v > 0);
        $minDelivery = $validPeriods->min() ?: null;

        $scored = $list->map(function ($q) use ($minPrice, $minDelivery, $today) {
            $price = (float)$q->final_price_including_tax;
            $period = is_null($q->delivery_period) ? null : (int)$q->delivery_period;
            $priceScore = ($minPrice && $price) ? ($price / $minPrice) : 999;
            $deliveryScore = ($minDelivery && $period) ? ($period / $minDelivery) : 999;
            $score = (0.7 * $priceScore) + (0.3 * $deliveryScore);

            return [
                'id' => $q->id,
                'quotation_number' => $q->quotation_number,
                'supplier_name' => optional($q->supplier)->name_ar . ' / ' . optional($q->supplier)->name_en,
                'final_price_including_tax' => $price,
                'total_tax' => (float)$q->total_tax,
                'delivery_period' => $period,
                'end_date' => $q->end_date,
                'start_date' => $q->start_date,
                'is_expired' => ($q->end_date && $q->end_date < $today),
                'score' => $score,
                'price_score' => $priceScore,
                'delivery_score' => $deliveryScore,
            ];
        })->values();

        if ($scored->isEmpty()) return response()->json(['data' => []]);

        $bestPriceScore = $scored->min('price_score');
        $bestDeliveryScore = $scored->min('delivery_score');

        $sorted = $scored->sortBy('score')->values()->map(function ($r) use ($bestPriceScore, $bestDeliveryScore) {
            $r['is_best_price'] = ($r['price_score'] == $bestPriceScore);
            $r['is_fastest_delivery'] = ($r['delivery_score'] == $bestDeliveryScore);
            return $r;
        });

        $bestOverallIndex = $sorted->search(fn($r) => !$r['is_expired']);
        $bestOverallIndex = ($bestOverallIndex === false) ? 0 : $bestOverallIndex;
        $sorted = $sorted->values()->map(function ($r, $i) use ($bestOverallIndex) {
            $r['rank'] = $i + 1;
            $r['is_best_overall'] = ($i === $bestOverallIndex);
            return $r;
        });

        return response()->json(['data' => $sorted]);
    }

    /** تفاصيل عرض سعر (AJAX) */
    public function show($id)
    {
        $quotation = PriceQuotation::with([
            'supplier',
            'items.product',
            'items.unit.smallUnit',
            'items.unit.largeUnit',
            'requisition'
        ])->findOrFail($id);

        $details = [
            __('purchases.price_quotation_number')    => $quotation->quotation_number,
            __('purchases.requisition_number')        => optional($quotation->requisition)->requisition_number,
            __('purchases.start_date')                => $quotation->start_date,
            __('purchases.end_date')                  => $quotation->end_date,
            __('purchases.thesupplier')               => $quotation->supplier ? ($quotation->supplier->name_ar . ' / ' . $quotation->supplier->name_en) : '-',
            __('purchases.delivery_period')           => $quotation->delivery_period ?: '-',
            __('purchases.delivery_location')         => $quotation->delivery_location ?: '-',
            __('purchases.payment_method')            => match ($quotation->payment_method) {
                'cash' => __('purchases.payment_cash'),
                'bank_transfer' => __('purchases.payment_bank_transfer'),
                'credit' => __('purchases.payment_credit'),
                default => __('purchases.select_payment_method'),
            },
            __('purchases.payment_terms')             => $quotation->payment_terms ?: '-',
            __('purchases.notes')                     => $quotation->notes ?: '-',
            __('purchases.total_offer')               => number_format($quotation->total_offer, 2),
            __('purchases.total_tax')                 => number_format($quotation->total_tax, 2),
            __('purchases.final_price_including_tax') => number_format($quotation->final_price_including_tax, 2),
        ];

        $itemsTable = '<table class="table table-sm table-bordered"><thead>
            <tr>
              <th>' . __('purchases.item') . '</th>
              <th>' . __('purchases.unit') . '</th>
              <th>' . __('purchases.quantity') . '</th>
              <th>' . __('purchases.price') . '</th>
              <th>' . __('purchases.vat_amount') . '</th>
              <th>' . __('purchases.line_total') . '</th>
              <th>' . __('general.notes') . '</th>
            </tr>
            </thead><tbody>';

        foreach ($quotation->items as $item) {
            $product = $item->product;
            $itemName = $product ? ($product->name_ar . ($product->name_en ? ' / ' . $product->name_en : '')) : '-';
            $before = $item->quantity * $item->price;
            $taxAmt = $item->tax_amount;
            $lineTotal = $before + $taxAmt;

            $unitRow = $item->unit;
            $unitName = '-';
            if ($unitRow) {
                if ($unitRow->smallUnit) {
                    $unitName = $unitRow->smallUnit->name_ar . ' / ' . $unitRow->smallUnit->name_en;
                } elseif ($unitRow->largeUnit) {
                    $unitName = $unitRow->largeUnit->name_ar . ' / ' . $unitRow->largeUnit->name_en;
                }
            }

            $itemsTable .= '<tr>
                <td>' . $itemName . '</td>
                <td>' . $unitName . '</td>
                <td>' . number_format($item->quantity, 2) . '</td>
                <td>' . number_format($item->price, 2) . '</td>
                <td>' . number_format($taxAmt, 2) . '</td>
                <td>' . number_format($lineTotal, 2) . '</td>
                <td>' . ($item->notes ?: '-') . '</td>
            </tr>';
        }
        $itemsTable .= '</tbody></table>';
        $details[__('purchases.items')] = $itemsTable;

        return response()->json(['details' => $details]);
    }

    /** إنشاء عرض سعر لطلب احتياج معيّن */
    public function create(Request $request)
    {
        $requisitionId = $request->query('requisition_id');
        if (!$requisitionId) {
            return redirect()->route('purchase_requisitions.index')
                ->with('error', __('purchases.requisition_not_specified'));
        }

        $requisition = PurchaseRequisition::with(['items.product', 'items.unit.smallUnit', 'items.unit.largeUnit'])
            ->findOrFail($requisitionId);

        if ($this->isReqLocked($requisition)) {
            return redirect()->route('price_quotations.index', ['requisition_id' => $requisition->id])
                ->with('error', 'لا يمكن إنشاء عرض سعر لهذا الطلب لأنه تم إنشاء أمر شراء له أو تم إغلاقه.');
        }

        $requisition = PurchaseRequisition::with(['items.product', 'items.unit.smallUnit', 'items.unit.largeUnit'])
            ->findOrFail($requisitionId);

        $last = PriceQuotation::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $quotationNumber = 'PQ-' . Carbon::now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $suppliers = Suppliersdata::where('status', 'active')->get();
        $defaultSupplierId = $requisition->preferred_supplier_id;

        // === منتجات مرتبطة بالمخزن الخاص بالطلب + منتجات البنود الحالية
        $storeId = (int) $requisition->warehouse_id;
        $storeProductIds = DB::table('store_products')
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->pluck('product_id');

        $reqItemIds = $requisition->items->pluck('item_id');
        $productIds = $storeProductIds->merge($reqItemIds)->unique()->values();

        $products = ProductData::whereIn('id', $productIds)
            ->where('status', 'active')
            ->select('id', 'name_ar', 'name_en', 'vat', 'vat_value')
            ->get();

        $units = ProductUnit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('product_id')->orderBy('id')
            ->get();

        $unitsMap = [];
        foreach ($units as $u) {
            $nameAr = optional($u->smallUnit)->name_ar ?? optional($u->largeUnit)->name_ar ?? '-';
            $nameEn = optional($u->smallUnit)->name_en ?? optional($u->largeUnit)->name_en ?? '-';
            $unitsMap[$u->product_id][] = ['id' => $u->id, 'name' => "$nameAr / $nameEn"];
        }

        // حدود المخزن (قيمة الطلب)
        $warehouse = DB::table('store_main_date')->where('id', $storeId)->first(['min_order_quantity', 'max_order_quantity']);
        $storeAmountLimits = [
            'min' => $warehouse ? ($warehouse->min_order_quantity ?? null) : null,
            'max' => $warehouse ? ($warehouse->max_order_quantity ?? null) : null,
        ];

        // حدود الأصناف لعرضها كسطر مساعد
        $limitsMap = $this->buildProductLimitsMap($storeId, $products->pluck('id')->all());

        return view('purchases.programs.price_quotations.create', compact(
            'requisition',
            'quotationNumber',
            'suppliers',
            'defaultSupplierId',
            'products',
            'unitsMap',
            'storeAmountLimits',
            'limitsMap'
        ));
    }

    /** حفظ عرض سعر جديد (مع التحقق من الحدود) */
    public function store(Request $request)
    {
        $rules = [
            'requisition_id'   => 'required|exists:purchase_requisitions,id',
            'quotation_number' => 'required|unique:price_quotations,quotation_number',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'supplier_id'      => 'required|exists:suppliers_data,id',
            'delivery_period'  => 'nullable|integer|min:0',
            'delivery_location' => 'nullable|string|max:255',
            'payment_method'   => 'nullable|in:cash,bank_transfer,credit',
            'payment_terms'    => 'nullable|string|max:500',
            'notes'            => 'nullable|string|max:1000',
            'product_id'       => 'required|array|min:1',
            'product_id.*'     => 'required|exists:products_data,id',
            'unit_id'          => 'required|array',
            'unit_id.*'        => 'required|exists:product_units,id',
            'quantity'         => 'required|array',
            'quantity.*'       => 'required|numeric|min:0',
            'price'            => 'required|array',
            'price.*'          => 'required|numeric|min:0',
            'tax_rate'         => 'required|array',
            'tax_rate.*'       => 'required|numeric|min:0',
            'item_notes'       => 'nullable|array',
            'item_notes.*'     => 'nullable|string|max:500',
        ];
        $data = $request->validate($rules);

        $req = PurchaseRequisition::findOrFail($data['requisition_id']);

        if ($this->isReqLocked($req)) {
            return redirect()->route('price_quotations.index', ['requisition_id' => $req->id])
                ->with('error', 'لا يمكن إنشاء عرض سعر لهذا الطلب لأنه تم إنشاء أمر شراء له أو تم إغلاقه.');
        }
        // تحقق الحدود
        [$violations, $orderTotals, $limitsMap] = $this->checkQuotationLimits($data, $req);
        if (!empty($violations)) {
            return back()->withInput()->with([
                'showLimitModal'  => true,
                'limitViolations' => $violations,
                'orderTotals'     => $orderTotals,
            ]);
        }

        // حساب الإجماليات
        $totalOffer = 0.0;
        $totalTax = 0.0;
        $lines = [];
        for ($i = 0; $i < count($data['product_id']); $i++) {
            $qty = (float)$data['quantity'][$i];
            $prc = (float)$data['price'][$i];
            $rate = (float)$data['tax_rate'][$i];
            $sub = $qty * $prc;
            $tax = $sub * ($rate / 100);
            $totalOffer += $sub;
            $totalTax += $tax;

            $lines[] = [
                'product_id' => $data['product_id'][$i],
                'unit_id' => $data['unit_id'][$i],
                'quantity' => $qty,
                'price' => $prc,
                'tax_amount' => $tax,
                'notes' => $data['item_notes'][$i] ?? null,
            ];
        }
        $final = $totalOffer + $totalTax;

        $quotation = PriceQuotation::create([
            'requisition_id' => $data['requisition_id'],
            'quotation_number' => $data['quotation_number'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'supplier_id' => $data['supplier_id'],
            'delivery_period' => $data['delivery_period'] ?? null,
            'delivery_location' => $data['delivery_location'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'total_tax' => $totalTax,
            'total_offer' => $totalOffer,
            'final_price_including_tax' => $final,
            'payment_terms' => $data['payment_terms'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $requisition = PurchaseRequisition::find($quotation->requisition_id);
        if ($requisition && $requisition->status === 'pending') {
            $requisition->status = 'receiving_quotes';
            $requisition->save();
        }

        foreach ($lines as $line) {
            PriceQuotationItem::create([
                'price_quotation_id' => $quotation->id,
                'item_id' => $line['product_id'],
                'unit_id' => $line['unit_id'],
                'quantity' => $line['quantity'],
                'price' => $line['price'],
                'tax_amount' => $line['tax_amount'],
                'notes' => $line['notes'],
            ]);
        }

        return redirect()->route('price_quotations.index', ['requisition_id' => $data['requisition_id']])
            ->with('success', __('purchases.quotation_created'));
    }

    /** تعديل عرض سعر */
    public function edit($id)
    {
        $quotation   = PriceQuotation::with(['items.product', 'items.unit', 'requisition'])->findOrFail($id);
        $requisition = $quotation->requisition;

        if ($this->isReqLocked($requisition)) {
            return redirect()->route('price_quotations.index', ['requisition_id' => $requisition->id])
                ->with('error', 'لا يمكن تعديل عرض السعر لأن الطلب المرتبط تم إنشاء أمر شراء له أو تم إغلاقه.');
        }

        $suppliers = Suppliersdata::where('status', 'active')->get();
        $defaultSupplierId = $quotation->supplier_id;

        // === منتجات مخزن الطلب + منتجات سطور العرض
        $storeId = (int) $requisition->warehouse_id;
        $storeProductIds = DB::table('store_products')
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->pluck('product_id');

        $rowProductIds = $quotation->items->pluck('item_id');
        $productIds = $storeProductIds->merge($rowProductIds)->unique()->values();

        $products = ProductData::whereIn('id', $productIds)
            ->where('status', 'active')
            ->select('id', 'name_ar', 'name_en', 'vat', 'vat_value')
            ->get();

        $units = ProductUnit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])
            ->whereIn('product_id', $products->pluck('id'))
            ->orderBy('product_id')->orderBy('id')
            ->get();

        $unitsMap = [];
        foreach ($units as $u) {
            $nameAr = optional($u->smallUnit)->name_ar ?? optional($u->largeUnit)->name_ar ?? '-';
            $nameEn = optional($u->smallUnit)->name_en ?? optional($u->largeUnit)->name_en ?? '-';
            $unitsMap[$u->product_id][] = ['id' => $u->id, 'name' => "$nameAr / $nameEn"];
        }

        // حدود المخزن (قيمة الطلب)
        $warehouse = DB::table('store_main_date')->where('id', $storeId)->first(['min_order_quantity', 'max_order_quantity']);
        $storeAmountLimits = [
            'min' => $warehouse ? ($warehouse->min_order_quantity ?? null) : null,
            'max' => $warehouse ? ($warehouse->max_order_quantity ?? null) : null,
        ];

        // حدود الأصناف للعرض
        $limitsMap = $this->buildProductLimitsMap($storeId, $products->pluck('id')->all());

        return view('purchases.programs.price_quotations.edit', compact(
            'quotation',
            'requisition',
            'suppliers',
            'defaultSupplierId',
            'products',
            'unitsMap',
            'storeAmountLimits',
            'limitsMap'
        ));
    }

    /** تحديث عرض سعر (مع التحقق من الحدود) */
    public function update(Request $request, $id)
    {
        $quotation = PriceQuotation::with('requisition')->findOrFail($id);
        $req = $quotation->requisition;

        if ($this->isReqLocked($req)) {
            return redirect()->route('price_quotations.index', ['requisition_id' => $req->id])
                ->with('error', 'لا يمكن تعديل عرض السعر لأن الطلب المرتبط تم إنشاء أمر شراء له أو تم إغلاقه.');
        }

        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'supplier_id' => 'required|exists:suppliers_data,id',
            'delivery_period' => 'nullable|integer|min:0',
            'delivery_location' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer,credit',
            'payment_terms' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products_data,id',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|exists:product_units,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
            'tax_rate' => 'required|array',
            'tax_rate.*' => 'required|numeric|min:0',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string|max:500',
        ];
        $data = $request->validate($rules);

        $req = $quotation->requisition()->firstOrFail();

        // تحقق الحدود قبل الحفظ
        [$violations, $orderTotals, $limitsMap] = $this->checkQuotationLimits($data, $req);
        if (!empty($violations)) {
            return back()->withInput()->with([
                'showLimitModal'  => true,
                'limitViolations' => $violations,
                'orderTotals'     => $orderTotals,
            ]);
        }

        // حساب الإجماليات
        $totalOffer = 0.0;
        $totalTax = 0.0;
        $lines = [];
        for ($i = 0; $i < count($data['product_id']); $i++) {
            $qty = (float)$data['quantity'][$i];
            $prc = (float)$data['price'][$i];
            $rate = (float)$data['tax_rate'][$i];
            $sub = $qty * $prc;
            $tax = $sub * ($rate / 100);
            $totalOffer += $sub;
            $totalTax += $tax;
            $lines[] = ['product_id' => $data['product_id'][$i], 'unit_id' => $data['unit_id'][$i], 'quantity' => $qty, 'price' => $prc, 'tax_amount' => $tax, 'notes' => $data['item_notes'][$i] ?? null];
        }
        $final = $totalOffer + $totalTax;

        $quotation->update([
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'supplier_id' => $data['supplier_id'],
            'delivery_period' => $data['delivery_period'] ?? null,
            'delivery_location' => $data['delivery_location'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'total_offer' => $totalOffer,
            'total_tax' => $totalTax,
            'final_price_including_tax' => $final,
            'payment_terms' => $data['payment_terms'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $quotation->items()->delete();
        foreach ($lines as $line) {
            PriceQuotationItem::create([
                'price_quotation_id' => $quotation->id,
                'item_id' => $line['product_id'],
                'unit_id' => $line['unit_id'],
                'quantity' => $line['quantity'],
                'price' => $line['price'],
                'tax_amount' => $line['tax_amount'],
                'notes' => $line['notes'],
            ]);
        }

        return redirect()->route('price_quotations.index', ['requisition_id' => $quotation->requisition_id])
            ->with('success', __('purchases.quotation_updated'));
    }

    /** طباعة عرض سعر */
    public function print($id)
    {
        $quotation = PriceQuotation::with(['supplier', 'items.product', 'items.unit.smallUnit', 'items.unit.largeUnit'])
            ->findOrFail($id);

        $movement_summary = (object)[
            'quotation_number' => $quotation->quotation_number,
            'start_date' => $quotation->start_date,
            'end_date' => $quotation->end_date,
            'supplier' => $quotation->supplier,
            'delivery_location' => $quotation->delivery_location,
            'payment_method' => $quotation->payment_method,
            'payment_terms' => $quotation->payment_terms,
            'notes' => $quotation->notes,
        ];
        $movement_items = $quotation->items;
        $title = __('purchases.print_price_quotation_title');

        return view('purchases.programs.price_quotations.print_price_quotations', compact(
            'movement_summary',
            'movement_items',
            'title'
        ));
    }

    /** حذف عرض سعر */
    public function destroy($id)
    {
        $quotation = PriceQuotation::with('requisition')->find($id);
        if (!$quotation) {
            return response()->json(['success' => false, 'message' => __('purchases.quotation_not_found')], 404);
        }

        if ($this->isReqLocked($quotation->requisition)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف عرض السعر لأن الطلب المرتبط تم إنشاء أمر شراء له أو تم إغلاقه.'
            ], 403);
        }

        $quotation->items()->delete();
        $quotation->delete();

        return response()->json(['success' => true, 'message' => __('purchases.quotation_deleted')]);
    }
}
