<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankBranch;

class BankBranches extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $branch_id, $name_ar, $name_en, $address, $phone, $email, $status = 'active';
    public $isEdit = false;

    protected $rules = [
        'name_ar' => 'required|string|max:255',
        'name_en' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'phone'   => 'nullable|string|max:50',
        'email'   => 'nullable|email|max:255',
        'status'  => 'required|in:active,inactive',
    ];

    public function resetFields()
    {
        $this->branch_id = null;
        $this->name_ar = '';
        $this->name_en = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->status = 'active';
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        BankBranch::create($data);
        session()->flash('success', 'تمت إضافة الفرع بنجاح');
        $this->resetFields();
    }

    public function edit($id)
    {
        $branch = BankBranch::findOrFail($id);
        $this->fill($branch->toArray());
        $this->branch_id = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate();
        BankBranch::findOrFail($this->branch_id)->update($data);
        session()->flash('success', 'تم تعديل بيانات الفرع بنجاح');
        $this->resetFields();
    }

    public function delete($id)
    {
        BankBranch::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف الفرع بنجاح');
    }

    public function render()
    {
        return view('livewire.finance.banks.bank-branches', [
            'branches' => BankBranch::paginate(10),
        ])->extends('layouts.master')->section('content');
    }
}