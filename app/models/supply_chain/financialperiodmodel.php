<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class financialperiodmodel extends Model
{
    use SoftDeletes;
    protected $table = 'financial_periods';

    protected $fillable = [
        'name_ar',
        'name_en',
        'start_date',
        'end_date',
    ];
}
