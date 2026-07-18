<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\supply_chain\StoreTransfer;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\productdata;
use App\Models\supply_chain\unit;
use App\Models\supply_chain\productstorebalance;
use App\Models\supply_chain\productunit;
use App\Services\SupplyChain\ProductUnitService;

class CreateStoreTransfer extends Component
{
    public $store_from_id, $store_to_id, $transfer_date, $notes;
    public $items = [];
    public $stores, $products;

    protected $rules = [
        'store_from_id' => 'required|different:store_to_id',
        'store_to_id' => 'required',
        'transfer_date' => 'required|date',
        'items.*.product_id' => 'required',
        'items.*.unit_id' => 'required',
        'items.*.quantity' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->transfer_date = date('Y-m-d');
        $this->stores = storemaindata::all();
        $this->products = productdata::all();
        $this->addEmptyItem();
    }

    public function addEmptyItem()
    {
        $this->items[] = [
            'product_id' => '',
            'unit_id' => '',
            'quantity' => '',
            'batch_no' => '',
            'expiry_date' => '',
            'available_quantity' => [],
            'related_units' => [],
        ];
    }

    public function updatedStoreFromId()
    {
        $this->updateItemsAvailableQuantity();
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'items.')) {
            $parts = explode('.', $propertyName);
            $index = $parts[1];
            $field = $parts[2];

            if ($field === 'product_id') {
                $this->updateProductRelatedUnits($index, $this->items[$index]['product_id']);
                $this->updateItemAvailableQuantity($index);
            }

            if ($field === 'unit_id') {
                $this->updateItemAvailableQuantity($index);
            }
        }
    }

    protected function updateItemsAvailableQuantity()
    {
        foreach ($this->items as $index => $item) {
            if ($item['product_id']) {
                $this->updateItemAvailableQuantity($index);
            }
        }
    }

    protected function updateProductRelatedUnits($index, $productId)
    {
        $this->items[$index]['related_units'] = $this->getProductUnits($productId);
        $this->items[$index]['unit_id'] = '';
    }

    protected function updateItemAvailableQuantity($index)
    {
        $productId = $this->items[$index]['product_id'];
        $unitId = $this->items[$index]['unit_id'];
        $quantities = $this->getAvailableQuantities($productId, $unitId);

        $this->items[$index]['available_quantity'] = $quantities;

        if (isset($quantities[0]['quantity'])) {
            $this->items[$index]['current_balance'] = $quantities[0]['quantity'];
        } else {
            $this->items[$index]['current_balance'] = 0;
        }
    }

    public function getAvailableQuantities($productId, $unitId)
    {
        if (!$productId || !$this->store_from_id) return [];

        $balances = productstorebalance::where('product_id', $productId)
            ->where('store_id', $this->store_from_id)
            ->where('quantity_in_base_unit', '>', 0)
            ->orderBy('created_at')
            ->get();

        $service = new ProductUnitService();
        $totalBaseQty = $balances->sum('quantity_in_base_unit');
        $rate = $service->calculateSmallestUnits($productId, $unitId)['total_small_units'] ?? 1;
        $finalQty = $rate > 0 ? $totalBaseQty / $rate : 0;

        return [[
            'unit_id' => $unitId,
            'unit_name' => unit::find($unitId)?->name_ar ?? '',
            'quantity' => number_format($finalQty, 2),
            'in_base_unit' => $totalBaseQty,
        ]];
    }

    public function getProductUnits($productId)
    {
        if (!$productId) return collect();

        $units = productunit::with(['largeUnit', 'smallUnit'])
            ->where('product_id', $productId)->get();

        $related = collect();

        foreach ($units as $u) {
            if ($u->largeUnit) $related->push([
                'id' => $u->largeUnit->id,
                'name_ar' => $u->largeUnit->name_ar,
                'name_en' => $u->largeUnit->name_en ?? '',
            ]);
            if ($u->smallUnit) $related->push([
                'id' => $u->smallUnit->id,
                'name_ar' => $u->smallUnit->name_ar,
                'name_en' => $u->smallUnit->name_en ?? '',
            ]);
        }

        return $related->unique('id')->sortBy('id')->values();
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $service = new ProductUnitService();
            $transfer_number = $this->generateUniqueTransferNumber();

            foreach ($this->items as $item) {
                $result = $service->calculateSmallestUnits($item['product_id'], $item['unit_id']);
                $rate = $result['total_small_units'] ?? 1;
                $base_quantity = $item['quantity'] * $rate;

                $this->validateItemQuantity($item, $base_quantity);
                $this->deductFromBatches($item, $base_quantity, $transfer_number, $rate);
            }

            DB::commit();
            $this->resetForm();
            session()->flash('success', '✅ تم التحويل بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', '❌ ' . $e->getMessage());
        }
    }

    protected function validateItemQuantity($item, $base_quantity)
    {
        $available = productstorebalance::where('product_id', $item['product_id'])
            ->where('store_id', $this->store_from_id)
            ->sum('quantity_in_base_unit');

        if ($base_quantity > $available) {
            throw new \Exception('الرصيد غير كاف في المخزن للمنتج المحدد.');
        }
    }

    protected function deductFromBatches($item, $base_quantity, $transfer_number, $rate)
    {
        $remaining = $base_quantity;

        $batches = productstorebalance::where('product_id', $item['product_id'])
            ->where('store_id', $this->store_from_id)
            ->where('quantity_in_base_unit', '>', 0)
            ->orderBy('created_at')
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $deduct = min($remaining, $batch->quantity_in_base_unit);

            $batch->decrement('quantity_in_base_unit', $deduct);

            productstorebalance::updateOrCreate(
                [
                    'product_id' => $item['product_id'],
                    'store_id' => $this->store_to_id,
                    'units_id' => $item['unit_id'],
                    'batch_no' => $batch->batch_no,
                    'expiry_date' => $batch->expiry_date
                ],
                [
                    'quantity_in_base_unit' =>$deduct
                ]
            );

            StoreTransfer::create([
                'transfer_number' => $this->generateUniqueTransferNumber(),
                'store_from_id' => $this->store_from_id,
                'store_to_id' => $this->store_to_id,
                'transfer_date' => $this->transfer_date,
                'product_id' => $item['product_id'],
                'units_id' => $item['unit_id'],
                'quantity' => $deduct / $rate,
                'quantity_in_base_unit' => $deduct,
                'batch_no' => $batch->batch_no,
                'expiry_date' => $batch->expiry_date,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new \Exception('لم يتم العثور على رصيد كاف في جميع الباتشات.');
        }
    }

    protected function generateUniqueTransferNumber()
    {
        do {
            $number = 'TR-' . strtoupper(Str::random(10));
        } while (StoreTransfer::where('transfer_number', $number)->exists());

        return $number;
    }

    protected function resetForm()
    {
        $this->items = [];
        $this->addEmptyItem();
        $this->notes = '';
    }

    public function addItemRow()
    {
        $this->addEmptyItem();
    }

    public function removeItemRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function render()
    {
        return view('livewire.supply-chain.program.create-store-transfer');
    }
}