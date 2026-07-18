<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Currency;

class Currencies extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $currency_id, $code, $name_ar, $name_en, $exchange_rate = 1, $status = 'active';
    public $isEdit = false;

    protected $rules = [
        'code'          => 'required|string|max:10|unique:fin_currencies,code',
        'name_ar'       => 'required|string|max:255',
        'name_en'       => 'nullable|string|max:255',
        'exchange_rate' => 'numeric|min:0',
        'status'        => 'required|in:active,inactive',
    ];

    public function resetFields()
    {
        $this->currency_id = null;
        $this->code = '';
        $this->name_ar = '';
        $this->name_en = '';
        $this->exchange_rate = 1;
        $this->status = 'active';
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        Currency::create($data);
        session()->flash('success', __('bank_trans.currency_added'));
        $this->resetFields();
    }

    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        $this->fill($currency->toArray());
        $this->currency_id = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate([
            'code'          => 'required|string|max:10|unique:fin_currencies,code,' . $this->currency_id,
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'exchange_rate' => 'numeric|min:0',
            'status'        => 'required|in:active,inactive',
        ]);

        Currency::findOrFail($this->currency_id)->update($data);
        session()->flash('success', __('bank_trans.currency_updated'));
        $this->resetFields();
    }

    public function delete($id)
    {
        Currency::findOrFail($id)->delete();
        session()->flash('success', __('bank_trans.currency_deleted'));
    }

    public function render()
    {
        return view('livewire.finance.banks.currencies', [
            'currencies' => Currency::paginate(10),
        ])->extends('layouts.master')->section('content');
    }
}