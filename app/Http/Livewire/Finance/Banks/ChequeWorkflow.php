<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankCheque;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChequeWorkflow extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $bank_account_id, $amount, $issue_date, $due_date, $payee, $notes;
    public $search = '';

    protected $rules = [
        'bank_account_id' => 'required|exists:fin_bank_accounts,id',
        'amount'          => 'required|numeric|min:0',
        'issue_date'      => 'required|date',
        'due_date'        => 'nullable|date|after_or_equal:issue_date',
        'payee'           => 'required|string|max:255',
        'notes'           => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function issue()
    {
        if (!auth()->user()->hasBankPermission('can_issue_cheque')) {
            abort(403, __('bank_trans.no_permission'));
        }

        $data = $this->validate();
        $data['status'] = 'issued';
        $data['created_by'] = Auth::id();

        $cheque = BankCheque::create($data);

        $this->logStatus($cheque->id, null, 'issued');
        session()->flash('success', __('bank_trans.cheque_issued_successfully'));
        $this->reset(['bank_account_id','amount','issue_date','due_date','payee','notes']);
    }

    public function changeStatus($id, $newStatus)
    {
        $cheque = BankCheque::findOrFail($id);

        $workflow = [
            'issued'     => ['approved','cancelled'],
            'approved'   => ['cleared','cancelled'],
            'cleared'    => ['reconciled','bounced'],
            'bounced'    => ['cancelled'],
        ];

        $allowed = $workflow[$cheque->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            session()->flash('error', __('bank_trans.invalid_transition'));
            return;
        }

        $permissionsMap = [
            'issued'     => 'can_issue_cheque',
            'approved'   => 'can_approve_cheque',
            'cleared'    => 'can_clear_cheque',
            'reconciled' => 'can_reconcile_cheque',
            'bounced'    => 'can_bounce_cheque',
            'cancelled'  => 'can_cancel_cheque',
        ];

        $requiredPermission = $permissionsMap[$newStatus] ?? null;

        if ($requiredPermission && !auth()->user()->hasBankPermission($requiredPermission)) {
            abort(403, __('bank_trans.no_permission'));
        }

        $oldStatus = $cheque->status;
        $cheque->update(['status' => $newStatus]);

        $this->logStatus($cheque->id, $oldStatus, $newStatus);

        session()->flash('success', __('bank_trans.cheque_status_updated'));
    }

    private function logStatus($chequeId, $old, $new)
    {
        DB::table('fin_bank_cheque_logs')->insert([
            'cheque_id'  => $chequeId,
            'user_id'    => Auth::id(),
            'old_status' => $old,
            'new_status' => $new,
            'created_at' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.finance.banks.cheque-workflow', [
            'cheques' => BankCheque::where('payee', 'like', "%{$this->search}%")
                                   ->orWhere('cheque_number', 'like', "%{$this->search}%")
                                   ->orWhere('status', 'like', "%{$this->search}%")
                                   ->paginate(10),
        ]);
    }
}