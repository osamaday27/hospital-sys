<?php
// app/Models/Purchases/PriceQuotation.php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuotation extends Model
{
    use SoftDeletes;

    protected $table = 'price_quotations';

    protected $fillable = [
        'requisition_id',
        'quotation_number',
        'start_date',
        'end_date',
        'supplier_id',
        'delivery_period',
        'delivery_location',
        'payment_method',
        'total_tax',
        'total_offer',
        'final_price_including_tax',
        'payment_terms',
        'notes',
        'created_by',
    ];

    public function requisition()
    {
        return $this->belongsTo(\App\Models\Purchases\PurchaseRequisition::class, 'requisition_id');
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Suppliers\SuppliersData::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PriceQuotationItem::class, 'price_quotation_id');
    }
}
