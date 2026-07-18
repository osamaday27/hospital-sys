<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\storeItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class storeRequestList extends Component
{
    public $requests = [];

    public function mount()
    {
        $this->fetchRequests();
    }

    public function fetchRequests()
    {
        $this->requests = storeItemRequest::with(['store'])
            ->orderByDesc('request_date')
            ->get();
    }

    public function editRequest($id)
    {
        $request = storeItemRequest::findOrFail($id);

        if ($request->status !== 'new') {
            session()->flash('error', __('supply_chain_trans.cannot_edit_closed_request'));
            return;
        }

        return redirect()->route('edit_request_store.store_requestedit', ['id' => $id]);

    }

    public function closeRequest($id)
    {
        $request = storeItemRequest::findOrFail($id);

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
        return view('livewire.supply-chain.program.store-request-list', [
            'requests' => $this->requests,
        ]);
    }
}