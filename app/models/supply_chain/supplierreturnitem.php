<?php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierReturnItem extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_return_items';

    protected $fillable = [
        'supplier_return_id',
        'product_id',
        'units_id',
        'quantity',
        'unit_price',
        'tax_value',
        'unit_price_with_tax',
        'batch_no',
        'supplier_invoice_id',
        'notes',
    ];

    protected $casts = [
        'quantity'             => 'decimal:4',
        'unit_price'           => 'decimal:4',
        'tax_value'            => 'decimal:4',
        'unit_price_with_tax'  => 'decimal:4',
    ];

    // العلاقات
    public function supplierReturn()
    {
        return $this->belongsTo(SupplierReturn::class, 'supplier_return_id');
    }

    public function product()
    {
        return $this->belongsTo(productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(unit::class, 'units_id');
    }

    public function supplierInvoice()
    {
        return $this->belongsTo(\App\Models\Suppliers\SupplierInvoice::class, 'supplier_invoice_id');
    }
}
