<?php

namespace App\Http\Livewire\Finance\Program;

use Livewire\Component;
use App\Models\finance\glaccount;
use App\Models\finance\glentry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Trialbalance extends Component
{
    public $from_date,
    $to_date;
    public $balances = [];

    public function mount()
    {
        $this->from_date = now()->startOfMonth()->toDateString();
        $this->to_date   = now()->endOfMonth()->toDateString();
        $this->loadData();
    }

    public function updated($field)
    {
        if (in_array($field, ['from_date','to_date'])) {
            $this->loadData();
        }
    }

    public function loadData()
    {
        $accounts = glaccount::where('is_group', 0)->with(['debitEntries','creditEntries'])->get();

        $data = [];

        foreach ($accounts as $account) {
            $debit = glentry::where('debit_account_id', $account->id)
                        ->when($this->from_date && $this->to_date, function ($q) {
                            $q->whereBetween('date', [$this->from_date, $this->to_date]);
                        })
                        ->sum('amount');

            $credit = glentry::where('credit_account_id', $account->id)
                        ->when($this->from_date && $this->to_date, function ($q) {
                            $q->whereBetween('date', [$this->from_date, $this->to_date]);
                        })
                        ->sum('amount');

            $balance = $debit - $credit;
            $balanceType = $balance >= 0 ? 'مدين' : 'دائن';

            $data[] = [
                'code'         => $account->code,
                'name'         => $account->name_ar,
                'type'         => $account->type_translated,
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
        return view('livewire.finance.program.trialbalance', [
            'balances' => $this->balances,
        ]);
    }
}