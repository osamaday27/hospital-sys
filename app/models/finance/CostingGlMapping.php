<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostingGlMapping extends Model
{
    use SoftDeletes;

    protected $table = 'costing_gl_mappings';

    protected $fillable = [
        'line_type','item_type','cost_center_id',
        'debit_account','credit_account','description',
        'active','user_id',
    ];

    public static function lineTypes(): array { return ['direct','overhead']; }
    public static function itemTypes(): array { return ['supply','drug','resource','misc','overhead']; }
}