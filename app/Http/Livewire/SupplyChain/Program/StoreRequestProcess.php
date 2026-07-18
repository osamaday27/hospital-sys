<?php
// app/Http/Livewire/SupplyChain/Program/StoreRequestProcess.php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\{
    StoreItemRequest,
    productstorebalance,
    StoreMovement,
    productunit
};
use App\Services\SupplyChain\ProductUnitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreRequestProcess extends Component
{
    /**
     * نخزن الـ ID فقط لتفادي Corrupt Data في Livewire
     */
    public int $storeRequestId;

    /**
     * عناصر المعالجة (سكalars/arrays فقط)
     */
    public array $items = [];

    /**
     * بيانات عامة خفيفة للعرض (اختياري)
     */
    public array $requestMeta = [];

    /**
     * mount: تهيئة المكوّن
     */
   public function mount($id)
{
    $this->storeRequestId = (int) $id;

    $req = $this->storeRequest; // computed property
if ($req->status === 'processed') {
    session()->flash('error', 'هذا الطلب تم صرفه بالفعل ولا يمكن معالجته مرة أخرى.');

}


    $this->requestMeta = [
        'request_number' => $req->request_number,
        'status'         => $req->status,
        'store_id'       => $req->store_id,
        'store_to_id'    => $req->store_to_id,
        'store_name'     => optional($req->store)->name_ar,
        'store_to_name'  => optional($req->store_to)->name_ar,
        'request_date'   => (string) $req->request_date,
    ];

    // باقي الكود ...

        foreach ($req->items as $item) {
            // وحدات المنتج (كبير/صغير) بدون تكرار
            $units = productunit::where('product_id', $item->product_id)
                ->with(['largeUnit', 'smallUnit'])->get()
                ->flatMap(fn($u) => [
                    $u->largeUnit ? ['id' => $u->largeUnit->id, 'name_ar' => $u->largeUnit->name_ar] : null,
                    $u->smallUnit ? ['id' => $u->smallUnit->id, 'name_ar' => $u->smallUnit->name_ar] : null,
                ])
                ->filter()->unique('id')->values()->toArray();

            // المخازن المتاح بها رصيد لهذا المنتج/الوحدة
            $stores = productstorebalance::where('product_id', $item->product_id)
                ->where('units_id', $item->unit_id)
                ->where('quantity', '>', 0)
                ->with('store')->get()
                ->map(fn($b) => [
                    'id'       => (int) $b->store_id,
                    'name_ar'  => (string) optional($b->store)->name_ar,
                    'quantity' => (float) $b->quantity,
                ])->toArray();

            $srcId = (int) $req->store_id;
            $dstId = (int) $req->store_to_id;

            $currentBalance = (float) (collect($stores)->firstWhere('id', $srcId)['quantity'] ?? 0.0);

            $this->items[] = [
                'product_id'         => (int) $item->product_id,
                'product_name'       => (string) optional($item->product)->name_ar,
                'unit_id'            => (int) $item->unit_id,
                'source_store_id'    => $srcId, // السحب من مخزن المصدر
                'dest_store_id'      => $dstId, // الإيداع في مخزن الوجهة
                'quantity_requested' => (float) $item->quantity,
                'quantity_issued'    => (float) $item->quantity,
                'related_units'      => $units,
                'available_stores'   => $stores,
                'current_balance'    => $currentBalance,
            ];
        }
    }

    /**
     * Computed property: نحضر الطلب بعلاقاته عند الحاجة فقط
     */
    public function getStoreRequestProperty()
    {
        return StoreItemRequest::with(['items.product', 'items.unit', 'store', 'store_to'])
            ->findOrFail($this->storeRequestId);
    }

    /**
     * تحديثات مباشرة على العناصر
     */
    public function updatedItems($value, $key)
    {
        [$index, $field] = explode('.', $key);

        if (in_array($field, ['unit_id', 'source_store_id'])) {
            $productId = $this->items[$index]['product_id'] ?? null;
            $unitId    = $this->items[$index]['unit_id'] ?? null;
            $storeId   = $this->items[$index]['source_store_id'] ?? null;

            if ($productId && $unitId && $storeId) {
                $balance = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->where('units_id', $unitId)
                    ->sum('quantity');

                $this->items[$index]['current_balance'] = (float) $balance;
            }
        }

        if ($field === 'quantity_issued') {
            $this->items[$index]['quantity_issued'] = (float) $this->items[$index]['quantity_issued'];
        }
    }

    /**
     * تنفيذ الصرف والتحويل
     */
    public function process()
    {
         $req = $this->storeRequest;

    if ($req->status === 'processed') {
        session()->flash('error', 'هذا الطلب تم صرفه مسبقًا.');
        return redirect()->route('receive_withdraw_requests');
    }

    $movementNumber = 'ISS-' . now()->format('YmdHis');

        DB::beginTransaction();
        try {
            $req = $this->storeRequest; // نجيبه لحظة التنفيذ

            // فحص مسبق للأرصدة
            $this->precheckAvailabilityOrFail();

            foreach ($this->items as $row) {
                $productId = (int) $row['product_id'];
                $unitId    = (int) $row['unit_id'];
                $qtyIssue  = (float) $row['quantity_issued'];
                $srcId     = (int) $row['source_store_id'];
                $dstId     = (int) $row['dest_store_id'];

                if ($qtyIssue <= 0) {
                    throw new \Exception('الكمية المصروفة يجب أن تكون أكبر من صفر.');
                }
                if ($srcId === $dstId) {
                    throw new \Exception('لا يمكن التحويل لنفس المخزن.');
                }

                // التحويل لأصغر وحدة
                $service  = new ProductUnitService();
                $rateInfo = $service->calculateSmallestUnits($productId, $unitId);
                $unitRate = (float) ($rateInfo['total_small_units'] ?? 1.0);
                if ($unitRate <= 0) $unitRate = 1.0;

                $remainingBase = $qtyIssue * $unitRate;

                // أرصدة المصدر مرتبة بالأقرب انتهاء أولاً ثم بالأقدم
                $balances = $this->getSourceBalances($productId, $srcId, $unitId);

                foreach ($balances as $balance) {
                    if ($remainingBase <= 0) break;

                    $availableBase = (float) $balance->quantity_in_base_unit;
                    if ($availableBase <= 0) continue;

                    $deductBase = min($remainingBase, $availableBase);
                    $deductUnit = $deductBase / $unitRate;

                    // 1) سحب من المصدر
                    StoreMovement::create([
                        'movement_number' => $movementNumber,
                        'product_id'      => $productId,
                        'store_id'        => $srcId,
                        'units_id'        => $unitId,
                        'quantity'        => $deductUnit,
                        'batch_no'        => $balance->batch_no,
                        'expiry_date'     => $balance->expiry_date,
                        'movement_type'   => 'withdrawal',
                        'movement_date'   => now(),
                        'reference_no'    => $req->request_number,
                        'notes'           => 'صرف تحويل لمخزن آخر',
                        'user_id'         => Auth::id(),
                    ]);

                    // خصم من رصيد المصدر
                    $balance->decrement('quantity', $deductUnit);
                    $balance->decrement('quantity_in_base_unit', $deductBase);

                    // 2) إضافة في الوجهة بنفس الباتش/الصلاحية
                    $destBalance = productstorebalance::firstOrCreate([
                        'product_id'  => $productId,
                        'store_id'    => $dstId,
                        'units_id'    => $unitId,
                        'batch_no'    => $balance->batch_no,
                        'expiry_date' => $balance->expiry_date,
                    ], [
                        'quantity'              => 0,
                        'quantity_in_base_unit' => 0,
                    ]);

                    $destBalance->increment('quantity', $deductUnit);
                    $destBalance->increment('quantity_in_base_unit', $deductBase);

                    // 3) حركة الإضافة في الوجهة
                    StoreMovement::create([
                        'movement_number' => $movementNumber,
                        'product_id'      => $productId,
                        'store_id'        => $dstId,
                        'units_id'        => $unitId,
                        'quantity'        => $deductUnit,
                        'batch_no'        => $balance->batch_no,
                        'expiry_date'     => $balance->expiry_date,
                        'movement_type'   => 'normal_addition',
                        'movement_date'   => now(),
                        'reference_no'    => $req->request_number,
                        'notes'           => 'استلام تحويل من مخزن آخر',
                        'user_id'         => Auth::id(),
                    ]);

                    $remainingBase -= $deductBase;
                }

                if ($remainingBase > 1e-9) {
                    throw new \Exception('تعذّر صرف كامل الكمية المطلوبة لمنتج ID: ' . $productId);
                }

                // تحديث حالة صف الطلب
                $itemModel = $req->items
                    ->firstWhere(fn($i) => (int)$i->product_id === $productId && (int)$i->unit_id === $unitId);

                if ($itemModel) {
                    $itemModel->update(['status' => 'processed']);
                }
            }

            // تحديث حالة الطلب كله
            $req->update(['status' => 'processed']);
            DB::commit();

            session()->flash('success', 'تم الصرف والتحويل بنجاح - رقم الحركة: ' . $movementNumber);
            return redirect()->route('receive_withdraw_requests');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء المعالجة: ' . $e->getMessage());
        }
    }

    /**
     * تمرير المتغير للفيو باسم $request لتفادي "Undefined variable $request"
     */
    public function render()
    {
        return view('livewire.supply-chain.program.store-request-process', [
            'request' => $this->storeRequest, // متاح في الفيو باسم $request
        ]);
    }

    /* ===================== أدوات داخلية ===================== */

    /**
     * فحص مسبق للأرصدة قبل التنفيذ
     */
    protected function precheckAvailabilityOrFail()
    {
        $service = new ProductUnitService();

        foreach ($this->items as $row) {
            $productId = (int) $row['product_id'];
            $unitId    = (int) $row['unit_id'];
            $qtyIssue  = (float) $row['quantity_issued'];
            $srcId     = (int) $row['source_store_id'];

            if ($qtyIssue <= 0) {
                throw new \Exception('الكمية المصروفة يجب أن تكون أكبر من صفر للمنتج ID: ' . $productId);
            }

            $rateInfo = $service->calculateSmallestUnits($productId, $unitId);
            $unitRate = (float) ($rateInfo['total_small_units'] ?? 1.0);
            if ($unitRate <= 0) $unitRate = 1.0;

            $needBase = $qtyIssue * $unitRate;

            $availableBase = (float) productstorebalance::where('product_id', $productId)
                ->where('store_id', $srcId)
                ->where('units_id', $unitId)
                ->sum('quantity_in_base_unit');

            if ($availableBase + 1e-9 < $needBase) {
                throw new \Exception(
                    'الكمية غير كافية في المخزن المصدر للمنتج ID: ' . $productId .
                    ' — المطلوب بالوحدة الأساسية: ' . $needBase .
                    ' / المتاح: ' . $availableBase
                );
            }
        }
    }

    /**
     * أرصدة المصدر مرتبة بالأقرب انتهاء ثم بالأقدم
     */
    protected function getSourceBalances($productId, $storeId, $unitId)
    {
        return productstorebalance::where('product_id', $productId)
            ->where('store_id', $storeId)
            ->where('units_id', $unitId)
            ->where('quantity_in_base_unit', '>', 0)
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }
}