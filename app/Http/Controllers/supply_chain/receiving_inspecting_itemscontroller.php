<?php

namespace App\Http\Controllers\supply_chain;

use Illuminate\Support\Str;

use App\models\hr\Employee;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use App\Models\supply_chain\productunit;
use App\Models\Supply_Chain\ProductUnit as Unit;
use App\Models\supply_chain\receivinginspecting;
use App\Models\Suppliers\SuppliersData as Supplier;
use App\Models\Supply_Chain\ProductData as Product;
use App\Models\supply_chain\storemaindata as Store;
use App\Models\supply_chain\receivinginspectingitem;
use App\Models\purchases\purchaseorder as PurchaseOrder;
use App\Models\supply_chain\receivinginspectingapproval;
use App\Models\supply_chain\unit as UnitModel;

class receiving_inspecting_itemscontroller extends Controller
{
    public function index(Request $request)
    {
        // صفحة العرض فقط؛ البيانات تجي عبر datatable()
        return view('supply_chain.program.receiving_inspecting_items.index');
    }

    public function datatable(Request $request)
    {
        $status = $request->get('status');

        $q = receivinginspecting::query()
            ->with([
                'supplier:id,name_ar,name_en',
                'store:id,name_ar,name_en',
                'purchaseOrder:id,order_number',
            ])
            // <<< إجمالي التكلفة لكل محضر
            ->withSum('items as total_cost', 'cost_price')
            // ^ سيضيف عمود total_cost على الموديل
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), function ($qq) use ($status) {
                $qq->where('status', $status);
            })
            ->latest('id');

        return datatables()->of($q)
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier->name_ar ?? $row->supplier->name_en ?? '-';
            })
            ->addColumn('store_name', function ($row) {
                return $row->store->name_ar ?? $row->store->name_en ?? '-';
            })
            ->addColumn('po_code', function ($row) {
                return $row->purchaseOrder->order_number ?? '-';
            })
            ->editColumn('document_date', function ($row) {
                try {
                    return \Illuminate\Support\Carbon::parse($row->document_date)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return (string) $row->document_date;
                }
            })
            // <<< عمود إجمالي التكلفة (منسّق)
            ->addColumn('total_cost', function ($row) {
                $val = (float) ($row->total_cost ?? 0);
                return number_format($val, 2);
            })
            ->editColumn('status', function ($row) {
                $map = [
                    'pending'  => [trans('supply_chain_trans.status_pending'),  'bg-warning text-dark'],
                    'approved' => [trans('supply_chain_trans.status_approved'), 'bg-success'],
                    'rejected' => [trans('supply_chain_trans.status_rejected'), 'bg-danger'],
                ];
                [$label, $class] = $map[$row->status] ?? [$row->status, 'bg-secondary'];
                return '<span class="badge ' . $class . '">' . $label . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $showText = e(trans('supply_chain_trans.btn_view'));
                $editText = e(trans('supply_chain_trans.btn_edit'));
                $delText  = e(trans('supply_chain_trans.btn_delete'));

                $showBtn = '<button type="button" class="btn btn-sm btn-outline-primary me-1 btn-show" data-id="' . $row->id . '">' . $showText . '</button>';

                $buttons = '<div class="btn-group">' . $showBtn;

                if (($row->status ?? '') === 'pending') {
                    $editUrl = route('receiving_inspecting.edit', ['id' => $row->id]);
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-sm btn-outline-secondary me-1">' . $editText . '</a>';
                    $delBtn  = '<button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $row->id . '">' . $delText . '</button>';
                    $buttons .= $editBtn . $delBtn;
                }

                $buttons .= '</div>';
                return $buttons;
            })

            ->rawColumns(['status', 'actions'])
            ->toJson();
    }


    /**
     * محتوى المودال (عرض تفاصيل المحضر)
     */
    public function show($id)
    {
        $doc = \App\Models\supply_chain\receivinginspecting::with([
            'supplier:id,name_ar,name_en',
            'store:id,name_ar,name_en',
            'purchaseOrder:id,order_number',

            // << شيلنا item_code من هنا
            'items.product:id,system_code,barcode,name_ar,name_en',
            'items.acceptedUnit:id,name_ar,name_en',
            'items.unit.largeUnit:id,name_ar,name_en',
            'items.unit.smallUnit:id,name_ar,name_en',

            'approvals.employee:id,full_name,first_name_ar,second_name_ar,third_name_ar,fourth_name_ar,first_name_en,second_name_en,third_name_en,fourth_name_en',
        ])->findOrFail($id);

        $date     = $doc->document_date ? \Illuminate\Support\Carbon::parse($doc->document_date)->format('Y-m-d') : '-';
        $supplier = $doc->supplier->name_ar ?? $doc->supplier->name_en ?? '-';
        $store    = $doc->store->name_ar ?? $doc->store->name_en ?? '-';
        $po       = $doc->purchaseOrder->order_number ?? '-';
        $totalCost = $doc->items->sum(fn($it) => (float) ($it->cost_price ?? 0));
        $hasCost   = $doc->items->whereNotNull('cost_price')->isNotEmpty();

        return view('supply_chain.program.receiving_inspecting_items._show_modal', compact(
            'doc',
            'date',
            'supplier',
            'store',
            'po',
            'totalCost',
            'hasCost'
        ));
    }





    /**
     * حذف (Soft Delete)
     */
    public function destroy($id)
    {
        $doc = \App\Models\supply_chain\receivinginspecting::with(['items', 'approvals'])->findOrFail($id);

        if (($doc->status ?? '') !== 'pending') {
            return response()->json([
                'ok' => false,
                'message' => 'لا يمكن حذف محضر حالته ليست قيد الانتظار.'
            ], 422);
        }

        DB::transaction(function () use ($doc) {
            $doc->items()->delete();
            $doc->approvals()->delete();
            $doc->delete();
        });

        return response()->json([
            'ok'      => true,
            'message' => trans('supply_chain_trans.delete_ri_with_relations_success'),
        ]);
    }


    // توليد كود المحضر
    protected function nextRiCodeForCurrentYear(): string
    {
        $year   = now()->format('Y');
        $prefix = "RI-{$year}-";

        $lastCode = receivinginspecting::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->value('code');

        $next = 1;
        if ($lastCode) {
            $num = (int) substr($lastCode, strlen($prefix));
            $next = $num + 1;
        }

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    // جلب اعضاء لجنة الفحص والاستلام
    public function poCommittee($poId)
    {
        $po = \App\Models\Purchases\PurchaseOrder::with([
            'committees.employee',
        ])->findOrFail($poId);

        $members = $po->committees->map(function ($c) {
            $e = $c->employee;

            $eName = $e->name_ar ?? $e->name_en ?? $e->full_name ?? $e->name ?? null;
            $eCode = $e->code    ?? $e->employee_code ?? $e->emp_code ?? null;
            $display = trim(($eName ?? '') . ($eCode ? " ({$eCode})" : ''));

            return [
                'employee_id'      => $c->employee_id,
                'employee_name'    => $display,
                'is_chairman'      => (bool)($c->is_chair ?? $c->is_chairman ?? $c->chairman ?? 0),
                'responsibilities' => $c->responsibilities ?? $c->job_position ?? $c->role ?? '',
                'status'           => $c->status ?? 'pending',
                'remarks'          => $c->remarks ?? null,
            ];
        })->values();

        return response()->json(['committee' => $members]);
    }
    public function productUnits(Request $request)
    {
        $productId   = (int) $request->query('p'); // Required
        $mapRowId    = (int) $request->query('m'); // product_units.id لسطر الـ PO (اختياري)
        $fallbackUId = (int) $request->query('u'); // units.id fallback (اختياري)

        // روابط وحدات الصنف
        $links = \App\Models\supply_chain\productunit::where('product_id', $productId)->get();
        if ($links->isEmpty()) {
            return response()->json(['options' => []]);
        }

        // parents: لكل وحدة صغيرة -> [الوحدة الكبيرة, معامل التحويل, id السجل الرابط]
        // children: لكل وحدة كبيرة -> [[الوحدة الصغيرة, المعامل, id السجل], ...]
        $parents  = [];
        $children = [];
        foreach ($links as $lnk) {
            $L  = (int) $lnk->unit_large_id;
            $S  = (int) $lnk->unit_small_id;
            $R  = (float) $lnk->conversion_rate; // 1 Large = R Small
            $ID = (int) $lnk->id;

            if ($S) {
                $parents[$S] = ['large' => $L, 'rate' => $R, 'id' => $ID];
            }
            if ($L) {
                $children[$L][] = ['small' => $S, 'rate' => $R, 'id' => $ID];
            }
        }

        // نقطة البدء
        $startUnitId = null;
        if ($mapRowId) {
            $mapRow = $links->firstWhere('id', $mapRowId);
            if ($mapRow) {
                $startUnitId = (int) ($mapRow->unit_large_id ?: $mapRow->unit_small_id);
            }
        }
        if (!$startUnitId) {
            $first = $links->first();
            $startUnitId = $fallbackUId ?: (int) ($first->unit_large_id ?: $first->unit_small_id);
        }
        if (!$startUnitId) {
            return response()->json(['options' => []]);
        }

        // اصعد لأعلى وحدة (الأساس)
        $top = $startUnitId;
        $guard = 0;
        while (isset($parents[$top]) && $guard < 50) {
            $p = $parents[$top]['large'] ?? null;
            if (!$p) break;
            $top = (int) $p;
            $guard++;
        }

        // أسماء الوحدات
        $unitIds = collect([$top]);
        foreach ($children as $L => $arr) {
            $unitIds->push($L);
            foreach ($arr as $edge) {
                $unitIds->push($edge['small']);
            }
        }
        $unitIds = $unitIds->filter()->unique()->values();

        $unitsMap = \App\Models\supply_chain\unit::whereIn('id', $unitIds)->get()
            ->mapWithKeys(function ($u) {
                $name = $u->name_ar ?? $u->name_en ?? ('#' . $u->id);
                return [$u->id => $name];
            });

        // BFS من الأعلى لحساب rate_to_base وبناء الخيارات
        $options = [];
        $visited = [];
        $queue   = [[$top, 1.0]]; // [units.id, rate_to_base]
        $visited[$top] = true;

        while (!empty($queue)) {
            [$uid, $rate] = array_shift($queue);

            // map_id: id من product_units الذي يربط هذه الوحدة مع أبيها (إن وجِد)
            $mapId = null;
            if (isset($parents[$uid])) {
                $mapId = (int) ($parents[$uid]['id'] ?? null);
            } elseif ($uid === $top && $mapRowId) {
                // للوحدة الأساسية لا يوجد أب؛ ن fallback لسطر الـPO الممرّر
                $mapId = (int) $mapRowId;
            }

            $options[] = [
                'id'           => (int) $uid,                 // units.id (يُحفظ في accepted_unit_id)
                'name'         => $unitsMap[$uid] ?? ('#' . $uid),
                'rate_to_base' => (float) $rate,              // كم "وحدة مختارة" في "وحدة الأساس"
                'map_id'       => $mapId,                     // product_units.id المناسب لهذه الوحدة
            ];

            if (!empty($children[$uid])) {
                foreach ($children[$uid] as $edge) {
                    $sId = (int) $edge['small'];
                    $edgeRate = (float) $edge['rate'];
                    if ($sId && empty($visited[$sId])) {
                        $visited[$sId] = true;
                        $queue[] = [$sId, $rate * $edgeRate];
                    }
                }
            }
        }

        // رتب بالمعامل (الأساس أولاً = 1)
        usort($options, function ($a, $b) {
            return $a['rate_to_base'] <=> $b['rate_to_base'];
        });

        return response()->json([
            'product_id'   => $productId,
            'base_unit_id' => $top,      // units.id للوحدة الأساسية
            'options'      => $options,  // كل عنصر: id (units.id) + name + rate_to_base + map_id (product_units.id)
        ]);
    }




    public function create(Request $request)
    {
        $today    = now()->toDateString();
        $nextCode = $this->nextRiCodeForCurrentYear();

        return view('supply_chain.program.receiving_inspecting_items.create', compact('today', 'nextCode'));
    }
    /**
     * خريطة تسعير/ضرائب/كمية الـPO حسب (product_id|unit_id "Mapping").
     * المفتاح: "<product_id>|<product_unit_mapping_id>"
     * القيم: ['unit_price' => float, 'tax_amount' => float, 'po_qty' => float]
     */
    protected function buildPoPriceMap(\App\Models\Purchases\PurchaseOrder $po): array
    {
        $map = [];

        foreach (($po->items ?? []) as $pit) {
            $productId = $pit->item_id ?? $pit->product_id ?? null;
            $mapUnitId = $pit->unit_id ?? ($pit->unit->id ?? null); // تأكيد أنه mapping id

            if (!$productId || !$mapUnitId) {
                // لو مفيش مابينج واضح، نتجاهل السطر لتفادي مفاتيح مبهمة
                continue;
            }

            $key = $productId . '|' . $mapUnitId;

            // بدائل أسماء الحقول
            $unitPrice = (float)($pit->last_price ?? $pit->unit_price ?? 0);
            $taxAmount = (float)($pit->tax_amount ?? $pit->tax ?? 0);
            $poQty     = (float)($pit->requested_qty ?? $pit->qty ?? $pit->quantity ?? 0);

            // تجميع لو تكرر نفس (product|mapping) في الـPO
            if (!isset($map[$key])) {
                $map[$key] = [
                    'unit_price' => $unitPrice, // لو اختلف عبر الأسطر، هتفضل قيمة آخر سطر
                    'tax_amount' => $taxAmount,
                    'po_qty'     => $poQty,     // بالأساس (وحدة الـPO)
                ];
            } else {
                $map[$key]['tax_amount'] += $taxAmount;
                $map[$key]['po_qty']     += $poQty;
                // ممكن تعمل متوسط مرجّح للسعر لو بتتوقع اختلافًا:
                // $map[$key]['unit_price'] = $unitPrice; // أو اترك آخر قيمة
            }
        }

        return $map;
    }

    protected function unitRatesToBase(int $productId): array
    {
        $links = \App\Models\supply_chain\productunit::where('product_id', $productId)->get();
        if ($links->isEmpty()) return [];

        // parents: key=unit_small_id => [large_id, R]
        // children: key=unit_large_id => [[small_id, R], ...]
        $parents = [];
        $children = [];
        foreach ($links as $lnk) {
            $L = (int) $lnk->unit_large_id;
            $S = (int) $lnk->unit_small_id;
            $R = (float) $lnk->conversion_rate; // 1 Large = R Small
            if ($S) $parents[$S]   = [$L, $R];
            if ($L) $children[$L][] = [$S, $R];
        }

        // حدّد أعلى وحدة (الأساس)
        $top = (int) ($links->first()->unit_large_id ?: $links->first()->unit_small_id);
        $guard = 0;
        while (isset($parents[$top]) && $guard < 20) {
            [$pL, $pR] = $parents[$top];
            if (!$pL) break;
            $top = (int) $pL;
            $guard++;
        }

        // BFS لإنتاج rate_to_base لكل وحدة
        $rates = [$top => 1.0];
        $visited = [$top => true];
        $queue = [[$top, 1.0]];

        while (!empty($queue)) {
            [$uid, $rate] = array_shift($queue);
            if (!empty($children[$uid])) {
                foreach ($children[$uid] as [$sId, $edgeRate]) {
                    if ($sId && empty($visited[$sId])) {
                        $visited[$sId] = true;
                        $nextRate = $rate * (float) $edgeRate;
                        $rates[(int)$sId] = $nextRate;
                        $queue[] = [(int)$sId, $nextRate];
                    }
                }
            }
        }

        return $rates; // units.id => rate_to_base
    }

    /**
     * حساب معامل التحويل (rate_to_base) لوحدة عرض مختارة accepted_unit_id
     * بحيث يرجع كم "وحدة أساس" = 1 من هذه الوحدة.
     *
     * @param int $productId
     * @param int $accUnitId   units.id
     * @return float           rate_to_base
     */
    private function calculateUnitRate(int $productId, int $accUnitId): float
    {
        if ($accUnitId <= 0) {
            return 1.0;
        }

        // روابط التحويل من جدول product_units
        $links = \App\Models\supply_chain\productunit::where('product_id', $productId)->get();
        if ($links->isEmpty()) {
            return 1.0; // لا توجد روابط تحويل
        }

        // بناء شجرة التحويل
        $parents  = []; // small => [large, R]
        $children = []; // large => [[small, R], ...]
        foreach ($links as $lnk) {
            $L = (int) $lnk->unit_large_id;
            $S = (int) $lnk->unit_small_id;
            $R = (float) $lnk->conversion_rate; // 1 Large = R Small
            if ($S) {
                $parents[$S] = [$L, $R];
            }
            if ($L) {
                $children[$L][] = [$S, $R];
            }
        }

        // تحديد أعلى وحدة كأساس (Top-most Unit)
        $top = (int)($links->first()->unit_large_id ?: $links->first()->unit_small_id);
        $guard = 0;
        while (isset($parents[$top]) && $guard < 20) {
            [$pL, $pR] = $parents[$top];
            if (!$pL) break;
            $top = (int)$pL;
            $guard++;
        }

        // BFS لإيجاد معامل التحويل لكل وحدة
        $rates   = [$top => 1.0];
        $visited = [$top => true];
        $queue   = [[$top, 1.0]];

        while (!empty($queue)) {
            [$uid, $rate] = array_shift($queue);
            if (!empty($children[$uid])) {
                foreach ($children[$uid] as [$sId, $edgeRate]) {
                    if ($sId && empty($visited[$sId])) {
                        $visited[$sId] = true;
                        $nextRate = $rate * (float)$edgeRate;
                        $rates[(int)$sId] = $nextRate;
                        $queue[] = [(int)$sId, $nextRate];
                    }
                }
            }
        }

        // رجّع معامل accepted_unit_id لو موجود، وإلا 1.0
        return (float)($rates[$accUnitId] ?? 1.0);
    }
    private function formatResponsibilities($value): ?string
    {
        if (is_array($value)) {
            $vals = array_values(array_filter(array_map('trim', $value), fn($v) => $v !== ''));
            return $vals ? implode('، ', $vals) : null;
        }
        if (is_string($value)) {
            $v = trim($value);
            return $v !== '' ? $v : null;
        }
        return null;
    }

    public function store(Request $request)
    {
        $table      = (new receivinginspecting)->getTable();
        $unitsTable = (new \App\Models\supply_chain\unit)->getTable();

        $validated = $request->validate([
            'supplier_id'       => ['nullable', 'integer'],
            'store_id'          => ['required', 'integer'],
            'purchase_order_id' => ['required', 'integer'],
            'document_date'     => ['required', 'date'],
            'code'              => ['required', 'regex:/^RI-\d{4}-\d{5}$/', "unique:{$table},code"],

            'items'                           => ['required', 'array', 'min:1'],
            'items.*.product_id'              => ['required', 'integer'],
            'items.*.unit_id'                 => ['required', 'integer'],
            'items.*.accepted_unit_id'        => ['required', 'integer', Rule::exists($unitsTable, 'id')],
            'items.*.batch_no'                => ['nullable', 'string', 'max:50'],
            'items.*.expiry_date'             => ['nullable', 'date'],
            'items.*.request_qty'             => ['nullable', 'numeric', 'min:0'],
            'items.*.po_qty'                  => ['required', 'numeric', 'min:0'],
            'items.*.delivered_qty'           => ['required', 'numeric', 'min:0'],
            'items.*.accepted_qty'            => ['required', 'numeric', 'min:0'],
            'items.*.rejected_qty'            => ['nullable', 'numeric', 'min:0'],
            'items.*.delivered_qty_input'     => ['nullable', 'numeric', 'min:0'],
            'items.*.accepted_qty_input'      => ['nullable', 'numeric', 'min:0'],
            'items.*.cost_price'              => ['nullable', 'numeric', 'min:0'],
            'items.*.rejection_reason'        => ['nullable', 'string', 'max:255'],

            'approvals'                       => ['required', 'array', 'min:1'],
            'approvals.*.employee_id'         => ['required', 'integer'],
            'approvals.*.responsibilities'    => ['nullable'],
            'approvals.*.is_chairman'         => ['nullable', 'boolean'],
        ]);

        $po = \App\Models\Purchases\PurchaseOrder::with(['items'])->findOrFail($request->purchase_order_id);
        $supplierIdFromPO = (int)($po->supplier_id ?? 0);
        if ($supplierIdFromPO <= 0) {
            return back()->withInput()->withErrors(['purchase_order_id' => 'لا يوجد مورد مرتبط بأمر الشراء المحدد.']);
        }

        try {
            DB::transaction(function () use ($request, $po, $supplierIdFromPO) {

                $doc = receivinginspecting::create([
                    'supplier_id'       => $supplierIdFromPO,
                    'store_id'          => $request->store_id,
                    'purchase_order_id' => $request->purchase_order_id,
                    'code'              => $request->code,
                    'document_date'     => $request->document_date,
                    'notes'             => $request->notes,
                    'receipt_notes'     => $request->receipt_notes,
                    'status'            => 'pending',
                ]);

                $poPriceMap = $this->buildPoPriceMap($po);

                $sumDeliveredByProduct = [];
                $sumAcceptedByProduct  = [];
                $poLimitByProduct      = [];

                foreach ($request->input('items', []) as $i) {
                    if (empty($i['product_id']) || empty($i['unit_id'])) continue;

                    $productId = (int)$i['product_id'];
                    $mapUnitId = (int)$i['unit_id'];
                    $po_base   = max(0, (float)($i['po_qty'] ?? 0));

                    if (!isset($poLimitByProduct[$productId])) {
                        $poLimitByProduct[$productId] = $po_base;
                    }

                    $delivered_base = max(0, (float)($i['delivered_qty'] ?? 0));
                    $accepted_base  = max(0, (float)($i['accepted_qty']  ?? 0));

                    $usedDel  = $sumDeliveredByProduct[$productId] ?? 0.0;
                    $allowDel = max(0, ($poLimitByProduct[$productId] ?? $po_base) - $usedDel);
                    if ($delivered_base > $allowDel) $delivered_base = $allowDel;

                    if ($accepted_base > $delivered_base) $accepted_base = $delivered_base;
                    $usedAcc        = $sumAcceptedByProduct[$productId] ?? 0.0;
                    $newDelSum      = $usedDel + $delivered_base;
                    $allowAccGlobal = max(0, $newDelSum - $usedAcc);
                    if ($accepted_base > $allowAccGlobal) $accepted_base = $allowAccGlobal;

                    $rejected_base = max(0, $delivered_base - $accepted_base);

                    $priceRow   = $this->resolvePriceRow($poPriceMap, $productId, $mapUnitId, $po_base);
                    $unit_price = (float)($priceRow['unit_price'] ?? 0.0);
                    $tax_total  = (float)($priceRow['tax_amount'] ?? 0.0);
                    $po_qty_key = (float)($priceRow['po_qty']     ?? $po_base);
                    $tax_share  = $po_qty_key > 0 ? ($tax_total * ($accepted_base / $po_qty_key)) : 0.0;
                    $line_total = ($accepted_base * $unit_price) + $tax_share;

                    $accUnitId = (int)($i['accepted_unit_id'] ?? 0);
                    $rSel = $this->calculateUnitRate($productId, $accUnitId);

                    $delDisp = round($delivered_base * $rSel, 3);
                    $accDisp = round($accepted_base  * $rSel, 3);
                    $rejDisp = round($rejected_base  * $rSel, 3);

                    receivinginspectingitem::create([
                        'receiving_inspecting_id' => $doc->id,
                        'product_id'              => $productId,
                        'unit_id'                 => $mapUnitId,
                        'accepted_unit_id'        => $accUnitId,
                        'batch_no'                => $i['batch_no']      ?? null,
                        'expiry_date'             => !empty($i['expiry_date']) ? $i['expiry_date'] : null,
                        'request_qty'             => max(0, (float)($i['request_qty'] ?? 0)),
                        'po_qty'                  => $po_base,
                        'delivered_qty'           => round($delivered_base, 6),
                        'accepted_qty'            => round($accepted_base,  6),
                        'rejected_qty'            => round($rejected_base,  6),
                        'delivered_qty_display'   => $delDisp,
                        'accepted_qty_display'    => $accDisp,
                        'rejected_qty_display'    => $rejDisp,
                        'cost_price'              => round($line_total, 2),
                        'rejection_reason'        => $i['rejection_reason'] ?? null,
                    ]);

                    $sumDeliveredByProduct[$productId] = $usedDel + $delivered_base;
                    $sumAcceptedByProduct[$productId]  = $usedAcc + $accepted_base;
                }

                foreach ($request->input('approvals', []) as $a) {
                    if (empty($a['employee_id'])) continue;
                    $respStr = $this->formatResponsibilities($a['responsibilities'] ?? null);
                    receivinginspectingapproval::create([
                        'receiving_inspecting_id' => $doc->id,
                        'employee_id'             => (int)$a['employee_id'],
                        'is_chairman'             => !empty($a['is_chairman']) ? 1 : 0,
                        'responsibilities'        => $respStr,
                        'status'                  => 'pending',
                    ]);
                }
            });
        } catch (QueryException $e) {
            if ((int)$e->getCode() === 23000) {
                return back()->withInput()->withErrors(['code' => trans('supply_chain_trans.code_taken_reload')]);
            }
            throw $e;
        }
  $action = $request->input('requisition_action'); // 'close' أو 'open'
    if (in_array($action, ['close', 'open'], true) && !empty($po->requisition_id)) {
        // NOTE: نستخدم موديل طلب الاحتياج الذي أعطيته (namespace الحرفي)
        $req = \App\Models\purchases\purchaserequisition::find($po->requisition_id);
        if ($req) {
            $req->status = ($action === 'close') ? 'closed' : 'receiving_quotes';
            $req->save();
        }
    }
        return redirect()->route('receiving_inspecting.index', [app()->getLocale()])
            ->with('success', trans('supply_chain_trans.created_success'));
    }





    // ============ AJAX helpers ============

    public function availablePOs(Request $request)
    {
        $q = $request->get('q');

        $poTable  = (new PurchaseOrder)->getTable();
        $riTable  = (new receivinginspecting)->getTable(); // <— تم تصحيحه هنا
        $supTable = (new \App\Models\Suppliers\SuppliersData)->getTable();

        $rows = PurchaseOrder::query()
            ->leftJoin($riTable . ' as ri', function ($join) use ($poTable) {
                $join->on('ri.purchase_order_id', '=', $poTable . '.id')
                    ->whereNull('ri.deleted_at');
            })
            ->join($supTable . ' as s', 's.id', '=', $poTable . '.supplier_id')
            ->whereNull('ri.id')
            ->when($q, function ($qq) use ($poTable, $supTable, $q) {
                $qq->where($poTable . '.order_number', 'like', "%$q%")
                    ->orWhere('s.name_ar', 'like', "%$q%")
                    ->orWhere('s.name_en', 'like', "%$q%");
            })
            ->select(
                $poTable . '.id',
                $poTable . '.order_number',
                's.name_ar as supplier_name_ar',
                's.name_en as supplier_name_en'
            )
            ->orderBy($poTable . '.id', 'desc')
            ->get()
            ->map(fn($r) => [
                'id'   => $r->id,
                'text' => $r->order_number . ' - ' . ($r->supplier_name_ar ?? $r->supplier_name_en ?? '')
            ]);

        return response()->json(['results' => $rows]);
    }



    public function poItems($poId)
    {
        $po = \App\Models\Purchases\PurchaseOrder::with([
            'items.product',
            'items.unit.largeUnit',
            'items.unit.smallUnit',
            // تحميل المورّد لإرجاعه للواجهة
            'supplier:id,name_ar,name_en',
        ])->findOrFail($poId);

        $reqMap = [];
        if (!empty($po->requisition_id) && Schema::hasTable('purchase_requisition_items')) {
            $reqRows = DB::table('purchase_requisition_items')
                ->where('requisition_id', $po->requisition_id)
                ->select('item_id', 'unit_id', 'requested_qty')
                ->get();

            foreach ($reqRows as $r) {
                $qty = (float)($r->requested_qty ?? 0);
                if (!is_null($r->unit_id)) {
                    $reqMap[$r->item_id . '|' . $r->unit_id] = $qty;
                }
                if (!isset($reqMap[$r->item_id])) {
                    $reqMap[$r->item_id] = $qty;
                }
            }
        }

        $items = $po->items->map(function ($it) use ($reqMap) {
            $product     = $it->product ?? null;
            $productId   = $it->item_id ?? $it->product_id ?? ($product->id ?? null);
            $productCode = $product->code ?? $product->item_code ?? $product->barcode ?? (string)($product->id ?? '');
            $productName = $product->name_ar ?? $product->name_en ?? $product->name ?? '';

            $pu       = $it->unit ?? null;
            $unitId   = $pu->id ?? ($it->unit_id ?? null);
            $unitName = '';
            if ($pu) {
                $type = strtolower((string)($pu->unit_type ?? ''));
                if (in_array($type, ['large', 'big'])) {
                    $unitName = $pu->largeUnit->name_ar ?? $pu->largeUnit->name_en ?? '';
                } elseif ($type === 'small') {
                    $unitName = $pu->smallUnit->name_ar ?? $pu->smallUnit->name_en ?? '';
                }
                if (!$unitName) {
                    $unitName = $pu->largeUnit->name_ar
                        ?? $pu->smallUnit->name_ar
                        ?? $pu->largeUnit->name_en
                        ?? $pu->smallUnit->name_en
                        ?? '';
                }
            }

            $poQty      = (float)($it->requested_qty ?? 0);
            $unitPrice  = (float)($it->last_price   ?? 0);
            $taxAmount  = (float)($it->tax_amount   ?? 0);

            $reqQty = 0.0;
            if (!is_null($productId)) {
                $keyUnit = $productId . '|' . ($unitId ?? '');
                if (isset($reqMap[$keyUnit])) {
                    $reqQty = (float)$reqMap[$keyUnit];
                } elseif (isset($reqMap[$productId])) {
                    $reqQty = (float)$reqMap[$productId];
                }
            }

            $initialTotal = ($poQty * $unitPrice) + $taxAmount;

            return [
                'product_id'   => $productId,
                'product_code' => $productCode,
                'product_name' => $productName,
                'unit_id'      => $unitId,
                'unit_name'    => $unitName,
                'request_qty'  => $reqQty,
                'po_qty'       => $poQty,
                'unit_price'   => $unitPrice,
                'tax_amount'   => $taxAmount,
                'initial_line_total' => round($initialTotal, 2),
            ];
        })->values();

        // تجهيز بيانات المورّد للواجهة (للعرض فقط + hidden للحفظ)
        $supplierText = $po->supplier->name_ar ?? $po->supplier->name_en ?? '';
        $supplierId   = $po->supplier_id ? (int) $po->supplier_id : null;

        return response()->json([
            'items'    => $items,
            'supplier' => [
                'id'   => $supplierId,
                'text' => $supplierText,
            ],
        ]);
    }


    public function searchSuppliers(Request $request)
    {
        $q = $request->get('q');
        $rows = Supplier::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where('name_ar', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%");
            })
            ->select('id', 'name_ar', 'name_en')
            ->orderBy('id', 'desc')->get()
            ->map(fn($r) => ['id' => $r->id, 'text' => trim(($r->name_ar ?? $r->name_en) . '')]);

        return response()->json(['results' => $rows]);
    }

    public function searchStores(Request $request)
    {
        $q = $request->get('q');
        $rows = Store::query()
            ->when($q, fn($qq) => $qq->where('name_ar', 'like', "%$q%")->orWhere('name_en', 'like', "%$q%"))
            ->select('id', 'name_ar', 'name_en')
            ->orderBy('id', 'desc')->get()
            ->map(fn($r) => ['id' => $r->id, 'text' => trim(($r->name_ar ?? $r->name_en) . '')]);

        return response()->json(['results' => $rows]);
    }

    public function searchEmployees(Request $request)
    {
        $q = trim((string)$request->get('q'));

        $rows = \App\models\hr\Employee::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('full_name', 'like', "%{$q}%")
                    ->orWhere('first_name_ar', 'like', "%{$q}%")
                    ->orWhere('first_name_en', 'like', "%{$q}%")
                    ->orWhere('employee_code', 'like', "%{$q}%");
            })
            ->select('id', 'full_name')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'text' => ($r->full_name ?? '')]);

        return response()->json(['results' => $rows]);
    }
    public function edit($id)
    {
        $doc = receivinginspecting::with([
            'supplier:id,name_ar,name_en',
            'store:id,name_ar,name_en',
            'purchaseOrder:id,order_number,supplier_id',
            'items.product:id,name_ar,name_en,barcode',
            'items.unit.largeUnit:id,name_ar,name_en',
            'items.unit.smallUnit:id,name_ar,name_en',
            'items.acceptedUnit:id,name_ar,name_en',
            'approvals.employee:id,full_name,first_name_ar,second_name_ar,third_name_ar,fourth_name_ar,first_name_en,second_name_en,third_name_en,fourth_name_en',
        ])->findOrFail($id);
        if (($doc->status ?? '') !== 'pending') {
            return redirect()
                ->route('receiving_inspecting.index', [app()->getLocale()])
                ->withErrors(['code' => 'لا يمكن تعديل محضر حالته ليست قيد الانتظار.']);
        }

        // سند المرجع (PO) + خريطة الأسعار/الضرائب
        $po = PurchaseOrder::with(['items'])->findOrFail($doc->purchase_order_id);
        $poPriceMap = $this->buildPoPriceMap($po); // نفس الدالة المستخدمة في store

        $today = Carbon::now()->format('Y-m-d');

        // تجهيز بيانات الـ Select2 المُعطّلة (supplier/store/po)
        $supplierText = $doc->supplier->name_ar ?? $doc->supplier->name_en ?? '';
        $storeText    = $doc->store->name_ar ?? $doc->store->name_en ?? '';
        $poText       = $po->order_number ?? ('#' . $po->id);

        // تحويل البنود لهيئة مناسبة للـJS (تشبه poItems + قيم السند الحالي)
        $itemsForEdit = $doc->items->map(function ($it) use ($poPriceMap) {
            $prod   = $it->product;
            $pu     = $it->unit; // ProductUnit mapping (سطر الـ PO)
            $unitId = $pu->id ?? $it->unit_id;

            $code = $prod->item_code ?? $prod->barcode ?? (string)($prod->id ?? '');
            $name = $prod->name_ar ?? $prod->name_en ?? '';

            // اسم وحدة الـ PO للعرض (زي create)
            $unitName = '';
            if ($pu) {
                $unitName = $pu->largeUnit->name_ar
                    ?? $pu->smallUnit->name_ar
                    ?? $pu->largeUnit->name_en
                    ?? $pu->smallUnit->name_en
                    ?? '';
            }

            $key       = ($it->product_id ?? $prod->id) . '|' . $unitId;
            $unitPrice = (float)($poPriceMap[$key]['unit_price'] ?? 0);
            $taxAmount = (float)($poPriceMap[$key]['tax_amount'] ?? 0);

            return [
                'product_id'           => (int)($it->product_id),
                'product_code'         => $code,
                'product_name'         => $name,

                'unit_id'              => (int)$unitId,
                'unit_name'            => $unitName,

                'request_qty'          => (float)$it->request_qty, // للعرض فقط
                'po_qty'               => (float)$it->po_qty,      // أساس

                // القيم الأساسية المخزَّنة
                'delivered_qty_base'   => (float)$it->delivered_qty,
                'accepted_qty_base'    => (float)$it->accepted_qty,
                'rejected_qty_base'    => (float)$it->rejected_qty,

                // وحدة العرض المختارة سابقًا
                'accepted_unit_id'     => (int)($it->accepted_unit_id ?? 0),

                // بيانات إضافية للسطر
                'batch_no'             => $it->batch_no,
                'expiry_date'          => $it->expiry_date ? Carbon::parse($it->expiry_date)->format('Y-m-d') : null,

                // تكلفة السطر الحالية (للعرض – سيتم إعادة حسابها JS عند التعديل)
                'cost_price'           => (float)($it->cost_price ?? 0),
                'rejection_reason'     => (string)($it->rejection_reason ?? ''),

                // لوجيك إعادة حساب التكلفة JS
                'unit_price'           => $unitPrice,
                'tax_amount'           => $taxAmount,
            ];
        })->values();

        // اللجنة الحالية → مصفوفة init للـJS
        $committeeInit = $doc->approvals->map(function ($ap) {
            $e = $ap->employee;
            $full = $e->full_name
                ?? trim(($e->first_name_ar . ' ' . $e->second_name_ar . ' ' . $e->third_name_ar . ' ' . $e->fourth_name_ar))
                ?? trim(($e->first_name_en . ' ' . $e->second_name_en . ' ' . $e->third_name_en . ' ' . $e->fourth_name_en))
                ?? ('#' . $ap->employee_id);
            // المسؤوليات كانت محفوظة كنص؛ نحاول تقسيمها
            $resp = $ap->responsibilities ?: '';
            $arr  = array_values(array_filter(preg_split('/\s*[،,]\s*/u', $resp)));
            return [
                'employee_id'   => (int)$ap->employee_id,
                'employee_name' => $full,
                'responsibilities' => $arr,
                'is_chairman'   => (bool)$ap->is_chairman,
            ];
        })->values();

        return view('supply_chain.program.receiving_inspecting_items.edit', compact(
            'doc',
            'today',
            'supplierText',
            'storeText',
            'poText',
            'itemsForEdit',
            'committeeInit',
            'po',
            'poPriceMap'
        ));
    }
    /**
     * يرجّع صف التسعير للـ (product|mapUnit) من خريطة الـ PO،
     * أو أول صف لنفس المنتج لو المابينج المرسل مش موجود (لأي سبب).
     */
    private function resolvePriceRow(array $poPriceMap, int $productId, int $mapUnitId, float $po_base): array
    {
        $key = $productId . '|' . $mapUnitId;
        if (isset($poPriceMap[$key])) {
            return $poPriceMap[$key];
        }

        // fallback: أول صف لنفس المنتج
        foreach ($poPriceMap as $k => $row) {
            [$pid] = array_pad(explode('|', $k, 2), 2, null);
            if ((int)$pid === $productId) {
                return $row;
            }
        }

        // fallback أخير
        return ['unit_price' => 0.0, 'tax_amount' => 0.0, 'po_qty' => $po_base];
    }

    public function update(Request $request, $id)
    {
        $doc = receivinginspecting::findOrFail($id);

        $table         = $doc->getTable();
        $unitsTable    = (new UnitModel)->getTable();
        $itemsTable    = (new receivinginspectingitem)->getTable();
        $approvalsTable = (new receivinginspectingapproval)->getTable();

        $request->validate([
            'supplier_id'       => ['nullable', 'integer'],
            'store_id'          => ['required', 'integer'],
            'purchase_order_id' => ['required', 'integer'],
            'document_date'     => ['required', 'date'],
            'code' => [
                'required',
                'regex:/^RI-\d{4}-\d{5}$/',
                Rule::unique($table, 'code')->ignore($doc->id)->whereNull('deleted_at'),
            ],
            'items'                           => ['required', 'array', 'min:1'],
            'items.*.product_id'              => ['required', 'integer'],
            'items.*.unit_id'                 => ['required', 'integer'],
            'items.*.accepted_unit_id'        => ['required', 'integer', Rule::exists($unitsTable, 'id')],
            'items.*.batch_no'                => ['nullable', 'string', 'max:50'],
            'items.*.expiry_date'             => ['nullable', 'date'],
            'items.*.request_qty'             => ['nullable', 'numeric', 'min:0'],
            'items.*.po_qty'                  => ['required', 'numeric', 'min:0'],
            'items.*.delivered_qty'           => ['required', 'numeric', 'min:0'],
            'items.*.accepted_qty'            => ['required', 'numeric', 'min:0'],
            'items.*.rejected_qty'            => ['nullable', 'numeric', 'min:0'],
            'items.*.delivered_qty_input'     => ['nullable', 'numeric', 'min:0'],
            'items.*.accepted_qty_input'      => ['nullable', 'numeric', 'min:0'],
            'items.*.cost_price'              => ['nullable', 'numeric', 'min:0'],
            'items.*.rejection_reason'        => ['nullable', 'string', 'max:255'],
            'approvals'                       => ['required', 'array', 'min:1'],
            'approvals.*.employee_id'         => ['required', 'integer'],
            'approvals.*.responsibilities'    => ['nullable'],
            'approvals.*.is_chairman'         => ['nullable', 'boolean'],
        ]);

        $po = PurchaseOrder::with(['items'])->findOrFail($request->purchase_order_id);
        $supplierIdFromPO = (int)($po->supplier_id ?? 0);
        if ($supplierIdFromPO <= 0) {
            return back()->withInput()->withErrors(['purchase_order_id' => 'لا يوجد مورد مرتبط بأمر الشراء المحدد.']);
        }

        try {

            DB::transaction(function () use ($request, $doc, $po, $supplierIdFromPO, $itemsTable, $approvalsTable) {

                $doc->update([
                    'supplier_id'       => $supplierIdFromPO,
                    'store_id'          => $request->store_id,
                    'purchase_order_id' => $request->purchase_order_id,
                    'code'              => $request->code,
                    'document_date'     => $request->document_date,
                    'notes'             => $request->notes,
                    'receipt_notes'     => $request->receipt_notes,
                ]);

                DB::table($itemsTable)->where('receiving_inspecting_id', $doc->id)->delete();
                DB::table($approvalsTable)->where('receiving_inspecting_id', $doc->id)->delete();

                $poPriceMap = $this->buildPoPriceMap($po);

                $sumDeliveredByProduct = [];
                $sumAcceptedByProduct  = [];
                $poLimitByProduct      = [];

                foreach ($request->input('items', []) as $i) {
                    if (empty($i['product_id']) || empty($i['unit_id'])) continue;

                    $productId = (int)$i['product_id'];
                    $mapUnitId = (int)$i['unit_id'];
                    $po_base   = max(0, (float)($i['po_qty'] ?? 0));

                    if (!isset($poLimitByProduct[$productId])) {
                        $poLimitByProduct[$productId] = $po_base;
                    }

                    $delivered_base = max(0, (float)($i['delivered_qty'] ?? 0));
                    $accepted_base  = max(0, (float)($i['accepted_qty']  ?? 0));

                    $usedDel  = $sumDeliveredByProduct[$productId] ?? 0.0;
                    $allowDel = max(0, ($poLimitByProduct[$productId] ?? $po_base) - $usedDel);
                    if ($delivered_base > $allowDel) $delivered_base = $allowDel;

                    if ($accepted_base > $delivered_base) $accepted_base = $delivered_base;
                    $usedAcc        = $sumAcceptedByProduct[$productId] ?? 0.0;
                    $newDelSum      = $usedDel + $delivered_base;
                    $allowAccGlobal = max(0, $newDelSum - $usedAcc);
                    if ($accepted_base > $allowAccGlobal) $accepted_base = $allowAccGlobal;

                    $rejected_base = max(0, $delivered_base - $accepted_base);

                    $priceRow   = $this->resolvePriceRow($poPriceMap, $productId, $mapUnitId, $po_base);
                    $unit_price = (float)($priceRow['unit_price'] ?? 0.0);
                    $tax_total  = (float)($priceRow['tax_amount'] ?? 0.0);
                    $po_qty_key = (float)($priceRow['po_qty']     ?? $po_base);
                    $tax_share  = $po_qty_key > 0 ? ($tax_total * ($accepted_base / $po_qty_key)) : 0.0;
                    $line_total = ($accepted_base * $unit_price) + $tax_share;

                    $accUnitId = (int)($i['accepted_unit_id'] ?? 0);
                    $rSel = $this->calculateUnitRate($productId, $accUnitId);

                    $delDisp = round($delivered_base * $rSel, 3);
                    $accDisp = round($accepted_base  * $rSel, 3);
                    $rejDisp = round($rejected_base  * $rSel, 3);

                    receivinginspectingitem::create([
                        'receiving_inspecting_id' => $doc->id,
                        'product_id'              => $productId,
                        'unit_id'                 => $mapUnitId,
                        'accepted_unit_id'        => $accUnitId,
                        'batch_no'                => $i['batch_no']      ?? null,
                        'expiry_date'             => !empty($i['expiry_date']) ? $i['expiry_date'] : null,
                        'request_qty'             => max(0, (float)($i['request_qty'] ?? 0)),
                        'po_qty'                  => $po_base,
                        'delivered_qty'           => round($delivered_base, 6),
                        'accepted_qty'            => round($accepted_base,  6),
                        'rejected_qty'            => round($rejected_base,  6),
                        'delivered_qty_display'   => $delDisp,
                        'accepted_qty_display'    => $accDisp,
                        'rejected_qty_display'    => $rejDisp,
                        'cost_price'              => round($line_total, 2),
                        'rejection_reason'        => $i['rejection_reason'] ?? null,
                    ]);

                    $sumDeliveredByProduct[$productId] = $usedDel + $delivered_base;
                    $sumAcceptedByProduct[$productId]  = $usedAcc + $accepted_base;
                }

                foreach ($request->input('approvals', []) as $a) {
                    if (empty($a['employee_id'])) continue;
                    $respStr = $this->formatResponsibilities($a['responsibilities'] ?? null);
                    receivinginspectingapproval::create([
                        'receiving_inspecting_id' => $doc->id,
                        'employee_id'             => (int)$a['employee_id'],
                        'is_chairman'             => !empty($a['is_chairman']) ? 1 : 0,
                        'responsibilities'        => $respStr,
                        'status'                  => 'pending',
                    ]);
                }
            });
        } catch (QueryException $e) {
            $isDuplicate = (int)($e->errorInfo[1] ?? 0) === 1062 || (int)$e->getCode() === 23000;
            if ($isDuplicate) {
                $msg = 'تعذر الحفظ بسبب تعارض فريد (Unique). الرجاء مراجعة البيانات.';
                $sqlMsg = $e->getMessage();
                if (Str::contains($sqlMsg, $table) && Str::contains($sqlMsg, 'code')) {
                    $msg = trans('supply_chain_trans.code_taken_reload');
                } elseif (Str::contains($sqlMsg, $itemsTable)) {
                    $msg = 'تعارض فريد على أسطر البنود.';
                }
                return back()->withInput()->withErrors(['code' => $msg]);
            }
            throw $e;
        }
 $action = $request->input('requisition_action'); // 'close' أو 'open'
    if (in_array($action, ['close', 'open'], true) && !empty($po->requisition_id)) {
        $req = \App\Models\purchases\purchaserequisition::find($po->requisition_id);
        if ($req) {
            $req->status = ($action === 'close') ? 'closed' : 'receiving_quotes';
            $req->save();
        }
    }
        return redirect()->route('receiving_inspecting.index', [app()->getLocale()])
            ->with('success', trans('supply_chain_trans.updated_success'));
    }
}
