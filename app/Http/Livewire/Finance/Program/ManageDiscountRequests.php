<?php
// app/Http/Livewire/Finance/Program/ManageDiscountRequests.php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\finance\discountrequest;
use App\Models\finance\invoices;
use Illuminate\Support\Facades\Auth;

class ManageDiscountRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        $request = discountrequest::findOrFail($id);
        $request->status = 'approved';
        $request->action_by = Auth::id();
        $request->action_date = now();
        $request->save();

        $invoice = $request->invoice;
      
        $invoice->status = 'discount_approved';
        $invoice->save();

        session()->flash('success', __('lockers_trans.discount_approved_successfully'));
    }

    public function reject($id)
    {
        $request = discountrequest::findOrFail($id);
        $request->status = 'rejected';
        $request->action_by = Auth::id();
        $request->action_date = now();
        $request->save();

        $invoice = $request->invoice;
        $invoice->status = 'discount_rejected';
        $invoice->save();

        session()->flash('error', __('lockers_trans.discount_rejected'));
    }

    public function render()
    {
        $requests = discountrequest::with(['invoice', 'user'])
            ->whereHas('invoice', function ($query) {
                $query->where('number', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);
    
        return view('livewire.finance.program.manage-discount-requests', [
            'requests' => $requests,
        ]);
    }
    
}