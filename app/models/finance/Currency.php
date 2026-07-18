<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'fin_currencies';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'exchange_rate',
        'status',
    ];
}