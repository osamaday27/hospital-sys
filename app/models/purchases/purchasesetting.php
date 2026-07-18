<?php

namespace App\Models\purchases;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class purchasesetting extends Model
{
    use SoftDeletes;
    protected $table = 'purchase_settings';

    protected $fillable = [
        'has_max_limit',
        'max_limit_value',
        'use_last_supplier_price',
        'use_sale_price',
    ];
}
