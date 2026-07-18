<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class lockerdailybalance extends Model
{
    protected $table = 'fin_b_locker_daily_balances';

    protected $fillable = [
        'locker_id',
        'date',
        'opening_balance',
        'closing_balance',
        'actual_cash',
        'notes',
        'created_by',
    ];

    public function locker()
    {
        return $this->belongsTo(Locker::class, 'locker_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}