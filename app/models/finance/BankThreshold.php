<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankThreshold extends Model
{
    use HasFactory;

    protected $table = 'fin_bank_thresholds';

    protected $fillable = [
        'min_amount',
        'max_amount',
        'required_role',
        'status',
    ];
}