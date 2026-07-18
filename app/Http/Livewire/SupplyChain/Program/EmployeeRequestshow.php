<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\EmployeeItemRequest;

class EmployeeRequestshow extends Component
{
    public $request;

    public function mount($id)
    {
        $this->request = EmployeeItemRequest::with(['employee', 'store', 'details.product', 'details.unit'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.supply-chain.program.employee-requestshow');
    }
}