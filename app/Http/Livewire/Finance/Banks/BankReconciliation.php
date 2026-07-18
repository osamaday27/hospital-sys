<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankReconciliation as BankReconciliationmodel;
use App\Models\Finance\Bank;
use App\Models\Finance\BankAccount;
use Illuminate\Support\Facades\Auth;

class BankReconciliation extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $reconciliation_id, $bank_id, $account_id, $period;
    public $opening_balance = 0, $closing_balance = 0, $difference = 0;
    public $isEdit = false;

    protected $rules = [
        'bank_id'         => 'required|exists:fin_banks,id',
        'account_id'      => 'required|exists:fin_bank_accounts,id',
        'period'          => 'required|string|max:20',
        'opening_balance' => 'required|numeric',
        'closing_balance' => 'required|numeric',
    ];

    public function updated($field)
    {
        $this->validateOnly($field);
        $this->calculateDifference();
    }

    private function calculateDifference()
    {
        $this->difference = $this->closing_balance - $this->opening_balance;
    }

    public function resetFields()
    {
        $this->reconciliation_id = null;
        $this->bank_id = null;
        $this->account_id = null;
        $this->period = null;
        $this->opening_balance = 0;
        $this->closing_balance = 0;
        $this->difference = 0;
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        $data['difference'] = $this->difference;

        BankReconciliationmodel::create($data);
        session()->flash('success', __('bank_trans.reconciliation_added'));
        $this->resetFields();
    }

    public function edit($id)
    {
        $rec = BankReconciliationmodel::findOrFail($id);
        $this->reconciliation_id = $rec->id;
        $this->bank_id = $rec->bank_id;
        $this->account_id = $rec->account_id;
        $this->period = $rec->period;
        $this->opening_balance = $rec->opening_balance;
        $this->closing_balance = $rec->closing_balance;
        $this->difference = $rec->difference;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate();
        $data['difference'] = $this->difference;

        BankReconciliationmodel::findOrFail($this->reconciliation_id)->update($data);
        session()->flash('success', __('bank_trans.reconciliation_updated'));
        $this->resetFields();
    }

    public function delete($id)
    {
        BankReconciliationmodel::findOrFail($id)->delete();
        session()->flash('success', __('bank_trans.reconciliation_deleted'));
    }

    public function approve($id)
    {
        $rec = BankReconciliationmodel::findOrFail($id);
        $rec->update([
            'reconciled_by'     => Auth::id(),
            'reconciliation_date' => now(),
        ]);
        session()->flash('success', __('bank_trans.reconciliation_approved'));
    }

    public function render()
    {
        return view('livewire.finance.banks.bank-reconciliation', [
            'records' => BankReconciliationmodel::with(['bank','account','reconciler'])->paginate(10),
            'banks'   => Bank::all(),
            'accounts'=> BankAccount::all(),
        ]);
    }
}