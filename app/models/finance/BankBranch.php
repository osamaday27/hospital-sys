<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankBranch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_bank_branches';

    protected $fillable = [
        'name_ar',
        'name_en',
        'address',
        'phone',
        'email',
        'status',
    ];

    // علاقة مع البنوك
    public function banks()
    {
        return $this->hasMany(Bank::class, 'branch_id');
    }
}