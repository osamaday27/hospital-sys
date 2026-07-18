<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\administrative\medical_record;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\productdata;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\ClientItemRequest;
use App\Models\supply_chain\ClientItemRequestItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CreateClientItemRequest extends Component
{
    public $client_id, $store_id, $request_date, $reason, $registered_by;
    public $items = [];

    public $clients = [], $stores = [], $products = [];

    public function mount()
    {
        $this->request_date = date('Y-m-d');
        $this->registered_by = Auth::user()->name ?? '';
        $this->items[] = $this->emptyItem();
        $this->clients = medical_record::select('id', 'full_name')->get();
        $this->stores = storemaindata::select('id', 'name_ar')->get();
        $this->products = productdata::select('id', 'product_code', 'name_ar')->get();
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
        foreach ($this->items as $index => $item) {
            if ($propertyName === "items.$index.product_id" && $item['product_id']) {
                $this->items[$index]['related_units'] = productunit::where('product_id', $item['product_id'])->with('unit')->get()->map(function ($pu) {
                    return [
                        'id' => $pu->unit->id,
                        'name_ar' => $pu->unit->name_ar,
                        'name_en' => $pu->unit->name_en,
                    ];
                });
            }
        }
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
            'client_id' => 'required|exists:patient,id',
            'store_id' => 'required|exists:storemaindata,id',
            'request_date' => 'required|date',
            'items.*.product_id' => 'required|exists:productdata,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $request = ClientItemRequest::create([
                'client_id' => $this->client_id,
                'store_id' => $this->store_id,
                'request_date' => $this->request_date,
                'reason' => $this->reason,
                'user_id' => Auth::id(),
            ]);

            foreach ($this->items as $item) {
                ClientItemRequestItem::create([
                    'request_id' => $request->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            Session::flash('success', __('supply_chain_trans.success'));
            $this->resetExcept('clients', 'stores', 'products');
            $this->mount();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', __('supply_chain_trans.failed') . ' ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.create-client-item-request');
    }
}