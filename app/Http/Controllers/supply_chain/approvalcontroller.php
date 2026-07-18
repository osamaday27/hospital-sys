<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\supply_chain\receivinginspecting;
use App\Models\supply_chain\receivinginspectingapproval;
use App\Models\supply_chain\productstorebalance;
use App\Models\supply_chain\productunit;

// ملاحظة: موديل الحركة معرف تحت App\models\supply_chain (m صغيرة)
use App\models\supply_chain\storemovement as StoreMovement;

// فواتير المورد
use App\services\Supplier\SupplierInvoiceService;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Purchases\PurchaseOrder;

class approvalcontroller extends Controller
{
    /** نوع الحركة المطابق للـENUM */
    private const MOVEMENT_TYPE_NORMAL_ADDITION = 'normal_addition';

    /** خدمة إنشاء فواتير المورد */
    private SupplierInvoiceService $invoiceService;

    public function __construct(SupplierInvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /** صفحة العضو */
    public function index()
    {
        return view('supply_chain.program.manage_receiving_inspecting_items.index');
    }

    /** DataTable */
    public function datatable(Request $request)
    {
        $employeeId = Auth::user()->employee_id;
        $myStatus   = $request->get('my_status', 'pending');

        $itemsTable = (new \App\Models\supply_chain\receivinginspectingitem)->getTable();

        $q = receivinginspecting::query()
            ->leftJoin('receiving_inspecting_approvals as a', function ($join) use ($employeeId) {
                $join->on('a.receiving_inspecting_id', '=', 'receiving_inspecting.id')
                    ->whereNull('a.deleted_at')
                    ->where('a.employee_id', '=', $employeeId);
            })
            ->with([
                'supplier:id,name_ar,name_en',
                'store:id,name_ar,name_en',
            ])
            ->select([
                'receiving_inspecting.*',
                DB::raw('a.status as my_status'),
                DB::raw("(SELECT COALESCE(SUM(rii.cost_price),0)
                      FROM {$itemsTable} AS rii
                      WHERE rii.receiving_inspecting_id = receiving_inspecting.id) AS total_cost"),
            ])
            ->when(in_array($myStatus, ['pending', 'approved', 'rejected'], true), function ($qq) use ($myStatus) {
                $qq->where('a.status', $myStatus);
            })
            ->latest('receiving_inspecting.id');

        return datatables()->of($q)
            ->addColumn('supplier_name', fn($row) => $row->supplier->name_ar ?? $row->supplier->name_en ?? '-')
            ->addColumn('store_name', fn($row) => $row->store->name_ar ?? $row->store->name_en ?? '-')
            ->editColumn('document_date', fn($row) => \Illuminate\Support\Carbon::parse($row->document_date)->format('Y-m-d'))
            ->editColumn('status', function ($row) {
                $s = $row->my_status ?: 'pending';
                $map = [
                    'pending'  => [trans('supply_chain_trans.status_pending'),  'bg-warning text-dark'],
                    'approved' => [trans('supply_chain_trans.status_approved'), 'bg-success'],
                    'rejected' => [trans('supply_chain_trans.status_rejected'), 'bg-danger'],
                ];
                [$label, $class] = $map[$row->my_status] ?? $map[$s] ?? [$s, 'bg-secondary'];
                return '<span class="badge ' . $class . '">' . $label . '</span>';
            })
            ->editColumn('total_cost', fn($row) => number_format((float)($row->total_cost ?? 0), 2))
            ->addColumn('actions', function ($row) {
                $showText   = e(trans('supply_chain_trans.btn_view'));
                $myStatus   = e($row->my_status ?: 'pending');
                $docStatus  = e($row->status ?: 'pending');

                return '<button type="button" class="btn btn-sm btn-outline-primary btn-show"
                            data-id="' . $row->id . '"
                            data-my-status="' . $myStatus . '"
                            data-doc-status="' . $docStatus . '">' . $showText . '</button>';
            })
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    /** عرض المحضر */
    public function show($id)
    {
        $doc = receivinginspecting::with([
            'supplier:id,name_ar,name_en',
            'store:id,name_ar,name_en',
            'purchaseOrder:id,order_number',
            'items.product:id,system_code,name_ar,name_en',
            'items.acceptedUnit:id,name_ar,name_en',
            'items.unit.largeUnit:id,name_ar,name_en',
            'items.unit.smallUnit:id,name_ar,name_en',
            'approvals.employee:id,full_name,first_name_ar,second_name_ar,third_name_ar,fourth_name_ar,first_name_en,second_name_en,third_name_en,fourth_name_en',
        ])->findOrFail($id);

        $date     = $doc->document_date ? Carbon::parse($doc->document_date)->format('Y-m-d') : '-';
        $supplier = $doc->supplier->name_ar ?? $doc->supplier->name_en ?? '-';
        $store    = $doc->store->name_ar ?? $doc->store->name_en ?? '-';
        $po       = $doc->purchaseOrder->order_number ?? '-';

        $totalCost = $doc->items->sum(function ($it) {
            return (float) ($it->cost_price ?? 0);
        });

        return view('supply_chain.program.manage_receiving_inspecting_items._show_modal', compact(
            'doc',
            'date',
            'supplier',
            'store',
            'po',
            'totalCost'
        ));
    }

    /** موافقة/رفض */
    public function approve($approval)
    {
        return $this->updateApproval($approval, 'approved');
    }
    public function reject($approval)
    {
        return $this->updateApproval($approval, 'rejected');
    }

    /** تنفيذ تحديث موافقة العضو ضمن ترانزاكشن مع قفل */
    protected function updateApproval($id, $status)
    {
        $employeeId = Auth::user()->employee_id;

        DB::transaction(function () use ($id, $status, $employeeId) {
            $doc = receivinginspecting::with(['items.unit'])->lockForUpdate()->findOrFail($id);

            if ($doc->status !== 'pending') {
                abort(422, trans('supply_chain_trans.load_failed'));
            }

            $appr = receivinginspectingapproval::where('receiving_inspecting_id', $doc->id)
                ->where('employee_id', $employeeId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($appr->status !== 'pending') {
                abort(422, trans('supply_chain_trans.load_failed'));
            }

            $appr->update(['status' => $status]);

            $this->checkDocumentStatus($doc);
        });

        return response()->json(['ok' => true, 'message' => trans("supply_chain_trans.approval_{$status}")]);
    }

    /**
     * عند الإجماع على الموافقة:
     * - ترحيل الأرصدة.
     * - إنشاء فاتورة المورد مرة واحدة بعد أول ترحيل وقبل أول حركة.
     * - تسجيل الحركات.
     */
    protected function checkDocumentStatus(receivinginspecting $doc)
    {
        $total         = $doc->approvals()->count();
        $approvedCount = $doc->approvals()->where('status', 'approved')->count();
        $rejectedCount = $doc->approvals()->where('status', 'rejected')->count();

        if ($total > 0 && $approvedCount === $total) {
            $doc->update(['status' => 'approved']);

            $doc->loadMissing([
                'items.unit',
                'items.acceptedUnit',
                'purchaseOrder.items',
                'store:id,branch_id',
            ]);

            $buckets = [];
            foreach ($doc->items as $item) {
                [$qtySmallest, $smallestUnitId] = $this->toSmallestForItem($item);
                if ($qtySmallest <= 0) continue;

                $batch  = trim((string)($item->batch_no ?? ''));
                $batch  = ($batch === '') ? null : $batch;
                $expiry = $item->expiry_date ? Carbon::parse($item->expiry_date)->toDateString() : null;

                $key = implode('|', [
                    (int)$item->product_id,
                    (int)$doc->store_id,
                    $batch ?? 'NULL',
                    $expiry ?? 'NULL',
                ]);

                if (!isset($buckets[$key])) {
                    $buckets[$key] = [
                        'product_id'  => (int)$item->product_id,
                        'store_id'    => (int)$doc->store_id,
                        'batch_no'    => $batch,
                        'expiry_date' => $expiry,
                        'qty'         => (float)$qtySmallest,
                        'smallest_id' => $smallestUnitId,
                    ];
                } else {
                    $buckets[$key]['qty'] += (float)$qtySmallest;
                }
            }

            if (empty($buckets)) return;

            $invoiceNo = (string) $doc->code;
            $alreadyExists = SupplierInvoice::withTrashed()
                ->where('invoice_no', $invoiceNo)
                ->exists();

            $invoiceCreated = false;

            foreach ($buckets as $b) {
                $unitsIdForThisKey = $b['smallest_id']
                    ?: $this->getSmallestUnitId($b['product_id'])
                    ?: $this->pickAnyUnitId($b['product_id']);

                if (!$unitsIdForThisKey) {
                    abort(422, 'لا يمكن ترحيل المخزون/تسجيل الحركة: لا توجد وحدات معرفة للصنف #' . $b['product_id']);
                }

                // A) ترحيل الرصيد
                $this->mergeIntoBalance(
                    $b['product_id'],
                    $b['store_id'],
                    $b['batch_no'],
                    $b['expiry_date'],
                    (float)$b['qty'],
                    $unitsIdForThisKey
                );

                // B) إنشاء الفاتورة مرة واحدة بعد أول ترحيل وقبل أول حركة
                if (!$invoiceCreated && !$alreadyExists) {
                    [$invoiceData, $itemsData] = $this->buildInvoicePayloadFromDoc($doc);
                    if (!empty($itemsData)) {
                        $this->invoiceService->createInvoice($invoiceData, $itemsData);
                    }
                    $invoiceCreated = true;
                }

                // C) تسجيل الحركة
                $this->logStoreMovement($doc, $b, $unitsIdForThisKey);
            }
            return;
        }

        if ($total > 0 && $rejectedCount === $total) {
            $doc->update(['status' => 'rejected']);
            return;
        }

        $doc->update(['status' => 'pending']);
    }

    /** تحويل بند لأصغر وحدة */
    private function toSmallestForItem($item): array
    {
        $productId = (int)$item->product_id;

        $disp = $item->accepted_qty_display;
        if (is_numeric($disp) && (float)$disp > 0) {
            $displayQty   = (float)$disp;
            $displayUnitId = $item->accepted_unit_id
                ?: ($item->unit ? (int)$item->unit->unit_large_id : null);

            if ($displayUnitId) {
                [$factor, $small] = $this->factorToSmallest($productId, (int)$displayUnitId);
                $small = $small ?: $displayUnitId;
                return [round($displayQty * $factor, 6), (int)$small];
            }

            $small = $this->getSmallestUnitId($productId) ?: $this->pickAnyUnitId($productId);
            if (!$small) {
                $small = $item->unit->unit_small_id ?? $item->unit->unit_large_id ?? null;
            }
            if (!$small) {
                return [round($displayQty, 6), null];
            }
            return [round($displayQty, 6), (int)$small];
        }

        $qty = (float)($item->accepted_qty ?? 0);
        if ($qty <= 0) return [0.0, null];

        $fromUnitId = $item->accepted_unit_id
            ?: ($item->unit ? (int)$item->unit->unit_large_id : null);

        if ($fromUnitId) {
            [$factor, $small] = $this->factorToSmallest($productId, (int)$fromUnitId);
            $small = $small ?: $fromUnitId;
            return [round($qty * $factor, 6), (int)$small];
        }

        $small = $this->getSmallestUnitId($productId) ?: $this->pickAnyUnitId($productId);
        return [round($qty, 6), $small ? (int)$small : null];
    }

    /** دمج كمية المحضر في جدول الأرصدة */
    private function mergeIntoBalance(int $productId, int $storeId, ?string $batch, ?string $expiry, float $qtyToAddSmallest, ?int $smallestUnitIdHint = null): void
    {
        $smallestUnitId = $smallestUnitIdHint
            ?: $this->getSmallestUnitId($productId)
            ?: $this->pickAnyUnitId($productId);

        if (!$smallestUnitId) {
            abort(422, 'لا يمكن ترحيل المخزون: لا توجد وحدات معرفة للصنف #' . $productId);
        }

        $rows = productstorebalance::where('product_id', $productId)
            ->where('store_id', $storeId)
            ->when(
                is_null($batch),
                fn($q) => $q->whereNull('batch_no'),
                fn($q) => $q->where('batch_no', $batch)
            )
            ->when(
                $expiry,
                fn($q) => $q->whereDate('expiry_date', $expiry),
                fn($q) => $q->whereNull('expiry_date')
            )
            ->lockForUpdate()
            ->get();

        $existingTotalSmallest = 0.0;

        foreach ($rows as $r) {
            $val = !is_null($r->quantity) ? (float)$r->quantity : (float)$r->quantity_in_base_unit;

            if (!is_null($r->units_id) && (int)$r->units_id !== (int)$smallestUnitId) {
                [$factor, $_] = $this->factorToSmallest($productId, (int)$r->units_id);
                $existingTotalSmallest += $val * $factor;
            } else {
                $existingTotalSmallest += $val;
            }
        }

        $finalTotalSmallest = round($existingTotalSmallest + $qtyToAddSmallest, 6);

        if ($rows->isNotEmpty()) {
            $keep = $rows->sortBy('id')->first();
            $idsToDelete = $rows->pluck('id')->filter(fn($id) => $id != $keep->id);
            if ($idsToDelete->isNotEmpty()) {
                productstorebalance::whereIn('id', $idsToDelete)->delete();
            }

            $keep->update([
                'batch_no'              => $batch,
                'expiry_date'           => $expiry,
                'quantity_in_base_unit' => $finalTotalSmallest,
                'quantity'              => $finalTotalSmallest,
                'units_id'              => (int)$smallestUnitId,
            ]);
        } else {
            productstorebalance::create([
                'product_id'            => $productId,
                'store_id'              => $storeId,
                'batch_no'              => $batch,
                'expiry_date'           => $expiry,
                'quantity_in_base_unit' => $finalTotalSmallest,
                'units_id'              => (int)$smallestUnitId,
                'quantity'              => $finalTotalSmallest,
            ]);
        }
    }

    /* ====================== تحويلات الوحدات ====================== */

    private function factorToSmallest(int $productId, int $fromUnitId): array
    {
        [$next, $smallest] = $this->buildUnitChain($productId);

        if (empty($next) || !$smallest) {
            return [1.0, (int)$fromUnitId];
        }

        $factor  = 1.0;
        $guard   = 0;
        $current = $fromUnitId;

        while ($guard < 30) {
            if (!isset($next[$current])) break;
            $factor  *= (float)$next[$current]['rate'];
            $current  = (int)$next[$current]['small'];
            if ($current === $smallest) {
                return [$factor, $smallest];
            }
            $guard++;
        }

        return [$factor, $current ?: $smallest ?: (int)$fromUnitId];
    }

    private function getSmallestUnitId(int $productId): ?int
    {
        [, $smallest] = $this->buildUnitChain($productId);
        if ($smallest) return (int)$smallest;

        return $this->pickAnyUnitId($productId);
    }

    private function buildUnitChain(int $productId): array
    {
        $rows = productunit::where('product_id', $productId)
            ->whereNotNull('unit_small_id')
            ->get(['unit_large_id', 'unit_small_id', 'conversion_rate']);

        if ($rows->isEmpty()) return [[], null];

        $next      = [];
        $largeSet  = [];
        $smallSet  = [];

        foreach ($rows as $r) {
            $L = (int)$r->unit_large_id;
            $S = (int)$r->unit_small_id;
            $R = (float)$r->conversion_rate;
            if ($R <= 0) continue;

            $next[$L]   = ['small' => $S, 'rate' => $R];
            $largeSet[$L] = true;
            $smallSet[$S] = true;
        }

        $smallest = null;
        foreach ($smallSet as $sid => $_) {
            if (!isset($largeSet[$sid])) {
                $smallest = (int)$sid;
                break;
            }
        }
        if (!$smallest) {
            $ids = array_keys($smallSet);
            $smallest = (int) end($ids);
        }

        return [$next, $smallest];
    }

    private function pickAnyUnitId(int $productId): ?int
    {
        $row = productunit::where('product_id', $productId)
            ->orderBy('id')
            ->first();

        if (!$row) return null;

        if (!empty($row->unit_small_id)) return (int)$row->unit_small_id;
        if (!empty($row->unit_large_id)) return (int)$row->unit_large_id;

        return null;
    }

    /* ====================== تسجيل الحركة ====================== */

    private function logStoreMovement(receivinginspecting $doc, array $bucket, int $unitsIdForThisKey): void
    {
        if (!$unitsIdForThisKey) {
            abort(422, 'لا يمكن تسجيل الحركة: وحدة غير معروفة للصنف #' . $bucket['product_id']);
        }

        StoreMovement::create([
            'product_id'      => $bucket['product_id'],
            'store_id'        => $bucket['store_id'],
            'units_id'        => $unitsIdForThisKey,
            'supplier_id'     => $doc->supplier_id,
            'quantity'        => $bucket['qty'],
            'batch_no'        => $bucket['batch_no'],
            'expiry_date'     => $bucket['expiry_date'],
            'movement_type'   => self::MOVEMENT_TYPE_NORMAL_ADDITION,
            'movement_number' => $doc->code,
            'reference_no'    => $doc->purchase_order_id ?? $doc->id,
            'movement_date'   => $doc->document_date ?? now(),
            'cost_price'      => null,
            'sale_price'      => null,
            'notes'           => trim((string)($doc->notes ?? '') . ' (Receiving/Inspecting approved)'),
            'user_id'         => Auth::id(),
        ]);
    }

    /* ====================== تكوين الفاتورة ====================== */

    /**
     * - qty = accepted_qty_display
     * - product_unit_id = accepted_unit_id (units.id)
     * - unit_price (لكل وحدة عرض) = تحويل سعر الـPO من الأساس إلى العرض
     * - tax = نصيب ضريبة الـPO الموزع على accepted_qty (بالأساس)
     * - status: يعتمد على Default قاعدة البيانات (new)
     * - store_id: يُحفظ من $doc->store_id
     */
    private function buildInvoicePayloadFromDoc(receivinginspecting $doc): array
    {
        /** @var PurchaseOrder|null $po */
        $po = $doc->purchaseOrder()->with(['items'])->first();
        $poPriceMap = $po ? $this->buildPoPriceMap($po) : [];

        $docDate = $doc->document_date ? Carbon::parse($doc->document_date) : now();
        $dueDate = $this->computeDueDateFromPO($po, $docDate);

        $invoiceData = [
            'supplier_id'     => (int)$doc->supplier_id,
            'branch_id'       => (int)($doc->store->branch_id ?? 0),
            'store_id'        => (int)$doc->store_id,           // ✅ جديد
            'invoice_no'      => (string)$doc->code,
            'invoice_date'    => $docDate->toDateString(),
            'due_date'        => $dueDate->toDateString(),
            'invoice_type'    => 'products',
            'payment_method'  => (string)($po->payment_method ?? ''),
            'reference_no'    => (string)($doc->code ?? ''),
            'notes'           => (string)($doc->notes ?? ''),
            // ❌ لا نرسل status → يعتمد على Default (new)
            'user_id'         => Auth::id(),
        ];

        $itemsData = [];

        foreach ($doc->items as $item) {
            $accBase = (float)($item->accepted_qty ?? 0);
            $accDisp = (float)($item->accepted_qty_display ?? 0);
            if ($accBase <= 0 || $accDisp <= 0) continue;

            $productId = (int)$item->product_id;
            $mapUnitId = (int)($item->unit_id ?? ($item->unit->id ?? 0));

            $key            = $productId . '|' . $mapUnitId;
            $unitPriceBase  = (float)($poPriceMap[$key]['unit_price'] ?? 0.0);
            $poQtyKey       = (float)($poPriceMap[$key]['po_qty'] ?? 0.0);
            $taxTotalPO     = (float)($poPriceMap[$key]['tax_amount'] ?? 0.0);

            $taxShare = $poQtyKey > 0 ? ($taxTotalPO * ($accBase / $poQtyKey)) : 0.0;

            $ratioBaseToDisp  = $accDisp > 0 ? ($accDisp / $accBase) : 1.0;
            $unitPriceDisplay = $ratioBaseToDisp > 0 ? ($unitPriceBase / $ratioBaseToDisp) : $unitPriceBase;

            $itemsData[] = [
                'item_type'       => 'product',
                'product_id'      => $productId,
                'product_unit_id' => (int)($item->accepted_unit_id ?? 0), // units.id
                'qty'             => round($accDisp, 6),
                'unit_price'      => round($unitPriceDisplay, 2),
                'discount'        => 0.0,
                'tax'             => round($taxShare, 2),
                'description'     => trim('Batch: ' . ($item->batch_no ?? '-') . ' | Exp: ' . ($item->expiry_date ?? '-')),
            ];
        }

        if (empty($itemsData)) {
            return [$invoiceData, []];
        }

        return [$invoiceData, $itemsData];
    }

    /** خريطة تسعير/ضرائب/كمية الـPO */
    private function buildPoPriceMap(PurchaseOrder $po): array
    {
        $map = [];
        foreach (($po->items ?? []) as $pit) {
            $productId = $pit->item_id ?? $pit->product_id ?? null;
            $mapUnitId = $pit->unit_id ?? ($pit->unit->id ?? null);

            if (!$productId || !$mapUnitId) continue;

            $key = $productId . '|' . $mapUnitId;

            $unitPrice = (float)($pit->last_price ?? $pit->unit_price ?? 0);
            $taxAmount = (float)($pit->tax_amount ?? $pit->tax ?? 0);
            $poQty     = (float)($pit->requested_qty ?? $pit->qty ?? $pit->quantity ?? 0);

            if (!isset($map[$key])) {
                $map[$key] = [
                    'unit_price' => $unitPrice,
                    'tax_amount' => $taxAmount,
                    'po_qty'     => $poQty,
                ];
            } else {
                $map[$key]['tax_amount'] += $taxAmount;
                $map[$key]['po_qty']     += $poQty;
                $map[$key]['unit_price'] = $unitPrice;
            }
        }
        return $map;
    }

    /** حساب تاريخ الاستحقاق من الـPO (Net X) وإلا +30 يوم */
    private function computeDueDateFromPO(?PurchaseOrder $po, Carbon $docDate): Carbon
    {
        if (!$po) return (clone $docDate)->addDays(30);

        $terms = (string)($po->payment_terms ?? '');
        if ($terms !== '') {
            if (preg_match('/\d+/', $terms, $m)) {
                $days = (int)$m[0];
                if ($days > 0 && $days <= 365) {
                    return (clone $docDate)->addDays($days);
                }
            }
        }
        return (clone $docDate)->addDays(30);
    }
}
