<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use App\Models\finance\invoicecancellationrequest;
use App\Models\finance\invoices;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
class ManageCancellationRequests extends Component
{
    protected $requests;
    use WithPagination;
    public $filter_status = 'pending';
    public $loading = false;
    public $search = '';

    public function mount()
    {
        $this->loadRequests();
    }

    public function updatedFilterStatus()
    {
        $this->loadRequests();
    }

    public function getRequestsProperty()
    {
        return $this->requests;
    }
    
public function loadRequests()
{
    $this->loading = true;

    $this->requests = invoicecancellationrequest::with(['invoice.patient', 'requester', 'approver'])
        ->where('status', $this->filter_status)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $this->loading = false;
}


    public function approve($id)
    {
        $request = invoicecancellationrequest::findOrFail($id);
        $request->status = 'approved';
        $request->approved_by = Auth::id();
        $request->approved_at = now();
        $request->save();

        $request->invoice->update(['status' => 'cancelled']);

        session()->flash('success', __('lockers_trans.cancellation_approved'));
        $this->loadRequests();
    }

    public function reject($id)
    {
        $request = invoicecancellationrequest::findOrFail($id);
        $request->status = 'rejected';
        $request->approved_by = Auth::id();
        $request->approved_at = now();
        $request->save();

        session()->flash('success', __('lockers_trans.cancellation_rejected'));
        $this->loadRequests();
    }


    public function render()
    {
        $requests = invoicecancellationrequest::with(['invoice.patient', 'requester', 'approver'])
            ->where('status', $this->filter_status)
            ->where(function ($query) {
                $query->whereHas('invoice', function ($q) {
                    $q->where('number', 'like', '%' . $this->search . '%');
                })->orWhereHas('invoice.patient', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return view('livewire.finance.program.manage-cancellation-requests', compact('requests'));
    }
    
}