<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\suppliers\suppliersdata;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\productdata;
use App\Models\supply_chain\unit;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\productstorebalance;
use App\Models\supply_chain\StoreMovement;
use App\Models\supply_chain\storeproductmodel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\SupplyChain\ProductUnitService;

class CreateDirectItemMovement extends Component
{
    public $supplier_id, $store_id, $movement_date, $reference_no, $invoice_no, $registered_by, $notes;
    public $items = [];
    public $supplier_search = '';
    public $store_search = '';
    public $product_search = '';

    protected $listeners = ['refreshSelect2'];

    public function mount()
    {
        $this->movement_date = date('Y-m-d');
        $this->registered_by = Auth::user()->name ?? '';
        $this->items[] = $this->emptyItem();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'supplier_id' || $propertyName === 'store_id') {
            $this->dispatchBrowserEvent('reinit-select2');
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

            $this->items[$index]['related_units'] = collect($this->items[$index]['related_units'])
                ->unique('id')
                ->values()
                ->all();

            if ($index > 0) {
                $this->items[$index]['batch_no'] = $this->items[$index - 1]['batch_no'] ?? '';
                $this->items[$index]['expiry_date'] = $this->items[$index - 1]['expiry_date'] ?? '';
            }
        }

        if ($field === 'unit_id' && $this->items[$index]['product_id'] && $this->store_id) {
            $unitId = $value;
            $productId = $this->items[$index]['product_id'];

            $balances = productstorebalance::where('product_id', $productId)
                ->where('store_id', $this->store_id)
                ->get();

            $unitConversions = productunit::where('product_id', $productId)->get();
            $total_in_base = 0;

            foreach ($balances as $b) {
                $total_in_base += $b->quantity_in_base_unit;
            }

            $service = new ProductUnitService();
            $result = $service->calculateSmallestUnits($productId, $unitId);
            $rate = $result['total_small_units'] ?? 1;

            $this->items[$index]['current_balance'] = $rate ? round($total_in_base / $rate, 2) : 0;

            $unitInfo = $unitConversions->first(function ($q) use ($unitId) {
                return $q->unit_large_id == $unitId || $q->unit_small_id == $unitId;
            });

            $this->items[$index]['cost_price'] = $unitInfo->cost_price ?? 0;
            $this->items[$index]['sale_price'] = $unitInfo->sale_price ?? 0;
        }
    }

    public function addItemRow()
    {
        $this->items[] = $this->emptyItem();

        $lastIndex = count($this->items) - 1;
        $prevIndex = $lastIndex - 1;

        if ($prevIndex >= 0) {
            $this->items[$lastIndex]['batch_no'] = $this->items[$prevIndex]['batch_no'] ?? '';
            $this->items[$lastIndex]['expiry_date'] = $this->items[$prevIndex]['expiry_date'] ?? '';
        }
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

        DB::beginTransaction();

        $lastMovement = StoreMovement::latest('id')->first();
        $lastId = $lastMovement ? $lastMovement->id + 1 : 1;
        $movementNumber = 'MOV-' . str_pad($lastId, 6, '0', STR_PAD_LEFT);

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
                'movement_type' => 'direct_addition',
                'reference_no' => $this->reference_no,
                'invoice_no'=> $this->invoice_no,
                'movement_date' => $this->movement_date,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            $balance = productstorebalance::firstOrNew([
                'product_id' => $item['product_id'],
                'store_id' => $this->store_id,
                'batch_no' => $item['batch_no'],
                'units_id' => $item['unit_id'],
                'expiry_date' => $item['expiry_date'],
            ]);

            $balance->quantity_in_base_unit = ($balance->quantity_in_base_unit ?? 0) + $quantity_in_base;
            $balance->save();
        }

        DB::commit();
        session()->flash('success', 'تم حفظ الإضافة المباشرة بنجاح - رقم الحركة: ' . $movementNumber);
    }

    public function render()
    {
        $suppliers = suppliersdata::all();
        $stores = storemaindata::all();

        $products = collect();
        if ($this->store_id) {
            $products = storeproductmodel::with('product')
                ->where('store_id', $this->store_id)
                ->whereHas('product', function ($q) {
                    if ($this->product_search) {
                        $q->where('name_ar', 'like', '%' . $this->product_search . '%')
                          ->orWhere('name_en', 'like', '%' . $this->product_search . '%')
                          ->orWhere('product_code', 'like', '%' . $this->product_search . '%');
                    }
                })
                ->get()
                ->map(function ($sp) {
                    return $sp->product;
                });
        }

        return view('livewire.supply-chain.program.create-direct-item-movement', [
            'suppliers' => $suppliers,
            'stores' => $stores,
            'products' => $products,
            'units' => unit::all(),
        ]);
    }
}