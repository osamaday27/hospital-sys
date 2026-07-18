<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\models\suppliers\suppliersdata;
use App\models\supply_chain\storemaindata;
use App\models\supply_chain\productdata;
use App\models\supply_chain\unit;
use App\models\supply_chain\productunit;
use App\models\supply_chain\productstorebalance;
use App\models\supply_chain\StoreMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\SupplyChain\ProductUnitService;

class CreateOpeningBalance extends Component
{
    
    public $supplier_id, $store_id, $movement_date, $notes;
    public $items = [];
    public $rowErrors = [];

    public function mount()
    {
        $this->movement_date = date('Y-m-d');
        $this->items[] = $this->emptyItem();
    }

    public function updatedStoreId()
    {
        foreach ($this->items as $index => $item) {
            if (!empty($item['product_id']) && !empty($item['unit_id'])) {
                $this->updateCurrentBalanceForItem($index);
            }
        }
    }

    public function emptyItem()
    {
        return [
            'product_id' => '',
            'unit_id' => '',
            'quantity' => '',
            'expiry_date' => '',
            'batch_no' => '',
            'cost_price' => '',
            'sale_price' => '',
            'code' => '',
            'name' => '',
            'current_balance' => '',
            'related_units' => [],
        ];
    }

    public function updatedItems($value, $name)
    {
        [$index, $field] = explode('.', $name);

        if ($field === 'product_id' && $value) {
            $product = productdata::find($value);
            $this->items[$index]['name'] = $product->name_ar;
            $this->items[$index]['code'] = $product->product_code;

            $units = productunit::where('product_id', $value)->get();
            $this->items[$index]['related_units'] = [];

            foreach ($units as $unit) {
                if ($unit->unit_large_id) {
                    $this->items[$index]['related_units'][] = unit::find($unit->unit_large_id);
                }
                if ($unit->unit_small_id) {
                    $this->items[$index]['related_units'][] = unit::find($unit->unit_small_id);
                }
            }

            $this->items[$index]['related_units'] = collect($this->items[$index]['related_units'])->unique('id')->values()->all();
            $this->updateCurrentBalanceForItem($index);
        }

        if ($field === 'unit_id') {
            $this->updateCurrentBalanceForItem($index);
        }
    }

    private function updateCurrentBalanceForItem($index)
    {
        if (isset($this->items[$index]['product_id'], $this->items[$index]['unit_id'], $this->store_id)) {
            $productId = $this->items[$index]['product_id'];
            $unitId = $this->items[$index]['unit_id'];

            $balances = productstorebalance::where('product_id', $productId)
                ->where('store_id', $this->store_id)
                ->get();

            $total_in_base = $balances->sum('quantity_in_base_unit');

            $service = new ProductUnitService();
            $result = $service->calculateSmallestUnits($productId, $unitId);
            $rate = $result['total_small_units'] ?? 1;

            $this->items[$index]['current_balance'] = $rate ? round($total_in_base / $rate, 2) : 0;

            $unitInfo = productunit::where('product_id', $productId)
                ->where(function ($q) use ($unitId) {
                    $q->where('unit_large_id', $unitId)->orWhere('unit_small_id', $unitId);
                })->first();

            $this->items[$index]['cost_price'] = $unitInfo->cost_price ?? 0;
            $this->items[$index]['sale_price'] = $unitInfo->sale_price ?? 0;
        }
    }

    public function addItemRow()
    {
        $this->items[] = $this->emptyItem();
    }

    public function removeItemRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers_data,id',
            'store_id' => 'required|exists:store_main_date,id',
            'movement_date' => 'required|date',
            'items.*.product_id' => 'required|exists:products_data,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $this->rowErrors = [];

        foreach ($this->items as $index => $item) {
            $exists = StoreMovement::where('store_id', $this->store_id)
                ->where('product_id', $item['product_id'])
                ->where('units_id', $item['unit_id'])
                ->where('movement_type', 'opening_balance')
                ->exists();

            if ($exists) {
                $productName = productdata::find($item['product_id'])->name_ar ?? 'الصنف';
                $this->rowErrors[$index] = "الصنف ($productName) موجود بالفعل كرصيد افتتاحي في هذا المخزن.";
            }
        }

        if (!empty($this->rowErrors)) {
            session()->flash('error', 'بعض الأصناف لديها رصيد افتتاحي مسبق ولا يمكن إضافتها مرة أخرى.');
            return;
        }

        DB::beginTransaction();
        $lastMovement = StoreMovement::latest('id')->first();
        $lastId = $lastMovement ? $lastMovement->id + 1 : 1;
        $movementNumber = 'OPN-' . str_pad($lastId, 6, '0', STR_PAD_LEFT);

        $service = new ProductUnitService();

        foreach ($this->items as $item) {
            $result = $service->calculateSmallestUnits($item['product_id'], $item['unit_id']);
            $rate = $result['total_small_units'] ?? 1;
            $quantity_in_base = $item['quantity'] * $rate;

            StoreMovement::create([
                'movement_number' => $movementNumber,
                'product_id' => $item['product_id'],
                'supplier_id' => $this->supplier_id,
                'store_id' => $this->store_id,
                'units_id' => $item['unit_id'],
                'quantity' => $item['quantity'],
                'batch_no' => $item['batch_no'],
                'cost_price' => $item['cost_price'],
                'sale_price' => $item['sale_price'],
                'expiry_date' => $item['expiry_date'],
                'movement_type' => 'opening_balance',
                'reference_no' => null,
                'movement_date' => $this->movement_date,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            productstorebalance::updateOrCreate(
                [
                    'product_id' => $item['product_id'],
                    'store_id' => $this->store_id,
                    'batch_no' => $item['batch_no'],
                    'units_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'expiry_date' => $item['expiry_date'],
                ],
                [
                    'quantity_in_base_unit' => $quantity_in_base,
                ]
            );
        }

        DB::commit();

        session()->flash('success', 'تم حفظ الأرصدة الافتتاحية بنجاح - رقم الحركة: ' . $movementNumber);
        $this->reset(['items']);
        $this->items[] = $this->emptyItem();
    }

    public function render()
    {
        return view('livewire.supply-chain.program.create-opening-balance', [
            'suppliers' => suppliersdata::all(),
            'stores' => storemaindata::all(),
            'products' => productdata::all(),
            'units' => unit::all(),
            'rowErrors' => $this->rowErrors,
        ]);
    }
}