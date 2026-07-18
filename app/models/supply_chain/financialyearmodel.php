<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class financialyearmodel extends Model
{
    use SoftDeletes;
    protected $table = 'financial_years';

    protected $fillable = [
        'year_code',
        'periods_count',
        'start_date',
        'end_date',
    ];
}
