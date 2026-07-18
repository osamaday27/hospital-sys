<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\{
    EmployeeItemRequest,
    EmployeeItemRequestDetail,
    productstorebalance,
    StoreMovement,
    productunit,
    unit,
    storemaindata,
    storekeepermodel
};
use App\Services\SupplyChain\ProductUnitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeRequestProcess extends Component
{
    public $request;
    public $items = [];

    public function mount($id)
    {
        $this->request = EmployeeItemRequest::with(['details.product', 'details.unit', 'employee', 'store'])->findOrFail($id);

        foreach ($this->request->details as $item) {
            $units = productunit::where('product_id', $item->product_id)
                ->with(['largeUnit', 'smallUnit'])
                ->get()
                ->flatMap(function ($u) {
                    return [
                        $u->largeUnit ? ['id' => $u->largeUnit->id, 'name_ar' => $u->largeUnit->name_ar] : null,
                        $u->smallUnit ? ['id' => $u->smallUnit->id, 'name_ar' => $u->smallUnit->name_ar] : null
                    ];
                })->filter()->unique('id')->values()->toArray();

            $userStores = storekeepermodel::where('user_id', Auth::id())
                ->where('status', 'active')
                ->pluck('store_id')
                ->toArray();

            $stores = storemaindata::whereIn('id', $userStores)->get();

            $availableStores = [];
            foreach ($stores as $store) {
                $balance = productstorebalance::where('product_id', $item->product_id)
                    ->where('store_id', $store->id)
                    ->sum('quantity_in_base_unit');

                $rate = (new ProductUnitService)->calculateSmallestUnits($item->product_id, $item->unit_id)['total_small_units'] ?? 1;
                $convertedQty = $rate > 0 ? $balance / $rate : 0;

                $availableStores[] = [
                    'id' => $store->id,
                    'name_ar' => $store->name_ar,
                    'quantity' => round($convertedQty, 2),
                    'quantity_in_base_unit' => $balance,
                ];
            }

            $storeBalance = collect($availableStores)->firstWhere('id', $this->request->store_id);

            $this->items[] = [
                'product_id' => $item->product_id,
                'product_name' => optional($item->product)->name_ar,
                'unit_id' => $item->unit_id,
                'store_id' => $this->request->store_id,
                'quantity_requested' => $item->quantity,
                'quantity_issued' => $item->quantity,
                'related_units' => $units,
                'available_stores' => $availableStores,
                'current_balance' => $storeBalance['quantity'] ?? 0,
                'current_balance_base' => $storeBalance['quantity_in_base_unit'] ?? 0,
            ];
        }
    }

    public function updatedItems($value, $key)
    {
        [$index, $field] = explode('.', $key);
        if (in_array($field, ['unit_id', 'store_id'])) {
            $productId = $this->items[$index]['product_id'];
            $unitId = $this->items[$index]['unit_id'] ?? null;
            $storeId = $this->items[$index]['store_id'] ?? null;

            if ($productId && $unitId && $storeId) {
                $baseQty = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->sum('quantity_in_base_unit');

                $rate = (new ProductUnitService)->calculateSmallestUnits($productId, $unitId)['total_small_units'] ?? 1;
                $convertedQty = $rate > 0 ? $baseQty / $rate : 0;

                $this->items[$index]['current_balance'] = round($convertedQty, 2);
                $this->items[$index]['current_balance_base'] = $baseQty;
            }
        }
    }

    public function process()
    {
        $movementNumber = 'ISS-' . now()->format('YmdHis');

        DB::beginTransaction();

        try {
            foreach ($this->items as $item) {
                $productId = $item['product_id'];
                $unitId = $item['unit_id'];
                $qtyToIssue = $item['quantity_issued'];
                $storeId = $item['store_id'];

                $service = new ProductUnitService();
                $unitRate = $service->calculateSmallestUnits($productId, $unitId)['total_small_units'] ?? 1;
                $qtyToIssueBase = $qtyToIssue * $unitRate;

                $totalAvailable = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->sum('quantity_in_base_unit');

                if ($qtyToIssueBase > $totalAvailable) {
                    throw new \Exception('الصنف (' . $item['product_name'] . ') لا يوجد له رصيد كافي في المخزن المحدد.');
                }

                $balances = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->where('quantity_in_base_unit', '>', 0)
                    ->orderBy('expiry_date')
                    ->get();

                foreach ($balances as $balance) {
                    if ($qtyToIssueBase <= 0) break;

                    $deductBase = min($qtyToIssueBase, $balance->quantity_in_base_unit);
                    $deductUnit = $deductBase / $unitRate;

                    StoreMovement::create([
                        'movement_number' => $movementNumber,
                        'product_id' => $productId,
                        'store_id' => $storeId,
                        'units_id' => $unitId,
                        'quantity' => $deductUnit,
                        'batch_no' => $balance->batch_no,
                        'expiry_date' => $balance->expiry_date,
                        'movement_type' => 'withdrawal',
                        'movement_date' => now(),
                        'reference_no' => $this->request->request_number,
                        'notes' => 'صرف لصالح موظف',
                        'user_id' => Auth::id(),
                    ]);

                //    $balance->decrement('quantity', $deductUnit);
                    $balance->decrement('quantity_in_base_unit', $deductBase);
                    $qtyToIssueBase -= $deductBase;
                }

                EmployeeItemRequestDetail::where('request_id', $this->request->id)
                    ->where('product_id', $productId)
                    ->update(['status' => 'processed']);
            }

            $this->request->update(['status' => 'processed']);
            DB::commit();

            session()->flash('success', 'تم الصرف بنجاح - رقم الحركة: ' . $movementNumber);
            return redirect()->route('receive_withdraw_requests');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.employee-request-process');
    }
}