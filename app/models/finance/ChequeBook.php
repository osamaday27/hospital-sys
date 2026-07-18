<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChequeBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_cheque_books';

    protected $fillable = [
        'bank_account_id',
        'book_number',
        'start_number',
        'end_number',
        'current_number',
        'status',
        'notes',
        'created_by',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function leaves()
    {
        return $this->hasMany(ChequeLeaf::class, 'cheque_book_id');
    }
}