<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\AdvancePayment;
use Illuminate\Support\Facades\DB;

class PaymentsLog extends Component
{
    use WithPagination;

    public $selectedReceipt;
    public $search = '';

    protected $listeners = ['refreshPayments' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDetails($id)
    {
        $this->selectedReceipt = AdvancePayment::with(['patient', 'user'])->findOrFail($id);
        $this->dispatchBrowserEvent('showReceiptDetails');
    }

    public function confirmCancellation($id)
    {
        $this->selectedReceipt = AdvancePayment::findOrFail($id);
        $this->dispatchBrowserEvent('showCancelConfirm');
    }

    public function cancelReceipt()
    {
        if ($this->selectedReceipt) {
            DB::transaction(function () {
                $this->selectedReceipt->update([
                    'status' => 'cancelled',
                ]);
            });

            session()->flash('success', __('finance_trans.cancel_success'));
            $this->selectedReceipt = null;
            $this->emit('refreshPayments');
        }
    }

    public function render()
    {
        $receipts = AdvancePayment::with(['patient', 'user'])
            ->where(function ($q) {
                $q->whereHas('patient', function ($qq) {
                    $qq->where('full_name', 'like', '%' . $this->search . '%');
                })->orWhere('id', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.finance.program.payments-log', [
            'receipts' => $receipts,
        ]);
    }
}