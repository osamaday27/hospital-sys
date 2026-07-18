<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\ClientItemRequest;

class ClientRequestShow extends Component
{
    public $request;

    public function mount($id)
    {
        $this->request = ClientItemRequest::with([
            'client:id,full_name,medical_number',
            'store:id,name_ar,manual_code',
            'items.product:id,name_ar,product_code',
            'items.unit:id,name_ar,name_en'
        ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.supply-chain.program.client-request-show');
    }
}