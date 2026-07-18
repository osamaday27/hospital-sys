<?php

namespace App\Http\Livewire\Finance\Banks;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankThreshold;

class BankThresholds extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $threshold_id, $min_amount = 0, $max_amount, $required_role, $status = 'active';
    public $isEdit = false;

    protected $rules = [
        'min_amount'    => 'required|numeric|min:0',
        'max_amount'    => 'nullable|numeric|min:0',
        'required_role' => 'required|string|max:255',
        'status'        => 'required|in:active,inactive',
    ];

    public function resetFields()
    {
        $this->threshold_id = null;
        $this->min_amount = 0;
        $this->max_amount = null;
        $this->required_role = '';
        $this->status = 'active';
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        BankThreshold::create($data);
        session()->flash('success', __('bank_trans.threshold_added'));
        $this->resetFields();
    }

    public function edit($id)
    {
        $th = BankThreshold::findOrFail($id);
        $this->fill($th->toArray());
        $this->threshold_id = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate();
        BankThreshold::findOrFail($this->threshold_id)->update($data);
        session()->flash('success', __('bank_trans.threshold_updated'));
        $this->resetFields();
    }

    public function delete($id)
    {
        BankThreshold::findOrFail($id)->delete();
        session()->flash('success', __('bank_trans.threshold_deleted'));
    }

    public function render()
    {
        return view('livewire.finance.Banks.bank-thresholds', [
            'thresholds' => BankThreshold::paginate(10),
        ])->extends('layouts.master')->section('content');
    }
}