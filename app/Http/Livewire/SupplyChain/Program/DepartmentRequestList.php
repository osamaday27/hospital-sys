<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\departmentItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class departmentRequestList extends Component
{
    public $requests = [];

    public function mount()
    {
        $this->fetchRequests();
    }

    public function fetchRequests()
    {
        $this->requests = departmentItemRequest::with(['store', 'department'])
         
            ->orderByDesc('request_date')
            ->get();
    }

    public function editRequest($id)
    {
        $request = departmentItemRequest::findOrFail($id);

        if ($request->status !== 'new') {
            session()->flash('error', __('supply_chain_trans.cannot_edit_closed_request'));
            return;
        }

        return redirect()->route('edit_request_department.department_requestedit', ['id' => $id]);

    }

    public function closeRequest($id)
    {
        $request = departmentItemRequest::findOrFail($id);

        if ($request->status === 'new') {
            $request->update(['status' => 'closed']);
            session()->flash('success', __('supply_chain_trans.request_closed_successfully'));
            $this->fetchRequests();
        } else {
            session()->flash('error', __('supply_chain_trans.request_already_closed'));
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.department-request-list', [
            'requests' => $this->requests,
        ]);
    }
}