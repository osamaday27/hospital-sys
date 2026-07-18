<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\departmentItemRequest;

class departmentRequestshow extends Component
{
    public $request;

    public function mount($id)
    {
        $this->request = departmentItemRequest::with(['department', 'store', 'details.product', 'details.unit'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.supply-chain.program.department-requestshow');
    }
}