<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_banks';

    protected $fillable = [
        'branch_id',
        'name_ar',
        'name_en',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'balance',
        'status',
    ];

    // علاقة مع الفرع
    public function branch()
    {
        return $this->belongsTo(BankBranch::class, 'branch_id');
    }

    // علاقة مع حسابات البنك
    public function accounts()
    {
        return $this->hasMany(BankAccount::class, 'bank_id');
    }
}