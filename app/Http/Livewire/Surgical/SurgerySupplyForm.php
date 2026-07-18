<?php
// app/Http/Livewire/surgical/SurgerySupplyForm.php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Traits\store\GetAvailableProductQuantity;

use App\models\administrative\adm_b_surgery_bookings;
use App\models\administrative\adm_b_surgery_supply_issue;
use App\models\administrative\adm_b_surgery_supply_issue_item;

use App\Models\supply_chain\productdata;
use App\Models\supply_chain\productstorebalance;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\unit as unitModel;
use App\Models\supply_chain\StoreMovement;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\storekeepermodel;

use App\Services\SupplyChain\ProductUnitService;

// موديل ربط العملية بالمستلزمات الافتراضية
use App\Models\Medical\SurgeryItem;

class SurgerySupplyForm extends Component
{
    use GetAvailableProductQuantity;

    public $surgery_booking_id;
    public $booking;

    public $store_id = '';
    public $stores = [];              // مخازن المستخدم فقط
    public $user_store_ids = [];      // IDs للتحقق وقت الحفظ

    public $search = '';
    public $available_products = [];  // منتجات فقط
    public $loadingProducts = false;

    // المنتج المختار + وحداته
    public $selected_product = null;  // ['id','name_ar','name_en','system_code']
    public $selected_units = [];      // [['id','name_ar','name_en']]
    public $selected_unit_id = '';
    public $selected_unit_name = '';
    public $selected_unit_balance = 0.0;
    public $selected_unit_balance_formatted = '';

    public $quantity = 1;
    public $notes = '';

    // السلة
    public $cart = []; // [ product_id, system_code, product_name_ar, product_name_en, unit_id, unit_name_ar, quantity, current_balance, current_balance_formatted ]

    // السجل السابق للصرف على نفس العملية
    public $issued_items = []; // عناصر تم صرفها سابقًا

    // =========================
    // شاشة الافتراضي قبل الإضافة للسلة (تجمع لكل العمليات بدون تكرار)
    // =========================
    public $default_items = []; // لكل صف: [checked, product_id, system_code, name_ar, name_en, unit_id, unit_name, quantity, current_balance, current_balance_formatted, units:[]]
    public $default_select_all = false;
    public $auto_add_defaults = false; // لو true يضيف الافتراضي مباشرة للسلة

    protected $rules = [
        'store_id' => 'required|integer',
        'selected_product.id' => 'required|integer',
        'selected_unit_id' => 'required|integer',
        'quantity' => 'required|numeric|min:0.0001',
    ];

    public function mount($surgery_booking_id)
    {
        $this->surgery_booking_id = $surgery_booking_id;
        // نحمل الحجز ومعاه العمليات المرتبطة
        $this->booking = adm_b_surgery_bookings::with(['services:id'])->findOrFail($surgery_booking_id);

        $this->loadUserStores();          // مخازن المستخدم فقط
        $this->refreshAvailableProducts();
        $this->refreshIssuedItems();      // السجل السابق

        if ($this->store_id) {
            $this->preloadDefaultSupplies(); // تحميل الافتراضي لكل العمليات
        }
    }

    /*==========================
      مخازن المستخدم
    ==========================*/
    protected function loadUserStores(): void
    {
        $uid = Auth::id();

        $this->user_store_ids = storekeepermodel::query()
            ->where('user_id', $uid)
            ->where('status', 1)
            ->where('can_issue', 1)
            ->pluck('store_id')
            ->map(fn($v) => (int)$v)
            ->values()
            ->toArray();

        $this->stores = storemaindata::query()
            ->whereIn('id', $this->user_store_ids ?: [-1])
            ->select('id', 'name_ar', 'name_en')
            ->orderBy('name_ar')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name_ar' => $s->name_ar,
                'name_en' => $s->name_en,
            ])
            ->toArray();

        if ($this->store_id && !in_array((int)$this->store_id, $this->user_store_ids, true)) {
            $this->store_id = '';
        }
    }

    public function updatedStoreId()
    {
        try {
            if ($this->store_id && !in_array((int)$this->store_id, $this->user_store_ids, true)) {
                $bad = $this->store_id;
                $this->store_id = '';
                session()->flash('error', __('surgical_trans.not_authorized_store'));
                Log::warning('Unauthorized store selection', ['store_id_attempt' => $bad, 'user_id' => Auth::id()]);
                return;
            }

            $this->resetSelection();
            $this->refreshAvailableProducts();

            $this->preloadDefaultSupplies(); // إعادة تحميل الافتراضي بعد اختيار المخزن
        } catch (\Throwable $e) {
            Log::error('Error in updatedStoreId()', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'store_id'=> $this->store_id,
                'booking' => $this->surgery_booking_id,
                'user_id' => Auth::id(),
            ]);
            session()->flash('error', 'حدث خطأ أثناء تحميل بيانات المخزن. راجع اللوج لمعرفة التفاصيل.');
            if (config('app.debug')) {
                throw $e;
            }
        }
    }

    public function updatedSearch()
    {
        $this->refreshAvailableProducts();
    }

    protected function resetSelection()
    {
        $this->selected_product = null;
        $this->selected_units = [];
        $this->selected_unit_id = '';
        $this->selected_unit_name = '';
        $this->selected_unit_balance = 0.0;
        $this->selected_unit_balance_formatted = '';
        $this->quantity = 1;
    }

    /*==========================
      المنتجات المتاحة في المخزن
    ==========================*/
    protected function refreshAvailableProducts()
    {
        if (empty($this->store_id)) {
            $this->available_products = [];
            return;
        }

        $this->loadingProducts = true;

        $q = productstorebalance::query()
            ->where('store_id', $this->store_id)
            ->where(function ($qq) {
                $qq->where('quantity', '>', 0)->orWhere('quantity_in_base_unit', '>', 0);
            })
            ->with('product')
            ->select(['product_id', 'store_id'])
            ->groupBy('product_id', 'store_id');

        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $q->whereHas('product', function ($qq) use ($term) {
                $qq->where('name_ar', 'like', $term)
                   ->orWhere('name_en', 'like', $term)
                   ->orWhere('system_code', 'like', $term);
            });
        }

        $rows = $q->get()
            ->map(fn($r) => [
                'id' => $r->product_id,
                'name_ar' => optional($r->product)->name_ar,
                'name_en' => optional($r->product)->name_en,
                'system_code' => optional($r->product)->system_code,
            ])
            ->values()
            ->toArray();

        $this->available_products = collect($rows)->unique('id')->values()->toArray();
        $this->loadingProducts = false;
    }

    public function selectProduct($product_id)
    {
        if (!$this->store_id) {
            session()->flash('error', trans('surgical_trans.select_store_first'));
            return;
        }

        $product = productdata::find($product_id);
        if (!$product) return;

        $this->selected_product = [
            'id' => $product->id,
            'name_ar' => $product->name_ar,
            'name_en' => $product->name_en,
            'system_code' => $product->system_code,
        ];

        // الوحدات المعرّفة + الموجودة فعليًا بالأرصدة
        $unitRows = productunit::where('product_id', $product->id)
            ->with(['largeUnit:id,name_ar,name_en', 'smallUnit:id,name_ar,name_en'])
            ->get()
            ->flatMap(function ($u) {
                return [
                    $u->largeUnit ? [
                        'id' => $u->largeUnit->id,
                        'name_ar' => $u->largeUnit->name_ar,
                        'name_en' => $u->largeUnit->name_en,
                    ] : null,
                    $u->smallUnit ? [
                        'id' => $u->smallUnit->id,
                        'name_ar' => $u->smallUnit->name_ar,
                        'name_en' => $u->smallUnit->name_en,
                    ] : null,
                ];
            })
            ->filter()
            ->unique('id')
            ->values()
            ->toArray();

        $balanceUnits = productstorebalance::where('product_id', $product->id)
            ->where('store_id', $this->store_id)
            ->where(function ($q) {
                $q->where('quantity', '>', 0)->orWhere('quantity_in_base_unit', '>', 0);
            })
            ->with('unit:id,name_ar,name_en')
            ->get()
            ->map(fn($b) => [
                'id' => $b->units_id,
                'name_ar' => optional($b->unit)->name_ar,
                'name_en' => optional($b->unit)->name_en,
            ])
            ->unique('id')
            ->values()
            ->toArray();

        $this->selected_units = collect($unitRows)->merge($balanceUnits)->unique('id')->values()->toArray();

        $this->selected_unit_id = '';
        $this->selected_unit_name = '';
        $this->selected_unit_balance = 0.0;
        $this->selected_unit_balance_formatted = '';
        $this->quantity = 1;
    }

    public function updatedSelectedUnitId()
    {
        $meta = collect($this->selected_units)->firstWhere('id', (int) $this->selected_unit_id);
        $this->selected_unit_name = $meta ? ($meta['name_ar'] ?? $meta['name_en'] ?? '') : '';
        $this->updateUnitBalance();
    }

    /*==========================
      الرصيد للوحدة المختارة
    ==========================*/
    protected function updateUnitBalance()
    {
        $this->selected_unit_balance = 0.0;
        $this->selected_unit_balance_formatted = '';

        if (!$this->store_id || !$this->selected_product || !$this->selected_unit_id) {
            return;
        }

        $productId = (int)$this->selected_product['id'];
        $unitId    = (int)$this->selected_unit_id;

        $total_in_base = $this->totalInBaseUnits($productId);

        $service = new ProductUnitService();
        $calc    = $service->calculateSmallestUnits($productId, $unitId);
        $rate_to_smallest = (float)($calc['total_small_units'] ?? 1);
        if ($rate_to_smallest <= 0) { $rate_to_smallest = 1.0; }

        $qty_in_unit = $rate_to_smallest > 0 ? $total_in_base / $rate_to_smallest : 0.0;

        $this->selected_unit_balance = round($qty_in_unit, 4);
        $this->selected_unit_balance_formatted = $this->formatQuantityBreakdown($productId, $total_in_base);
    }

    private function formatFallbackBreakdown(float $total_in_base): string
    {
        if (!$this->selected_product) return '';
        return $this->formatQuantityBreakdown((int)$this->selected_product['id'], $total_in_base);
    }

    /*==========================
      السلة
    ==========================*/
    public function addToCart()
    {
        $this->validate();

        $this->updateUnitBalance();

        if ($this->quantity > $this->selected_unit_balance) {
            session()->flash('error', trans('surgical_trans.quantity_exceeds_balance'));
            return;
        }

        // دمج صف موجود لنفس المنتج/الوحدة
        foreach ($this->cart as $i => $row) {
            if ((int)$row['product_id'] === (int)$this->selected_product['id'] &&
                (int)$row['unit_id'] === (int)$this->selected_unit_id) {

                [$balanceQty, $formatted] = $this->currentBalanceFor($row['product_id'], $row['unit_id']);
                $newQty = (float)$row['quantity'] + (float)$this->quantity;

                if ($newQty > $balanceQty) {
                    session()->flash('error', trans('surgical_trans.quantity_exceeds_balance'));
                    return;
                }

                $this->cart[$i]['quantity'] = $newQty;
                $this->cart[$i]['current_balance'] = $balanceQty;
                $this->cart[$i]['current_balance_formatted'] = $formatted;
                $this->resetSelection();
                return;
            }
        }

        // إضافة صف جديد
        $unitMeta = collect($this->selected_units)->firstWhere('id', (int)$this->selected_unit_id) ?? [];
        [$balanceQty, $formatted] = $this->currentBalanceFor((int)$this->selected_product['id'], (int)$this->selected_unit_id);

        $this->cart[] = [
            'product_id' => (int)$this->selected_product['id'],
            'product_name_ar' => $this->selected_product['name_ar'] ?? '',
            'product_name_en' => $this->selected_product['name_en'] ?? '',
            'system_code' => $this->selected_product['system_code'] ?? '',
            'unit_id' => (int)$this->selected_unit_id,
            'unit_name_ar' => $unitMeta['name_ar'] ?? ($unitMeta['name_en'] ?? ''),
            'quantity' => (float)$this->quantity,
            'current_balance' => $balanceQty,
            'current_balance_formatted' => $formatted,
        ];

        $this->resetSelection();
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    private function currentBalanceFor(int $productId, int $unitId): array
    {
        $total_in_base = $this->totalInBaseUnits($productId);

        $service = new ProductUnitService();
        $calcReq = $service->calculateSmallestUnits($productId, $unitId);
        $rate_to_smallest = (float)($calcReq['total_small_units'] ?? 1);
        if ($rate_to_smallest <= 0) { $rate_to_smallest = 1.0; }

        $qty_in_unit = $rate_to_smallest > 0 ? $total_in_base / $rate_to_smallest : 0.0;

        return [
            round($qty_in_unit, 4),
            $this->formatQuantityBreakdown($productId, $total_in_base)
        ];
    }

    /**
     * تحميل سجلات الصرف السابقة على نفس العملية.
     */
    protected function refreshIssuedItems(): void
    {
        $issueIds = adm_b_surgery_supply_issue::query()
            ->where('surgery_booking_id', $this->surgery_booking_id)
            ->orderBy('id', 'desc')
            ->pluck('id');

        if ($issueIds->isEmpty()) {
            $this->issued_items = [];
            return;
        }

        $items = adm_b_surgery_supply_issue_item::query()
            ->whereIn('issue_id', $issueIds)
            ->orderBy('id', 'desc')
            ->get();

        $issuesById = adm_b_surgery_supply_issue::whereIn('id', $issueIds)->get()->keyBy('id');
        $productIds = $items->pluck('product_id')->unique()->values();
        $unitIds    = $items->pluck('unit_id')->unique()->values();

        $products = productdata::whereIn('id', $productIds)->get()->keyBy('id');
        $units    = unitModel::whereIn('id', $unitIds)->get()->keyBy('id');

        $this->issued_items = $items->map(function ($it) use ($issuesById, $products, $units) {
            $issue = $issuesById->get($it->issue_id);
            $prod  = $products->get($it->product_id);
            $unit  = $units->get($it->unit_id);

            return [
                'movement_number' => $issue->movement_number ?? '',
                'issue_date'      => optional($issue->created_at)?->format('Y-m-d H:i') ?? '',
                'product_code'    => $prod->system_code ?? ($prod->code ?? ''),
                'product_name_ar' => $prod->name_ar ?? '',
                'product_name_en' => $prod->name_en ?? '',
                'unit_name'       => $unit->name_ar ?? ($unit->name_en ?? ''),
                'qty_unit'        => rtrim(rtrim(number_format((float)($it->quantity_issued ?? 0), 4, '.', ''), '0'), '.'),
                'qty_base'        => rtrim(rtrim(number_format((float)($it->quantity_in_base_unit ?? 0), 4, '.', ''), '0'), '.'),
                'batch_no'        => $it->batch_no ?? '',
                'expiry_date'     => $it->expiry_date ?? '',
            ];
        })->values()->toArray();
    }

    /*==========================
      إجمالي الصغرى بدقة
    ==========================*/
    private function totalInBaseUnits(int $productId): float
    {
        $balances = productstorebalance::where('product_id', $productId)
            ->where('store_id', $this->store_id)
            ->get(['units_id', 'quantity', 'quantity_in_base_unit']);

        if ($balances->isEmpty()) {
            return 0.0;
        }

        $service = new ProductUnitService();
        $total_in_base = 0.0;

        foreach ($balances as $b) {
            $calcRow = $service->calculateSmallestUnits($productId, (int)$b->units_id);
            $rowRate = (float)($calcRow['total_small_units'] ?? 1);
            if ($rowRate <= 0) { $rowRate = 1.0; }

            $q_base = (!is_null($b->quantity_in_base_unit) && (float)$b->quantity_in_base_unit > 0)
                ? (float)$b->quantity_in_base_unit
                : ((float)$b->quantity * $rowRate);

            if ($q_base > 0) {
                $total_in_base += $q_base;
            }
        }

        return round($total_in_base, 6);
    }

    /*==========================
      الافتراضي لكل العمليات المرتبطة بالحجز (بدون تكرار)
    ==========================*/
    protected function preloadDefaultSupplies(): void
    {
        try {
            if (!$this->store_id) return;

            $this->default_items = []; // نبدأ من جديد

            // 1) اجمع كل service_ids المرتبطة بالحجز (pivot + fallback للسطر القديم)
            $serviceIds = $this->getBookingServiceIds();
            if ($serviceIds->isEmpty()) return;

            // 2) هات المستلزمات الافتراضية لكل العمليات مرة واحدة
            $defaults = SurgeryItem::whereIn('surgery_id', $serviceIds->all())->get();
            if ($defaults->isEmpty()) return;

            // 3) دمج بنود الافتراضي لنفس المنتج (تجميع كميات) لمنع التكرار
            //    المفتاح = product_id
            $merged = [];
            foreach ($defaults as $def) {
                $pid = (int)($def->item_id ?? 0);
                if ($pid <= 0) continue;

                $qty = (float)($def->quantity ?? 0);
                if ($qty <= 0) $qty = 1.0;

                // خزن آخر وحدة مذكورة أو اتركها null ليتحدد لاحقًا بأصغر وحدة
                $unitId = null;
                if (isset($def->unit_id) && $def->unit_id) {
                    $unitId = (int)$def->unit_id;
                } elseif (!empty($def->unit)) {
                    $name = trim($def->unit);
                    $uu = unitModel::where('name_ar', $name)->orWhere('name_en', $name)->first();
                    if ($uu) $unitId = (int)$uu->id;
                }

                if (!isset($merged[$pid])) {
                    $merged[$pid] = [
                        'product_id' => $pid,
                        'quantity'   => $qty,
                        'unit_id'    => $unitId, // قد تتحدث لاحقًا
                    ];
                } else {
                    $merged[$pid]['quantity'] += $qty;
                    // لو الوحدة لسه null وخدنا واحدة من بند آخر
                    if (is_null($merged[$pid]['unit_id']) && $unitId) {
                        $merged[$pid]['unit_id'] = $unitId;
                    }
                }
            }

            // 4) حوّل الميرج إلى صفوف افتراضية قابلة للعرض/التعديل
            foreach ($merged as $m) {
                $product = productdata::find($m['product_id']);
                if (!$product) continue;

                // الوحدة الافتراضية: لو ما تحددتش من البنود، خذ أصغر وحدة أو وحدة من الرصيد
                $unitId = $m['unit_id'] ?: $this->getSmallestUnitId($product->id);
                if (!$unitId) continue;

                // جميع وحدات المنتج للـ dropdown
                $unitsList = $this->buildUnitsList($product->id);

                // الرصيد الحالي بوحدة الاختيار
                [$balanceQty, $formatted] = $this->currentBalanceFor((int)$product->id, (int)$unitId);

                $unit = unitModel::find($unitId);
                $unitName = $unit?->name_ar ?? $unit?->name_en ?? '';

                $row = [
                    'checked' => true,
                    'product_id' => (int)$product->id,
                    'system_code' => $product->system_code ?? '',
                    'name_ar' => $product->name_ar ?? '',
                    'name_en' => $product->name_en ?? '',
                    'unit_id' => (int)$unitId,
                    'unit_name' => $unitName,
                    'quantity' => (float)$m['quantity'],
                    'current_balance' => $balanceQty,
                    'current_balance_formatted' => $formatted,
                    'units' => $unitsList,
                ];

                if ($this->auto_add_defaults) {
                    // سلوك قديم: أضف مباشرة للسلة
                    $this->cart[] = [
                        'product_id' => $row['product_id'],
                        'product_name_ar' => $row['name_ar'],
                        'product_name_en' => $row['name_en'],
                        'system_code' => $row['system_code'],
                        'unit_id' => $row['unit_id'],
                        'unit_name_ar' => $row['unit_name'],
                        'quantity' => $row['quantity'],
                        'current_balance' => $row['current_balance'],
                        'current_balance_formatted' => $row['current_balance_formatted'],
                    ];
                } else {
                    $this->default_items[] = $row;
                }
            }

            if (!$this->auto_add_defaults) {
                // البنود التي كميتها أكبر من الرصيد تظهر أولاً (للتنبيه)
                $this->default_items = collect($this->default_items)
                    ->sortBy(fn($r) => (float)$r['quantity'] > (float)$r['current_balance'] ? 0 : 1)
                    ->values()
                    ->toArray();
            }

        } catch (\Throwable $e) {
            Log::error('Error in preloadDefaultSupplies()', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'store_id'=> $this->store_id,
                'booking' => $this->surgery_booking_id,
                'user_id' => Auth::id(),
            ]);
            session()->flash('error', 'تعذر تحميل المستلزمات الافتراضية. راجع اللوج لمعرفة التفاصيل.');
            if (config('app.debug')) {
                throw $e;
            }
        }
    }

    // جميع الوحدات للمنتج
    protected function buildUnitsList(int $productId): array
    {
        $rows = productunit::where('product_id', $productId)
            ->with(['largeUnit:id,name_ar,name_en', 'smallUnit:id,name_ar,name_en'])
            ->get();

        $units = collect();

        foreach ($rows as $r) {
            if ($r->largeUnit) {
                $units->push([
                    'id' => $r->largeUnit->id,
                    'name' => $r->largeUnit->name_ar ?? $r->largeUnit->name_en,
                ]);
            }
            if ($r->smallUnit) {
                $units->push([
                    'id' => $r->smallUnit->id,
                    'name' => $r->smallUnit->name_ar ?? $r->smallUnit->name_en,
                ]);
            }
        }

        // أضف أي وحدة من الرصيد لو مش موجودة
        $balanceUnits = productstorebalance::where('product_id', $productId)
            ->where('store_id', $this->store_id)
            ->with('unit:id,name_ar,name_en')
            ->get()
            ->map(fn($b) => [
                'id' => $b->units_id,
                'name' => optional($b->unit)->name_ar ?? optional($b->unit)->name_en,
            ]);

        return $units->merge($balanceUnits)->unique('id')->values()->toArray();
    }

    // تبديل تحديد الكل
    public function toggleDefaultSelectAll()
    {
        $this->default_select_all = !$this->default_select_all;
        foreach ($this->default_items as $i => $row) {
            $this->default_items[$i]['checked'] = $this->default_select_all;
        }
    }

    // تغيير وحدة بند افتراضي
    public function changeDefaultUnit($index, $unitId)
    {
        if (!isset($this->default_items[$index])) return;

        $row = $this->default_items[$index];
        $row['unit_id'] = (int)$unitId;

        $u = unitModel::find((int)$unitId);
        $row['unit_name'] = $u?->name_ar ?? $u?->name_en ?? '';

        [$bal, $fmt] = $this->currentBalanceFor((int)$row['product_id'], (int)$unitId);
        $row['current_balance'] = $bal;
        $row['current_balance_formatted'] = $fmt;

        $this->default_items[$index] = $row;
    }

    // تغيير كمية بند افتراضي
    public function changeDefaultQty($index, $qty)
    {
        if (!isset($this->default_items[$index])) return;
        $qty = (float)$qty;
        if ($qty <= 0) $qty = 1.0;
        $this->default_items[$index]['quantity'] = $qty;
    }

    // إضافة بند افتراضي محدد إلى السلة
    public function addDefaultRowToCart($index)
    {
        if (!isset($this->default_items[$index])) return;
        $row = $this->default_items[$index];

        foreach ($this->cart as $i => $c) {
            if ((int)$c['product_id'] === (int)$row['product_id'] && (int)$c['unit_id'] === (int)$row['unit_id']) {
                $newQty = (float)$c['quantity'] + (float)$row['quantity'];
                if ($newQty > (float)$row['current_balance']) {
                    // هيبان أحمر في السلة لو UI عامل لون/تحذير
                }
                $this->cart[$i]['quantity'] = $newQty;
                [$bal, $fmt] = $this->currentBalanceFor((int)$row['product_id'], (int)$row['unit_id']);
                $this->cart[$i]['current_balance'] = $bal;
                $this->cart[$i]['current_balance_formatted'] = $fmt;
                return;
            }
        }

        $this->cart[] = [
            'product_id' => (int)$row['product_id'],
            'product_name_ar' => $row['name_ar'],
            'product_name_en' => $row['name_en'],
            'system_code' => $row['system_code'],
            'unit_id' => (int)$row['unit_id'],
            'unit_name_ar' => $row['unit_name'],
            'quantity' => (float)$row['quantity'],
            'current_balance' => (float)$row['current_balance'],
            'current_balance_formatted' => $row['current_balance_formatted'],
        ];
    }

    // إضافة كل الافتراضي المحدد للسلة
    public function addAllDefaultsToCart()
    {
        foreach ($this->default_items as $i => $row) {
            if (!($row['checked'] ?? false)) continue;
            $this->addDefaultRowToCart($i);
        }
        session()->flash('success', __('تمت إضافة الافتراضي المحدد إلى السلة.'));
    }

    /*==========================
      خدمة: IDs العمليات المرتبطة بالحجز
    ==========================*/
    protected function getBookingServiceIds()
    {
        $ids = collect();

        // من العلاقة pivot: services()
        if ($this->booking && $this->booking->relationLoaded('services')) {
            $ids = $ids->merge($this->booking->services->pluck('id')->filter());
        }

        // fallback للعمود القديم surgical_id
        if ($this->booking && $this->booking->surgical_id) {
            $ids = $ids->push((int)$this->booking->surgical_id);
        }

        return $ids->unique()->values();
    }

    // أصغر وحدة للمنتج أو وحدة من الرصيد
    protected function getSmallestUnitId(int $productId): ?int
    {
        $row = productunit::where('product_id', $productId)->with('smallUnit:id')->first();
        if ($row && $row->smallUnit) return (int)$row->smallUnit->id;

        $balance = productstorebalance::where('product_id', $productId)
            ->where('store_id', $this->store_id)
            ->where(function ($q) { $q->where('quantity', '>', 0)->orWhere('quantity_in_base_unit', '>', 0); })
            ->orderBy('id', 'asc')->first();

        return $balance ? (int)$balance->units_id : null;
    }

    /*==========================
      حفظ الصرف (FEFO) بنفس منطقك
    ==========================*/
    public function save()
    {
        if (!$this->store_id || empty($this->cart)) {
            session()->flash('error', trans('surgical_trans.select_store_first'));
            return;
        }

        if (!in_array((int)$this->store_id, $this->user_store_ids, true)) {
            session()->flash('error', __('surgical_trans.not_authorized_store'));
            return;
        }

        foreach ($this->cart as $row) {
            [$balanceQty] = $this->currentBalanceFor((int)$row['product_id'], (int)$row['unit_id']);
            if ((float)$row['quantity'] > $balanceQty) {
                session()->flash('error', trans('surgical_trans.quantity_exceeds_balance'));
                return;
            }
        }

        $movementNumber = 'SRG-ISS-' . now()->format('YmdHis');

        DB::beginTransaction();
        try {
            $issue = adm_b_surgery_supply_issue::create([
                'surgery_booking_id' => $this->surgery_booking_id,
                'visit_id' => $this->booking->visit_id ?? null,
                'patient_id' => $this->booking->patient_id ?? null,
                'store_id' => $this->store_id,
                'movement_number' => $movementNumber,
                'status' => 'processed',
                'user_id' => Auth::id(),
                'notes' => $this->notes,
            ]);

            $service = new ProductUnitService();

            foreach ($this->cart as $row) {
                $productId       = (int)$row['product_id'];
                $requestedUnitId = (int)$row['unit_id'];
                $qtyRequested    = (float)$row['quantity'];

                $calcReq = $service->calculateSmallestUnits($productId, $requestedUnitId);
                $reqRate = $calcReq['total_small_units'] ?? 1;
                $needBase = $qtyRequested * $reqRate;

                $balances = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $this->store_id)
                    ->where(function ($q) {
                        $q->where('quantity_in_base_unit', '>', 0)->orWhere('quantity', '>', 0);
                    })
                    ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->get();

                $remainBase = $needBase;

                foreach ($balances as $bal) {
                    if ($remainBase <= 0) break;

                    $calcRow = $service->calculateSmallestUnits($productId, (int)$bal->units_id);
                    $rowRate = $calcRow['total_small_units'] ?? 1;

                    if (is_null($bal->quantity_in_base_unit)) {
                        $bal->quantity_in_base_unit = (float)$bal->quantity * $rowRate;
                        $bal->save();
                        $bal->refresh();
                    }

                    $availableBase = max(0.0, (float)$bal->quantity_in_base_unit);
                    if ($availableBase <= 0) continue;

                    $deductBase = min($remainBase, $availableBase);
                    $deductUnitForRow = $deductBase / $rowRate;

                    StoreMovement::create([
                        'movement_number' => $movementNumber,
                        'product_id' => $productId,
                        'store_id' => $this->store_id,
                        'units_id' => (int)$bal->units_id,
                        'quantity' => $deductUnitForRow,
                        'batch_no' => $bal->batch_no,
                        'expiry_date' => $bal->expiry_date,
                        'movement_type' => 'withdrawal',
                        'movement_date' => now(),
                        'reference_no' => $this->surgery_booking_id,
                        'notes' => 'صرف مستلزمات (FEFO) لعملية جراحية',
                        'user_id' => Auth::id(),
                    ]);

                    $bal->decrement('quantity', $deductUnitForRow);
                    $bal->decrement('quantity_in_base_unit', $deductBase);

                    $deductInRequestedUnit = $deductBase / $reqRate;
                    adm_b_surgery_supply_issue_item::create([
                        'issue_id' => $issue->id,
                        'product_id' => $productId,
                        'unit_id' => $requestedUnitId,
                        'quantity_requested' => $qtyRequested,
                        'quantity_issued' => $deductInRequestedUnit,
                        'quantity_in_base_unit' => $deductBase,
                        'batch_no' => $bal->batch_no,
                        'expiry_date' => $bal->expiry_date,
                    ]);

                    $remainBase -= $deductBase;
                }

                if ($remainBase > 0.0001) {
                    throw new \RuntimeException('Insufficient balance after FEFO allocation.');
                }
            }

            DB::commit();

            session()->flash('success', trans('surgical_trans.issued_successfully') . ' - ' . $movementNumber);
            $this->cart = [];
            $this->resetSelection();
            $this->refreshAvailableProducts();
            $this->refreshIssuedItems();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in save() of SurgerySupplyForm', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'store_id'=> $this->store_id,
                'booking' => $this->surgery_booking_id,
                'user_id' => Auth::id(),
            ]);
            session()->flash('error', trans('surgical_trans.error_happened') . ': ' . $e->getMessage());
            if (config('app.debug')) {
                throw $e;
            }
        }
    }

    public function render()
    {
        return view('livewire.surgical.surgery-supply-form');
    }
}