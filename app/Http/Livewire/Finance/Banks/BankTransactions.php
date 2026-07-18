<?php
namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\Bank;
use App\Models\Finance\BankAccount;
use Illuminate\Support\Facades\Auth;

class BankTransactions extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $bank_id, $account_id, $transaction_date, $type = 'deposit', $amount, $reference, $related_invoice_id, $notes;
    public $transaction_id;
    public $isEdit = false;

    protected $rules = [
        'bank_id'          => 'required|exists:fin_banks,id',
        'account_id'       => 'required|exists:fin_bank_accounts,id',
        'transaction_date' => 'required|date',
        'type'             => 'required|in:deposit,withdrawal,transfer,fees,interest',
        'amount'           => 'required|numeric|min:0.01',
        'reference'        => 'nullable|string|max:255',
        'notes'            => 'nullable|string',
        'related_invoice_id' => 'nullable|exists:fin_invoices,id',
    ];

    public function save()
    {
        $data = $this->validate();
        $data['user_id'] = Auth::id();

        if ($this->isEdit && $this->transaction_id) {
            BankTransaction::findOrFail($this->transaction_id)->update($data);
            session()->flash('success', __('bank_trans.transaction_updated'));
        } else {
            BankTransaction::create($data);
            session()->flash('success', __('bank_trans.transaction_added'));
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $transaction = BankTransaction::findOrFail($id);
        $this->fill($transaction->toArray());
        $this->transaction_id = $id;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        BankTransaction::findOrFail($id)->delete();
        session()->flash('success', __('bank_trans.transaction_deleted'));
    }

    public function resetFields()
    {
        $this->bank_id = $this->account_id = $this->transaction_date = $this->amount = $this->reference = $this->related_invoice_id = $this->notes = null;
        $this->type = 'deposit';
        $this->transaction_id = null;
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.finance.banks.bank-transactions', [
            'transactions' => BankTransaction::with(['bank','account','user'])->orderByDesc('transaction_date')->paginate(10),
            'banks' => Bank::all(),
            'accounts' => BankAccount::all(),
        ]);
    }
}