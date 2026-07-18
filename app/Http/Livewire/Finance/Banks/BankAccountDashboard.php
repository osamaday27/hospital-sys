<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankAccount;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\BankCheque;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BankAccountDashboard extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $accounts = [];
    public $account_id;
    public $from_date, $to_date;

    public function mount($accountId = null)
    {
        $this->accounts   = BankAccount::all();
        $this->account_id = $accountId ?? $this->accounts->first()?->id;
        $this->from_date  = Carbon::now()->startOfMonth()->toDateString();
        $this->to_date    = Carbon::now()->endOfMonth()->toDateString();
    }

    public function render()
    {
        $account = BankAccount::find($this->account_id);

        $transactions = BankTransaction::where('account_id', $this->account_id)
            ->when($this->from_date, fn($q) => $q->whereDate('transaction_date', '>=', $this->from_date))
            ->when($this->to_date, fn($q) => $q->whereDate('transaction_date', '<=', $this->to_date))
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $cheques = BankCheque::where('bank_account_id', $this->account_id)
            ->orderBy('issue_date', 'desc')
            ->paginate(10, ['*'], 'chequesPage');

        $summary = [
            'total_deposits'  => BankTransaction::where('account_id', $this->account_id)->where('type', 'deposit')->sum('amount'),
            'total_withdraws' => BankTransaction::where('account_id', $this->account_id)->where('type', 'withdrawal')->sum('amount'),
            'total_transfers' => BankTransaction::where('account_id', $this->account_id)->where('type', 'transfer')->sum('amount'),
            'cheques_count'   => $cheques->total(),
            'balance'         => BankTransaction::where('account_id', $this->account_id)
                                ->sum(DB::raw("CASE WHEN type IN ('deposit','transfer') THEN amount ELSE -amount END")),
        ];

        return view('livewire.finance.banks.bank-account-dashboard', [
            'accounts'     => $this->accounts,
            'account'      => $account,
            'transactions' => $transactions,
            'cheques'      => $cheques,
            'summary'      => $summary,
        ]);
    }
}