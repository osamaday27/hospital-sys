<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\EmployeeItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeRequestList extends Component
{
    public $requests = [];

    public function mount()
    {
        $this->fetchRequests();
    }

    public function fetchRequests()
    {
        $this->requests = EmployeeItemRequest::with(['store', 'employee'])
            ->where('employee_id', Auth::user()->employee_id)
            ->orderByDesc('request_date')
            ->get();
    }

    public function editRequest($id)
    {
        $request = EmployeeItemRequest::findOrFail($id);

        if ($request->status !== 'new') {
            session()->flash('error', __('supply_chain_trans.cannot_edit_closed_request'));
            return;
        }

        return redirect()->route('edit_request_employee.employee_requestedit', ['id' => $id]);

    }

    public function closeRequest($id)
    {
        $request = EmployeeItemRequest::findOrFail($id);

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
        return view('livewire.supply-chain.program.employee-request-list', [
            'requests' => $this->requests,
        ]);
    }
}