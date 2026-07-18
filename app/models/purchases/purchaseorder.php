<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'quotation_id',
        'requisition_id',
        'order_number',
        'order_date',
        'currency',
        'delivery_period',
        'supplier_id',
        'priority',
        'delivery_location',
        'payment_method',
        'payment_terms',
        'notes',
        'subtotal',
        'total_tax',
        'total_amount_including_tax',
        'created_by',
    ];

    protected $dates = [
        'order_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // علاقات
    public function quotation()
    {
        return $this->belongsTo(PriceQuotation::class, 'quotation_id');
    }

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
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');
    }    public function committees()
    {
        return $this->hasMany(PurchaseOrderCommittee::class, 'purchase_order_id');
    }
}
