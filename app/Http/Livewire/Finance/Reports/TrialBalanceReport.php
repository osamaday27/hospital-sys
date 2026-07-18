<?php

namespace App\Http\Livewire\Finance\Reports;

use Livewire\Component;
use App\Models\finance\glaccount;
use App\Models\finance\glentry;
use Carbon\Carbon;

class TrialBalanceReport extends Component
{
    public $from_date;
    public $to_date;
    public $balances = [];
    public $statusFilter = '';
    public $cost_center = '';

    public function mount()
    {
        $this->from_date = now()->startOfMonth()->toDateString();
        $this->to_date   = now()->endOfMonth()->toDateString();
        $this->loadData();
    }

    public function updated($field)
    {
        if (in_array($field, ['from_date','to_date','statusFilter','cost_center'])) {
            $this->loadData();
        }
    }

    public function loadData()
    {
        $accounts = glaccount::where('is_group',0)->with('children')->get();
        $data = [];

        foreach ($accounts as $acc) {
            $debit = glentry::where('debit_account_id',$acc->id)
                        ->when($this->from_date && $this->to_date, fn($q)=>$q->whereBetween('date',[$this->from_date,$this->to_date]))
                        ->sum('amount');

            $credit = glentry::where('credit_account_id',$acc->id)
                        ->when($this->from_date && $this->to_date, fn($q)=>$q->whereBetween('date',[$this->to_date,$this->to_date]))
                        ->sum('amount');

            $balance = $debit - $credit;
            $balanceType = $balance >= 0 ? 'مدين' : 'دائن';

            $data[] = [
                'code'         => $acc->code,
                'name'         => $acc->name_ar,
                'type'         => $acc->type_translated,
                'type_color'   => $acc->type_color,
                'debit'        => $debit,
                'credit'       => $credit,
                'balance'      => abs($balance),
                'balance_type' => $balanceType,
            ];
        }

        $this->balances = $data;
    }

    public function render()
    {
        return view('livewire.finance.reports.trial-balance-report', [
            'balances' => $this->balances
        ]);
    }
}