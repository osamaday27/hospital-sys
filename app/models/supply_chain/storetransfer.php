<?php
// app/models/supply_chain/StoreTransfer.php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class storetransfer extends Model
{
    protected $table = 'store_transfers';

    protected $fillable = [
        'transfer_number',
        'store_from_id',
        'store_to_id',
        'transfer_date',
        'product_id',
        'units_id',
        'quantity',
        'batch_no',
        'expiry_date',
        'user_id',
        'notes',
    ];
}