<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_bank_accounts';

    protected $fillable = ['bank_id', 'account_name', 'account_number', 'currency', 'opening_balance', 'current_balance', 'type', 'status','gl_account_id'];

    // علاقة مع البنك
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
    public function glAccount()
    {
        return $this->belongsTo(glaccount::class, 'gl_account_id');
    }
}