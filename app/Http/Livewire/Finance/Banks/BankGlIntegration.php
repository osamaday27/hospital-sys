<?php

namespace App\Http\Livewire\Finance\Banks;


use Livewire\Component;
use App\Models\Finance\BankAccount;
use App\Models\Finance\GLAccount;

class BankGlIntegration extends Component
{
    public $account_id, $gl_account_id;

   public $search = '';
public $editMode = false;

public function edit($id)
{
    $account = BankAccount::findOrFail($id);
    $this->account_id = $account->id;
    $this->gl_account_id = $account->gl_account_id;
    $this->editMode = true;
}

public function save()
{
    $this->validate([
        'account_id'   => 'required|exists:fin_bank_accounts,id',
        'gl_account_id'=> 'required|exists:fin_s_gl_accounts,id',
    ]);

    $bankAccount = BankAccount::findOrFail($this->account_id);
    $bankAccount->update(['gl_account_id' => $this->gl_account_id]);

    session()->flash('success', $this->editMode ? __('bank_trans.gl_updated') : __('bank_trans.gl_linked'));

    $this->reset(['account_id','gl_account_id','editMode']);
}

public function render()
{
    $accounts = BankAccount::with('glAccount')
        ->when($this->search, function ($q) {
            $q->where('account_name', 'like', "%{$this->search}%")
              ->orWhere('account_number', 'like', "%{$this->search}%");
        })
        ->paginate(10);

    return view('livewire.finance.Banks.bank-gl-integration', [
        'bankAccounts' => $accounts,
        'glAccounts'   => GLAccount::all(),
    ])->extends('layouts.master')->section('content');
}

}