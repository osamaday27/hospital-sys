<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use App\Models\finance\invoices;
use App\Models\finance\invoicecancellationrequest;
use App\Models\finance\cancellationreason;
use Illuminate\Support\Facades\Auth;

class CreateCancellationRequest extends Component
{
    public $invoice_id;
    public $invoice;
    public $cancellation_reason = '';
    public $cancellation_notes = '';
    public $reasons = [];
    
    public function mount($invoice_id)
    {
        $this->invoice_id = $invoice_id;
        $this->invoice = invoices::with(['patient'])->findOrFail($invoice_id);
        $this->reasons = cancellationreason::where('status', 'active')->pluck('name_ar', 'id')->toArray();
    }
    
    public function submit()
    {
        $this->validate([
            'cancellation_reason' => 'required|exists:fin_s_cancellation_reasons,id',
            'cancellation_notes' => 'nullable|string|max:2000',
        ]);

        $invoice = invoices::findOrFail($this->invoice_id);
        $invoice->status = 'pending_cancellation';
        $invoice->save();

        invoicecancellationrequest::create([
            'invoice_id' => $invoice->id,
            'cancellation_reason' => $this->cancellation_reason,
            'cancellation_notes' => $this->cancellation_notes,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        session()->flash('success', __('lockers_trans.cancellation_request_sent'));
        return redirect()->route('locker_desktop.index');
    }

    public function render()
    {
        return view('livewire.finance.program.create-cancellation-request');
    }
}