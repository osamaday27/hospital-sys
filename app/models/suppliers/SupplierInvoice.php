<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\general\branch;

class SupplierInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_invoices';

    protected $fillable = [
        'supplier_id','store_id','branch_id',
        'invoice_no','invoice_date','due_date','invoice_type',
        'subtotal','discount','tax','total_amount',
        'paid_amount','remaining_amount','status',
        'payment_method','reference_no','notes','user_id','approved_by'
    ];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Suppliers\suppliersdata::class, 'supplier_id');
    }

    public function branch()
    {
        return $this->belongsTo(branch::class, 'branch_id');
    }

    public function items()
    {
        return $this->hasMany(SupplierInvoiceItem::class, 'invoice_id');
    }

    // ✅ هنا التعديل
    public function payments()
    {
        return $this->hasMany(SupplierPaymentInstallment::class, 'supplier_invoice_id');
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'store_id');
    }
}