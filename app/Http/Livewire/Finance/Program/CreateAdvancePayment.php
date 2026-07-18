<?php

namespace App\Http\Livewire\Finance\Program;


use Livewire\Component;
use App\Models\finance\advancepayment;
use App\Models\administrative\medical_record;
use App\Models\finance\slocker;
use Illuminate\Support\Facades\Auth;

class CreateAdvancePayment extends Component
{
    public $patient_id;
    public $amount;
    public $currency = 'EGP';
    public $payment_method = 'cash';
    public $payment_date;
    public $notes;
    public $card_number;
    public $card_bank;
    public $transfer_number;
    public $transfer_bank;
    public $locker_id;

    protected $listeners = ['openAdvancePayment' => 'setPatient'];

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
    }

    public function setPatient($patientId)
    {
        $this->resetValidation();
        $this->patient_id = $patientId;
        $this->dispatchBrowserEvent('showAdvancePaymentModal');
    }

    public function save()
    {
        $this->validate([
            'patient_id' => 'required|exists:medical_records,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'payment_method' => 'required|string',
            'locker_id' => 'required|exists:slockers,id',
            'payment_date' => 'required|date',
        ]);

        advancepayment::create([
            'patient_id' => $this->patient_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_date' => $this->payment_date,
            'notes' => $this->notes,
            'card_number' => $this->card_number,
            'card_bank' => $this->card_bank,
            'transfer_number' => $this->transfer_number,
            'transfer_bank' => $this->transfer_bank,
            'locker_id' => $this->locker_id,
            'user_id' => Auth::id(),
            'status' => 'paid',
        ]);

        $this->dispatchBrowserEvent('hideAdvancePaymentModal');
        $this->emit('paymentAdded');
        session()->flash('success', __('finance_trans.payment_added_successfully'));
    }

    public function render()
    {
        return view('livewire.finance.create-advance-payment', [
            'lockers' => slocker::all(),
            'patient' => $this->patient_id ? medical_record::find($this->patient_id) : null
        ]);
    }
}