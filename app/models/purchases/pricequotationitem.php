<?php
// app/Models/Purchases/PriceQuotationItem.php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuotationItem extends Model
{
    use SoftDeletes;

    protected $table = 'price_quotation_items';

    protected $fillable = [
        'price_quotation_id',
        'item_id',
        'unit_id',
        'quantity',
        'price',
        'tax_amount',
        'notes',
    ];

    public function quotation()
    {
        return $this->belongsTo(PriceQuotation::class, 'price_quotation_id');
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
