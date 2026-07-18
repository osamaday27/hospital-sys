<?php
// app/Models/supply_chain/storeItemRequestDetail.php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class storeItemRequestDetail extends Model
{
    protected $fillable = [
        'request_id',
        'product_id',
        'unit_id',
        'quantity',
        'created_by',
    ];
    protected $table = 'store_item_request_details_stroe';

    public function request()
    {
        return $this->belongsTo(storeItemRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductData::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}