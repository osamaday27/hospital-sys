<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Finance\surgeries_pricing;
use App\Models\Finance\FinContractLastLevel;
use App\Models\Finance\PriceList;
use App\Models\Finance\Service;

class SurgeriesPricingSettings extends Component
{
    public $pricing_id;
    public $data = [];

    public function mount($id = null)
    {
        if ($id) {
            $this->pricing_id = $id;
            $this->data = surgeries_pricing::findOrFail($id)->toArray();
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'data.fin_services_id' => 'required|exists:fin_services,id',
            'data.base_price' => 'required|numeric',
            'data.standard_time' => 'nullable',
            'data.status' => 'required|in:active,inactive',
        ]);

        $this->data['updated_by'] = auth()->id();
        if ($this->pricing_id) {
            surgeries_pricing::find($this->pricing_id)->update($this->data);
        } else {
            $this->data['created_by'] = auth()->id();
            surgeries_pricing::create($this->data);
        }

        session()->flash('message', __('surgery_price.saved_successfully'));
    }

    public function render()
    {
        return view('livewire.surgical.surgeries_pricing_settings', [
            'services' => Service::all(),
            'contracts' => FinContractLastLevel::all(),
            'priceLists' => PriceList::all(),
        ]);
    }
}