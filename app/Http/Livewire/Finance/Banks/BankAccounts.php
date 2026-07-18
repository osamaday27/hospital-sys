<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankAccount;
use App\Models\Finance\Bank;

class BankAccounts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $account_id, $bank_id, $account_name, $account_number, $currency = 'EGP', $opening_balance = 0, $current_balance = 0, $type = 'current', $status = 'active';
    public $isEdit = false;

    protected $rules = [
        'bank_id'         => 'required|exists:fin_banks,id',
        'account_name'    => 'required|string|max:255',
        'account_number'  => 'required|string|max:255|unique:fin_bank_accounts,account_number',
        'currency'        => 'required|string|max:10',
        'opening_balance' => 'numeric|min:0',
        'current_balance' => 'numeric|min:0',
        'type'            => 'required|in:current,savings,deposit',
        'status'          => 'required|in:active,inactive',
    ];

    public function resetFields()
    {
        $this->account_id = null;
        $this->bank_id = null;
        $this->account_name = '';
        $this->account_number = '';
        $this->currency = 'EGP';
        $this->opening_balance = 0;
        $this->current_balance = 0;
        $this->type = 'current';
        $this->status = 'active';
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        BankAccount::create($data);
        session()->flash('success', __('bank_trans.account_added'));
        $this->resetFields();
    }

    public function edit($id)
    {
        $acc = BankAccount::findOrFail($id);
        $this->fill($acc->toArray());
        $this->account_id = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate([
            'bank_id'         => 'required|exists:fin_banks,id',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:255|unique:fin_bank_accounts,account_number,' . $this->account_id,
            'currency'        => 'required|string|max:10',
            'opening_balance' => 'numeric|min:0',
            'current_balance' => 'numeric|min:0',
            'type'            => 'required|in:current,savings,deposit',
            'status'          => 'required|in:active,inactive',
        ]);

        BankAccount::findOrFail($this->account_id)->update($data);
        session()->flash('success', __('bank_trans.account_updated'));
        $this->resetFields();
    }

    public function delete($id)
    {
        BankAccount::findOrFail($id)->delete();
        session()->flash('success', __('bank_trans.account_deleted'));
    }

    public function render()
    {
        return view('livewire.finance.banks.bank-accounts', [
            'accounts' => BankAccount::with('bank')->paginate(10),
            'banks' => Bank::all(),
        ])->extends('layouts.master')->section('content');
    }
}