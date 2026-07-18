<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use App\Models\Finance\GeneralSetting;
use App\Models\Finance\Currency;

class DefaultCurrency extends Component
{
    public $default_currency_id;

    public function mount()
    {
        $settings = GeneralSetting::first();
        $this->default_currency_id = $settings?->default_currency_id;
    }

    public function save()
    {
        $this->validate([
            'default_currency_id' => 'required|exists:fin_currencies,id',
        ]);

        $settings = GeneralSetting::firstOrCreate([]);
        $settings->update(['default_currency_id' => $this->default_currency_id]);

        session()->flash('success', __('bank_trans.default_currency_saved'));
    }

    public function render()
    {
        return view('livewire.finance.Banks.default-currency', [
            'currencies' => Currency::all(),
        ])->extends('layouts.master')->section('content');
    }
}