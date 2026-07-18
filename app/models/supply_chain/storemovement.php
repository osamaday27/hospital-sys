<?php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\suppliers\suppliersdata;
use App\models\supply_chain\productdata;
use App\models\supply_chain\storemaindata;
use App\models\supply_chain\unit;
use App\models\supply_chain\productunit;
use App\models\supply_chain\productstorebalance;


class storemovement extends Model
{
    use SoftDeletes;

    protected $table = 'store_movements';

    protected $fillable = [
        'product_id',
        'store_id',
        'units_id',
        'supplier_id',
        'quantity',
        'product_store_balance',
        'batch_no',
        'expiry_date',
        'movement_type',
        'movement_number',
        'reference_no',
        'movement_date',
        'cost_price',
        'sale_price',
        'notes',
        'user_id',
    ];

    protected $dates = ['movement_date', 'expiry_date'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'product_store_balance' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];
public function product()
{
    return $this->belongsTo(productdata::class, 'product_id');
}

public function store()
{
    return $this->belongsTo(storemaindata::class, 'store_id');
}

public function unit()
{
    return $this->belongsTo(unit::class, 'units_id');
}

public function supplier()
{
    return $this->belongsTo(suppliersdata::class, 'supplier_id');
}

public function user()
{
    return $this->belongsTo(\App\models\User::class, 'user_id');
}

}
