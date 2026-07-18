<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\EmployeeItemRequest;
use App\Models\supply_chain\EmployeeItemRequestDetail;
use App\Models\supply_chain\StoreMainData;
use App\Models\supply_chain\ProductData;
use App\Models\supply_chain\Unit;
use App\Models\supply_chain\ProductUnit;
use App\Models\supply_chain\ProductStoreBalance;
use App\Models\hr\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeRequestCreate extends Component
{
    public $employee_id, $store_id, $request_date, $reason;
    public $items = [];
    public $available_products;

    public function mount()
    {
        $this->request_date = now()->format('Y-m-d');
        $this->items[] = $this->emptyItem();
        $this->available_products = collect();
    }

    public function emptyItem()
    {
        return [
            'product_id' => '',
            'unit_id' => '',
            'quantity' => '',
            'related_units' => [],
        ];
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'store_id') {
            $this->filterProductsByStore();
        }

        foreach ($this->items as $index => $item) {
            if ($propertyName === "items.$index.product_id") {
                $this->items[$index]['unit_id'] = '';
                $this->items[$index]['related_units'] = $this->getSingleUnitForProduct($item['product_id']);
            }
        }
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

            // نسخ رقم الباتش وتاريخ الانتهاء للعنصر الجديد من العنصر السابق (إن وجد)
            if ($index > 0) {
                $this->items[$index]['batch_no'] = $this->items[$index - 1]['batch_no'] ?? '';
                $this->items[$index]['expiry_date'] = $this->items[$index - 1]['expiry_date'] ?? '';
            }
        }

        if ($field === 'unit_id' && $this->items[$index]['product_id'] && $this->store_id) {
            $unitId = $value;
            $productId = $this->items[$index]['product_id'];
            $balance = productstorebalance::where('product_id', $productId)->where('store_id', $this->store_id)->where('units_id', $unitId)->sum('quantity');

            $unitInfo = productunit::where('product_id', $productId)
                ->where(function ($q) use ($unitId) {
                    $q->where('unit_large_id', $unitId)->orWhere('unit_small_id', $unitId);
                })
                ->first();

            $this->items[$index]['current_balance'] = $balance;
            $this->items[$index]['cost_price'] = $unitInfo->cost_price ?? 0;
            $this->items[$index]['sale_price'] = $unitInfo->sale_price ?? 0;
        }
    }

    public function filterProductsByStore()
    {
        $this->available_products = ProductData::whereHas('productstorebalance', function ($q) {
            $q->where('store_id', $this->store_id);
        })->get();
    }

    public function getSingleUnitForProduct($productId)
    {
        $unit = ProductUnit::where('product_id', $productId)
            ->where('unit_type', 'small')
            ->with('smallUnit')
            ->first();

        if ($unit && $unit->smallUnit) {
            return [[
                'id' => $unit->smallUnit->id,
                'name' => $unit->smallUnit->name_ar,
            ]];
        }

        return [];
    }

    public function addItem()
    {
        $this->items[] = $this->emptyItem();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'store_id' => 'required|exists:store_main_date,id',
            'request_date' => 'required|date',
            'items.*.product_id' => 'required|exists:products_data,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () {
            $request = EmployeeItemRequest::create([
                'employee_id' => $this->employee_id,
                'store_id' => $this->store_id,
                'request_date' => $this->request_date,
                'reason' => $this->reason,
                'status' => 'new',
                'request_number' => 'REQ-' . now()->format('YmdHis'),
                'created_by' => Auth::id(),
            ]);

            foreach ($this->items as $item) {
                EmployeeItemRequestDetail::create([
                    'request_id' => $request->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'created_by' => Auth::id(),
                ]);
            }
        });

        session()->flash('success', 'تم حفظ الطلب بنجاح.');
        $this->reset(['employee_id', 'store_id', 'reason', 'items']);
        $this->request_date = now()->format('Y-m-d');
        $this->items[] = $this->emptyItem();
    }

    public function render()
    {
        return view('livewire.supply-chain.program.employee-request-create', [
            'employees' => Employee::all(),
            'stores' => StoreMainData::all(),
            'products' => $this->available_products,
            'units' => Unit::all(),
        ]);
    }
}