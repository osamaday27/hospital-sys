<?php

namespace App\Http\Livewire\SupplyChain\Settings\StoreMainData;

use Livewire\Component;
use App\Models\supply_chain\storemaindata;
use App\Models\general\Branch;

class StoreBasicData extends Component
{
    public $storeId;

    // الحقول الأساسية
    public $manual_code, $name_ar, $name_en, $status = 'active';
    public $store_type = 'main', $branch_id;
    public $address_ar, $address_en;

    // الاتصال
    public $contact_methods = [];
    public $contact_type = 'email', $contact_value;

    // التفاصيل الأخرى
    public $profit_margin = 0, $daily_avg_consumption = 0, $min_order_quantity = 0, $max_order_quantity = 0;
    public $pricing_method = 'cost', $opening_balance = 0;
    public $is_virtual = false, $is_inventory_allowed = true;

    public function mount($storeId = null)
    {
        $this->storeId = $storeId;

        if ($storeId) {
            $store = storemaindata::findOrFail($storeId);

            $this->manual_code = data_get($store, 'manual_code', '');
            $this->name_ar = data_get($store, 'name_ar', '');
            $this->name_en = data_get($store, 'name_en', '');
            $this->address_ar = data_get($store, 'address_ar', '');
            $this->address_en = data_get($store, 'address_en', '');
            $this->store_type = data_get($store, 'store_type', 'main');
            $this->branch_id = data_get($store, 'branch_id');
            $this->status = data_get($store, 'status', 'active');

            $this->profit_margin = data_get($store, 'profit_margin', 0);
            $this->daily_avg_consumption = data_get($store, 'daily_avg_consumption', 0);
            $this->min_order_quantity = data_get($store, 'min_order_quantity', 0);
            $this->max_order_quantity = data_get($store, 'max_order_quantity', 0);
            $this->pricing_method = data_get($store, 'pricing_method', 'cost');
            $this->opening_balance = data_get($store, 'opening_balance', 0);

            $this->is_virtual = (bool) data_get($store, 'is_virtual', false);
            $this->is_inventory_allowed = (bool) data_get($store, 'is_inventory_allowed', true);

            $this->contact_methods = is_array($store->contact_methods) ? $store->contact_methods : [];
        }
    }


    public function addContactMethod()
    {
        if ($this->contact_type && $this->contact_value) {
            $this->contact_methods[] = [
                'type' => $this->contact_type,
                'value' => $this->contact_value,
            ];
            $this->contact_type = 'email';
            $this->contact_value = '';
        }
    }

    public function removeContactMethod($index)
    {
        unset($this->contact_methods[$index]);
        $this->contact_methods = array_values($this->contact_methods);
    }

    public function save()
    {
        $this->validate([
            'manual_code' => 'required|string|unique:store_main_date,manual_code,' . $this->storeId,
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'store_type' => 'required|in:main,sub',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'pricing_method' => 'required|in:cost,selling',
        ]);

        $data = [
            'manual_code' => $this->manual_code,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'address_ar' => $this->address_ar,
            'address_en' => $this->address_en,
            'store_type' => $this->store_type,
            'branch_id' => $this->branch_id,
            'status' => $this->status,
            'profit_margin' => $this->profit_margin,
            'daily_avg_consumption' => $this->daily_avg_consumption,
            'min_order_quantity' => $this->min_order_quantity,
            'max_order_quantity' => $this->max_order_quantity,
            'pricing_method' => $this->pricing_method,
            'opening_balance' => $this->opening_balance,
            'is_virtual' => $this->is_virtual,
            'is_inventory_allowed' => $this->is_inventory_allowed,
            'contact_methods' => $this->contact_methods,
        ];

        $store = storemaindata::updateOrCreate(
            ['id' => $this->storeId],
            $data
        );

        $this->storeId = $store->id;

        $this->dispatchBrowserEvent('store-saved');

        $this->emit('storeIdUpdated', $this->storeId);
        $this->emit('goToTab', 'tab2');
    }

    public function render()
    {
        return view('livewire.supply-chain.settings.store-main-data.store-basic-data', [
            'branches' => Branch::all(),
        ]);
    }
}
