<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceItem extends Model
{
    protected $table = 'supplier_invoice_items';

    protected $fillable = [
        'invoice_id',
        'item_type',
        'product_id',
        'product_unit_id',
        'service_id',
        'description',
        'qty',
        'unit_price',
        'discount',
        'tax',
        'total',
    ];

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\supply_chain\productdata::class, 'product_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(\App\Models\supply_chain\unit::class, 'product_unit_id');
    }

    public function service()
    {
        return $this->belongsTo(SupplierService::class, 'service_id');
    }
    public function unit() {
    return $this->belongsTo(\App\Models\supply_chain\unit::class, 'product_unit_id');
}

}
