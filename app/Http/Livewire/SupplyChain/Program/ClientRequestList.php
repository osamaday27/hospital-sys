<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\clientitemrequest;
use App\Models\supply_chain\storemaindata;

class ClientRequestList extends Component
{
    public $requests = [];

    public function mount()
    {
        $this->requests = clientitemrequest::with('store')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function editRequest($id)
    {
        return redirect()->route('edit_request_client', $id);
    }

    public function closeRequest($id)
    {
        $request = clientitemrequest::findOrFail($id);
        if ($request->status === 'new') {
            $request->status = 'closed';
            $request->save();
            session()->flash('success', __('supply_chain_trans.request_closed_successfully'));
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.client-request-list');
    }
}