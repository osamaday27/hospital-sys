<?php
// app/Http/Livewire/Finance/Program/CreateDiscountRequest.php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use App\Models\finance\discountrequest;
use App\Models\finance\invoices;
use Illuminate\Support\Facades\Auth;

class CreateDiscountRequest extends Component
{
   // داخل الكلاس
public $invoice_id, $discount_type, $percentage, $amount, $reason;
public $selected_invoice, $calculated_amount;

public function mount($invoice_id)
{
    $this->invoice_id = $invoice_id;
    $this->selected_invoice = invoices::findOrFail($invoice_id);
}

public function updatedPercentage()
{
    $this->calculateAmount();
}

public function calculateAmount()
{
    if ($this->discount_type === 'percentage' && $this->selected_invoice && $this->percentage > 0) {
        $this->calculated_amount = ($this->percentage / 100) * $this->selected_invoice->patient_contribution;
        $this->amount = $this->calculated_amount;
    }
}
public function submit()
{
    $this->validate([
        'discount_type' => 'required|in:percentage,amount',
        'amount' => 'required|numeric|min:0.01',
        'reason' => 'required|string|max:1000',
    ]);
    $invoice = invoices::findOrFail($this->invoice_id);
    $invoice->status = 'pending_discount';
    $invoice->save();
    if ($this->selected_invoice && $this->amount > $this->selected_invoice->patient_contribution) {
        $this->addError('amount', __('lockers_trans.amount_exceeds_contribution'));
        return;
    }

    discountrequest::create([
        'invoice_id' => $this->invoice_id,
        'user_id' => Auth::id(),
        'amount' => $this->amount,
        'reason' => $this->reason,
    ]);

    session()->flash('success', __('lockers_trans.discount_request_submitted'));
    return redirect()->route('locker_desktop.index');
}

    public function render()
    {
        return view('livewire.finance.program.create-discount-request');
    }
}