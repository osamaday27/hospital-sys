<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class damageditemrequestitem extends Model
{
    protected $table = 'damaged_item_request_items';

    protected $fillable = [
        'request_id',
        'product_id',
        'unit_id',
        'quantity',
        'notes',
    ];

    public function request()
    {
        return $this->belongsTo(damageditemrequest::class, 'request_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\supply_chain\productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\supply_chain\unit::class, 'unit_id');
    }
}