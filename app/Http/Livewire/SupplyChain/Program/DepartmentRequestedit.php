<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\departmentItemRequest;
use App\Models\supply_chain\departmentItemRequestDetail;
use App\Models\supply_chain\ProductUnit;
use App\Models\supply_chain\ProductData;
use App\Models\supply_chain\Unit;
use App\Models\supply_chain\StoreMainData;

class departmentRequestedit extends Component
{
    public $request_id;
    public $request_date, $store_id, $reason;
    public $items = [];
    public $products = [];
    public $stores = [];

    public function mount($id)
    {
        $this->request_id = $id;
        $request = departmentItemRequest::with('details')->findOrFail($id);

        $this->request_date = $request->request_date;
        $this->store_id = $request->store_id;
        $this->reason = $request->reason;
        $this->products = ProductData::all();
        $this->stores = StoreMainData::all();

        foreach ($request->details as $detail) {
            $this->items[] = [
                'product_id' => $detail->product_id,
                'unit_id' => $detail->unit_id,
                'quantity' => $detail->quantity,
                'related_units' => $this->getUnits($detail->product_id)
            ];
        }
    }

    public function getUnits($productId)
    {
        $units = ProductUnit::where('product_id', $productId)->get();
        $list = [];
        foreach ($units as $unit) {
            if ($unit->unit_small_id) {
                $list[] = [
                    'id' => $unit->unit_small_id,
                    'name' => Unit::find($unit->unit_small_id)->name_ar,
                ];
            }
            if ($unit->unit_large_id) {
                $list[] = [
                    'id' => $unit->unit_large_id,
                    'name' => Unit::find($unit->unit_large_id)->name_ar,
                ];
            }
        }
        return collect($list)->unique('id')->values()->all();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'unit_id' => '',
            'quantity' => '',
            'related_units' => [],
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updated($propertyName)
    {
        foreach ($this->items as $index => $item) {
            if ($propertyName === "items.$index.product_id") {
                $this->items[$index]['related_units'] = $this->getUnits($item['product_id']);
            }
        }
    }

    public function update()
    {
        $this->validate([
            'request_date' => 'required|date',
            'store_id' => 'required|exists:store_main_date,id',
            'items.*.product_id' => 'required|exists:products_data,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $request = departmentItemRequest::findOrFail($this->request_id);
        $request->update([
            'request_date' => $this->request_date,
            'store_id' => $this->store_id,
            'reason' => $this->reason,
        ]);

        departmentItemRequestDetail::where('request_id', $this->request_id)->delete();

        foreach ($this->items as $item) {
            departmentItemRequestDetail::create([
                'request_id' => $this->request_id,
                'product_id' => $item['product_id'],
                'unit_id' => $item['unit_id'],
                'quantity' => $item['quantity'],
                'created_by' => auth()->id(),   
            ]);
        }

        session()->flash('success', __('supply_chain_trans.updated_successfully'));
        return redirect()->route('issue_items_to_department');
    }

    public function render()
    {
        return view('livewire.supply-chain.program.department-requestedit');
    }
}