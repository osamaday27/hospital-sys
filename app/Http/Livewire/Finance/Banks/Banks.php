<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Bank;
use App\Models\Finance\BankBranch;

class Banks extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $bank_id, $branch_id, $name_ar, $name_en, $account_number, $iban, $swift_code, $currency = 'EGP', $balance = 0, $status = 'active';
    public $isEdit = false;

    protected $rules = [
        'branch_id'      => 'nullable|exists:fin_bank_branches,id',
        'name_ar'        => 'required|string|max:255',
        'name_en'        => 'nullable|string|max:255',
        'account_number' => 'required|string|max:255|unique:fin_banks,account_number',
        'iban'           => 'nullable|string|max:255',
        'swift_code'     => 'nullable|string|max:255',
        'currency'       => 'required|string|max:10',
        'balance'        => 'numeric|min:0',
        'status'         => 'required|in:active,inactive',
    ];

    public function resetFields()
    {
        $this->bank_id = null;
        $this->branch_id = null;
        $this->name_ar = '';
        $this->name_en = '';
        $this->account_number = '';
        $this->iban = '';
        $this->swift_code = '';
        $this->currency = 'EGP';
        $this->balance = 0;
        $this->status = 'active';
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        Bank::create($data);
        session()->flash('success', 'تمت إضافة البنك بنجاح');
        $this->resetFields();
    }

    public function edit($id)
    {
        $bank = Bank::findOrFail($id);
        $this->fill($bank->toArray());
        $this->bank_id = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate([
            'branch_id'      => 'nullable|exists:fin_bank_branches,id',
            'name_ar'        => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'account_number' => 'required|string|max:255|unique:fin_banks,account_number,' . $this->bank_id,
            'iban'           => 'nullable|string|max:255',
            'swift_code'     => 'nullable|string|max:255',
            'currency'       => 'required|string|max:10',
            'balance'        => 'numeric|min:0',
            'status'         => 'required|in:active,inactive',
        ]);

        Bank::findOrFail($this->bank_id)->update($data);
        session()->flash('success', 'تم تعديل بيانات البنك بنجاح');
        $this->resetFields();
    }

    public function delete($id)
    {
        Bank::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف البنك بنجاح');
    }

    public function render()
    {
        return view('livewire.finance.banks.banks', [
            'banks' => Bank::with('branch')->paginate(10),
            'branches' => BankBranch::all(),
        ])->extends('layouts.master')->section('content');
    }
}