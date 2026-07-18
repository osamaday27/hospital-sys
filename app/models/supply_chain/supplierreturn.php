<?php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierReturn extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_returns';

    protected $fillable = [
        'code',
        'store_id',
        'supplier_id',
        'reason',
        'subtotal',
        'tax_total',
        'grand_total',
        'document_date',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:4',
        'tax_total'    => 'decimal:4',
        'grand_total'  => 'decimal:4',
        'document_date'=> 'date',
    ];

    // العلاقات
    public function store()
    {
        return $this->belongsTo(storemaindata::class, 'store_id');
    }

    public function supplier()
    {
        // جدول الموردين في مساحة App\Models\Suppliers
        return $this->belongsTo(\App\Models\Suppliers\suppliersdata::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(SupplierReturnItem::class, 'supplier_return_id');
    }
}
