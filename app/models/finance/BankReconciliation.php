<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankReconciliation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_bank_reconciliations';

    protected $fillable = [
        'bank_id',
        'account_id',
        'period',
        'opening_balance',
        'closing_balance',
        'difference',
        'reconciled_by',
        'reconciliation_date',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
    }

    public function reconciler()
    {
        return $this->belongsTo(\App\Models\User::class, 'reconciled_by');
    }
}