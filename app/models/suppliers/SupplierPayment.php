<?php

namespace App\Models\suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use App\Models\Finance\slocker;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\User;

class SupplierPayment extends Model
{
    use SoftDeletes, TracksChanges;

    protected $table = 'supplier_invoice_payments';

    protected $fillable = [
        'supplier_invoice_id',
        'locker_id',
        'amount',
        'payment_method',
        'reference_no',
        'bank_name',
        'bank_details',
        'notes',
        'created_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function locker()
    {
        return $this->belongsTo(slocker::class, 'locker_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}