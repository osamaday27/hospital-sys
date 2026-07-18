<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\ChequeBook;
use App\Models\Finance\ChequeLeaf;
use App\Models\Finance\BankAccount;
use Illuminate\Support\Facades\Auth;

class ChequeBooks extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $book_number, $bank_account_id, $start_number, $end_number, $notes;
    public $status = 'active';

    protected $rules = [
        'book_number'    => 'required|string|max:255|unique:fin_cheque_books,book_number',
        'bank_account_id'=> 'required|exists:fin_bank_accounts,id',
        'start_number'   => 'required|integer|min:1',
        'end_number'     => 'required|integer|gte:start_number',
        'notes'          => 'nullable|string',
    ];

    public function save()
    {
        $data = $this->validate();
        $data['created_by'] = Auth::id();

        $book = ChequeBook::create($data);

        // توليد أوراق الشيكات داخل الدفتر
        for ($i = $this->start_number; $i <= $this->end_number; $i++) {
            ChequeLeaf::create([
                'cheque_book_id' => $book->id,
                'cheque_number'  => $i,
                'status'         => 'available',
            ]);
        }

        session()->flash('success', __('bank_trans.cheque_book_added'));
        $this->reset(['book_number','bank_account_id','start_number','end_number','notes']);
    }

    public function changeStatus($id, $newStatus)
    {
        $book = ChequeBook::findOrFail($id);
        $book->update(['status' => $newStatus]);

        session()->flash('success', __('bank_trans.cheque_book_status_updated'));
    }

    public function render()
    {
        return view('livewire.finance.banks.cheque-books', [
            'chequeBooks'   => ChequeBook::with('bankAccount')->paginate(10),
            'bankAccounts'  => BankAccount::all(),
        ]);
    }
}