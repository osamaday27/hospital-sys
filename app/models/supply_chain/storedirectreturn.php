<?php
// app/models/supply_chain/storedirectreturn.php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class storedirectreturn extends Model
{
    use SoftDeletes;

    protected $table = 'store_direct_returns';

    protected $fillable = [
        'code',
        'store_transfer_id',
        'transfer_number',
        'store_from_id',
        'store_to_id',
        'transfer_date',
        'product_id',
        'units_id',
        'quantity',
        'batch_no',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'quantity'      => 'decimal:4',
    ];

    // العلاقات
    public function transfer()
    {
        return $this->belongsTo(storetransfer::class, 'store_transfer_id');
    }

    public function storeFrom()
    {
        return $this->belongsTo(storemaindata::class, 'store_from_id');
    }

    public function storeTo()
    {
        return $this->belongsTo(storemaindata::class, 'store_to_id');
    }

    public function product()
    {
        return $this->belongsTo(productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(unit::class, 'units_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\models\User::class, 'user_id');
    }
}
