<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;

class lockertransaction extends Model
{
    protected $table = 'fin_b_locker_transactions';

    protected $fillable = [
        'locker_id',
        'transaction_type',
        'amount',
        'direction',
        'reference_type',
        'reference_id',
        'gl_account_id',
        'notes',
        'created_by',
         'bank_id','bank_machine_id','reference_no',
    ];

    public function locker()
    {
        return $this->belongsTo(Locker::class, 'locker_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }

    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    } public function bank()
    {
        return $this->belongsTo(\App\Models\Finance\Bank::class, 'bank_id');
    }

    public function machine()
    {
        return $this->belongsTo(\App\Models\Finance\BankMachine::class, 'bank_machine_id');
    }
}