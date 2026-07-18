<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\{
    DepartmentItemRequest,
    productstorebalance,
    StoreMovement,
    productunit
};
use App\Services\SupplyChain\ProductUnitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepartmentRequestProcess extends Component
{
    public $request;
    public $items = [];

    public function mount($id)
    {
        $this->request = DepartmentItemRequest::with(['items.product', 'items.unit'])->findOrFail($id);

        foreach ($this->request->items as $item) {
            $units = productunit::where('product_id', $item->product_id)
                ->with(['largeUnit', 'smallUnit'])->get()
                ->flatMap(fn($u) => [
                    $u->largeUnit ? ['id' => $u->largeUnit->id, 'name_ar' => $u->largeUnit->name_ar] : null,
                    $u->smallUnit ? ['id' => $u->smallUnit->id, 'name_ar' => $u->smallUnit->name_ar] : null,
                ])
                ->filter()->unique('id')->values()->toArray();

            $stores = productstorebalance::where('product_id', $item->product_id)
                ->where('units_id', $item->unit_id)
                ->where('quantity', '>', 0)
                ->with('store')->get()
                ->map(fn($b) => [
                    'id' => $b->store_id,
                    'name_ar' => optional($b->store)->name_ar,
                    'quantity' => $b->quantity,
                ])->toArray();

            $this->items[] = [
                'product_id' => $item->product_id,
                'product_name' => optional($item->product)->name_ar,
                'unit_id' => $item->unit_id,
                'store_id' => $this->request->store_id,
                'quantity_requested' => $item->quantity,
                'quantity_issued' => $item->quantity,
                'related_units' => $units,
                'available_stores' => $stores,
                'current_balance' => collect($stores)->firstWhere('id', $this->request->store_id)['quantity'] ?? 0,
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
                $balance = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->where('units_id', $unitId)->sum('quantity');

                $this->items[$index]['current_balance'] = $balance;
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

                $balances = productstorebalance::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->where('units_id', $unitId)
                    ->where('quantity', '>', 0)
                    ->orderByDesc('quantity_in_base_unit')->get();

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
                        'notes' => 'صرف لصالح قسم',
                        'user_id' => Auth::id(),
                    ]);
                    $balance->decrement('quantity', $deductUnit);
                    $balance->decrement('quantity_in_base_unit', $deductBase);
                    $qtyToIssueBase -= $deductBase;
                }

                $itemModel = $this->request->items->firstWhere('product_id', $productId);
                if ($itemModel) {
                    $itemModel->update(['status' => 'processed']);
                }
            }

            $this->request->update(['status' => 'processed']);
            DB::commit();

            session()->flash('success', 'تم الصرف بنجاح - رقم الحركة: ' . $movementNumber);
            return redirect()->route('receive_withdraw_requests');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء المعالجة: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.department-request-process');
    }
}