<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankCheque extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_bank_cheques';

    protected $fillable = [
        'bank_account_id',
        'cheque_number',
        'amount',
        'issue_date',
        'due_date',
        'payee',
        'status',
        'notes',
        'created_by',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}