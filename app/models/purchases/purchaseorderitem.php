<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_order_items';

    protected $fillable = [
        'order_id',
        'item_id',
        'unit_id',
        'requested_qty',
        'pending_qty',
        'current_balance',
        'other_stores_balance',
        'last_price',
        'tax_amount',
        'notes',
    ];

    // علاقات
    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Supply_Chain\ProductData::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\Supply_Chain\ProductUnit::class, 'unit_id');
    }
}
