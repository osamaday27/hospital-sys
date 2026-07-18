<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use App\Models\finance\{
    invoices,
    receipts,
    slocker,
    reasonspartialpayment,
    finservicevisit,
    discountrequest,
    invoicecancellationrequest,
    lockertransaction,
    lockerdailybalance
};
use App\Models\supply_chain\AdmBVisitSupply;
use App\Models\administrative\reservations;
use App\Models\Finance\Bank;
use App\Models\Finance\BankMachine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoicePayment extends Component
{
    public $invoice_id, $invoice;
    public $services = [], $lockers = [], $reasons = [];
    public $discount_requests = [], $selected_discounts = [], $cancellation_request;
    public $payments = [], $supplies = [];

    public $banks = [], $machines = [];

    public $total_paid = 0, $total_remain = 0, $approved_discount_total = 0, $patient_share = 0;

    public function mount($invoice_id)
    {
        $this->invoice_id = $invoice_id;
        $this->invoice = invoices::with(['patient', 'reservations.doctor', 'reservations.clinic', 'reservations.service'])
            ->findOrFail($invoice_id);

        if ($this->invoice->status === 'paid') {
            session()->flash('error', __('lockers_trans.invoice_already_paid'));
            return redirect()->route('locker_desktop.index');
        }

        $this->services = finservicevisit::with('service')->where('id_invoices', $invoice_id)->get();

        $this->supplies = AdmBVisitSupply::with(['product', 'unit', 'store'])
            ->where('reservation_id', $this->invoice->reservation_id)
            ->get();

        $this->patient_share = $this->invoice->patient_contribution;

        $this->lockers = slocker::whereHas('users', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();

        $this->reasons = reasonspartialpayment::where('status', 'active')->pluck('name_ar', 'id')->toArray();

        $this->discount_requests = discountrequest::where('invoice_id', $invoice_id)
            ->where('status', 'approved')->get();

        $this->cancellation_request = invoicecancellationrequest::where('invoice_id', $invoice_id)
            ->where('status', 'pending')->first();

        $this->banks = Bank::where('status', 'active')->orderBy('name_ar')->get();
        $this->machines = BankMachine::where('status', 'active')->orderBy('machine_code')->get();

        $this->updateDiscountTotal();

        $previous_paid = receipts::where('invoices_id', $this->invoice_id)->sum('total_paid');
        $due_now = max(0, $this->patient_share - $this->approved_discount_total - $previous_paid);

        $default = $this->newPaymentMethod();
        $default['amount'] = $due_now;
        $this->payments = [$default];

        $this->updateDiscountTotal();
    }

    public function newPaymentMethod()
    {
        return [
            'method' => 'cash',
            'amount' => $this->total_remain,
            'locker_id' => '',
            'partial_reason' => '',
            'bank_id' => null,
            'bank_name' => '',
            'transaction_number' => '',
            'bank_machine_id' => null,
            'card_info' => [
                'holder_name' => '',
                'card_number' => '',
            ],
        ];
    }

    public function addPayment()
    {
        $this->payments[] = $this->newPaymentMethod();
        $this->updateDiscountTotal();
    }

    public function removePayment($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
        $this->updateDiscountTotal();
    }

    public function updatedPayments()
    {
        $this->updateDiscountTotal();
    }

    public function updatedSelectedDiscounts()
    {
        $this->updateDiscountTotal();
    }

    protected function updateDiscountTotal()
    {
        $this->approved_discount_total = $this->discount_requests
            ->whereIn('id', $this->selected_discounts)
            ->sum('amount');
        $this->approved_discount_total = min($this->approved_discount_total, $this->patient_share);

        $previous_paid = receipts::where('invoices_id', $this->invoice_id)->sum('total_paid');
        $current_paid = collect($this->payments)->sum(fn($p) => (float) ($p['amount'] ?? 0));

        $this->total_paid = $previous_paid + $current_paid;

        $this->total_remain = max(0, $this->patient_share - $this->approved_discount_total - $this->total_paid);

        if (count($this->payments) === 1) {
            $cap = max(0, $this->patient_share - $this->approved_discount_total - $previous_paid);
            $this->payments[0]['amount'] = min((float)($this->payments[0]['amount'] ?? 0), $cap);
        }
    }

    protected function validateConditionalPaymentFields(): bool
    {
        foreach ($this->payments as $idx => $pay) {
            $method = $pay['method'] ?? '';
            $amount = (float) ($pay['amount'] ?? null);

            if (empty($method)) {
                $this->addError("payments.$idx.method", __('validation.required'));
                return false;
            }

            if ($amount === null || $amount < 0) {
                $this->addError("payments.$idx.amount", __('validation.min', ['min' => 0]));
                return false;
            }

            if (empty($pay['locker_id'])) {
                $this->addError("payments.$idx.locker_id", __('validation.required'));
                return false;
            }

            if ($method === 'bank') {
                if (empty($pay['bank_id'])) {
                    $this->addError("payments.$idx.bank_id", __('validation.required'));
                    return false;
                }
                if (empty($pay['transaction_number'])) {
                    $this->addError("payments.$idx.transaction_number", __('validation.required'));
                    return false;
                }
            }

            if ($method === 'visa') {
                if (empty($pay['bank_machine_id'])) {
                    $this->addError("payments.$idx.bank_machine_id", __('validation.required'));
                    return false;
                }
                if (empty($pay['card_info']['card_number'])) {
                    $this->addError("payments.$idx.card_info.card_number", __('validation.required'));
                    return false;
                }
            }
        }
        return true;
    }

    protected function getAlreadyPaid()
    {
        return (float) receipts::where('invoices_id', $this->invoice->id)->sum('total_paid');
    }

    public function resetForm()
    {
        $this->selected_discounts = [];
        $this->payments = [];
        $this->updateDiscountTotal();

        $previous_paid = receipts::where('invoices_id', $this->invoice_id)->sum('total_paid');
        $due_now = max(0, $this->patient_share - $this->approved_discount_total - $previous_paid);

        $default = $this->newPaymentMethod();
        $default['amount'] = $due_now;

        $this->payments = [$default];
        $this->updateDiscountTotal();
    }

    public function submit()
    {
        if ($this->discount_requests->count() && empty($this->selected_discounts)) {
            session()->flash('error', __('lockers_trans.discount_warning'));
            return;
        }

        $this->validate([
            'payments.*.method' => 'required',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.locker_id' => 'required',
        ]);

        if (!$this->validateConditionalPaymentFields()) {
            session()->flash('error', __('validation.custom'));
            return;
        }

        DB::transaction(function () {
            foreach ($this->payments as $pay) {
                $amount = (float) ($pay['amount'] ?? 0);

                $bankId = null;
                $bankMachineId = null;
                $referenceNo = null;
                $cardHolder = null;
                $cardNumber = null;
                $bankName = $pay['bank_name'] ?? null;

                if ($pay['method'] === 'bank') {
                    $bankId = $pay['bank_id'];
                    $referenceNo = $pay['transaction_number'];
                }

                if ($pay['method'] === 'visa') {
                    $bankMachineId = $pay['bank_machine_id'];
                    $machine = $this->machines->firstWhere('id', $bankMachineId);
                    if ($machine) {
                        $bankId = $machine->bank_id;
                        $bankName = $machine->bank->name_ar ?? '';
                    }
                    $cardHolder = $pay['card_info']['holder_name'] ?? null;
                    $cardNumber = $pay['card_info']['card_number'] ?? null;
                }

                receipts::create([
                    'invoices_number' => $this->invoice->number,
                    'invoices_id' => $this->invoice->id,
                    'pay_type' => $pay['method'],
                    'total_paid' => $amount,
                    'total_remainder' => max(0, $this->patient_share - $this->approved_discount_total - ($this->getAlreadyPaid() + $amount)),
                    'id_patient' => $this->invoice->patient_id,
                    'id_user' => Auth::id(),
                    'treasury' => $pay['locker_id'],
                    'bank_id' => $bankId,
                    'bank_machine_id' => $bankMachineId,
                    'transaction_number' => $referenceNo,
                    'card_holder_name' => $cardHolder,
                    'card_number' => $cardNumber,
                    'pay_meta' => $bankName,
                ]);

                lockertransaction::create([
                    'locker_id' => $pay['locker_id'],
                    'transaction_type' => 'invoice_payment',
                    'amount' => $amount,
                    'direction' => 'in',
                    'reference_type' => invoices::class,
                    'reference_id' => $this->invoice->id,
                    'gl_account_id' => null,
                    'notes' => __('lockers_trans.invoice_payment_for') . ' #' . $this->invoice->number,
                    'created_by' => Auth::id(),
                    'bank_id' => $bankId,
                    'bank_machine_id' => $bankMachineId,
                    'reference_no' => $referenceNo,
                ]);

                lockerdailybalance::updateOrCreate(
                    ['locker_id' => $pay['locker_id'], 'date' => now()->format('Y-m-d')],
                    [
                        'closing_balance' => DB::raw('closing_balance + ' . $amount),
                        'actual_cash' => DB::raw('actual_cash + ' . $amount),
                        'created_by' => Auth::id(),
                    ]
                );
            }

            $total_after = receipts::where('invoices_id', $this->invoice->id)->sum('total_paid');
            $this->invoice->paid = $total_after;
            $this->invoice->discount = $this->approved_discount_total;
            $this->invoice->residual = max(0, $this->patient_share - $this->approved_discount_total - $total_after);
            $this->invoice->status = $this->invoice->residual <= 0 ? 'paid' : 'partial';
            $this->invoice->save();

            $reservation = reservations::find($this->invoice->reservation_id);
            if ($reservation) {
                $reservation->status = $this->invoice->status === 'paid' ? 'confirmed_paid' : 'confirmed_partial';
                $reservation->save();
            }
        });

        session()->flash('success', __('lockers_trans.payment_successful'));
        return redirect()->route('finance.invoice_payment_print', ['invoice_id' => $this->invoice->id]);
    }

    public function render()
    {
        return view('livewire.finance.program.invoice-payment', [
            'supplies' => $this->supplies,
            'banks' => $this->banks,
            'machines' => $this->machines,
        ]);
    }
}