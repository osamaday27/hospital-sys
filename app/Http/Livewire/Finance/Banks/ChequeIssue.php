<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use App\Models\Finance\BankCheque;
use App\Models\Finance\BankAccount;
use App\Models\Finance\ChequeBook;
use App\Models\Finance\ChequeLeaf;
use Illuminate\Support\Facades\Auth;

class ChequeIssue extends Component
{
    public $bank_account_id, $cheque_book_id, $cheque_leaf_id;
    public $amount, $issue_date, $due_date, $payee, $notes;

    protected $rules = [
        'bank_account_id' => 'required|exists:fin_bank_accounts,id',
        'cheque_book_id'  => 'required|exists:fin_cheque_books,id',
        'cheque_leaf_id'  => 'required|exists:fin_cheque_leaves,id',
        'amount'          => 'required|numeric|min:0',
        'issue_date'      => 'required|date',
        'due_date'        => 'nullable|date|after_or_equal:issue_date',
        'payee'           => 'nullable|string|max:255',
        'notes'           => 'nullable|string',
    ];

    public function save()
    {
        $data = $this->validate();

        $leaf = ChequeLeaf::where('id', $this->cheque_leaf_id)
            ->where('status', 'available')
            ->first();

        if (!$leaf) {
            session()->flash('error', __('bank_trans.no_available_cheque'));
            return;
        }

        // إنشاء الشيك وربطه بالورقة
        $cheque = BankCheque::create([
            'bank_account_id' => $this->bank_account_id,
            'cheque_number'   => $leaf->cheque_number,
            'amount'          => $this->amount,
            'issue_date'      => $this->issue_date,
            'due_date'        => $this->due_date,
            'payee'           => $this->payee,
            'notes'           => $this->notes,
            'status'          => 'issued',
            'created_by'      => Auth::id(),
        ]);

        $leaf->update([
            'status' => 'issued',
            'linked_cheque_id' => $cheque->id,
        ]);

        session()->flash('success', __('bank_trans.cheque_added'));
        $this->reset(['bank_account_id','cheque_book_id','cheque_leaf_id','amount','issue_date','due_date','payee','notes']);
    }

    public function render()
    {
        return view('livewire.finance.banks.cheque-issue', [
            'bankAccounts' => BankAccount::all(),
            'chequeBooks'  => $this->bank_account_id 
                ? ChequeBook::where('bank_account_id',$this->bank_account_id)->where('status','active')->get()
                : [],
            'chequeLeaves' => $this->cheque_book_id
                ? ChequeLeaf::where('cheque_book_id',$this->cheque_book_id)->where('status','available')->get()
                : [],
        ]);
    }
}