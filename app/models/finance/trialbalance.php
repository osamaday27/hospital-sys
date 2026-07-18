<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class trialbalance extends Model
{
    use SoftDeletes;

    protected $table = 'fin_b_trial_balances';

    protected $fillable = [
        'gl_account_id',
        'from_date',
        'to_date',
        'debit',
        'credit',
        'balance',
        'balance_type',
        'created_by'
    ];

    public function account()
    {
        return $this->belongsTo(glaccount::class, 'gl_account_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}