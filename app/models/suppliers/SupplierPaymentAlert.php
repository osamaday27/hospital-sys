<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;

class SupplierPaymentAlert extends Model
{
    protected $fillable = [
        'installment_id', 'type', 'seen', 'alert_date'
    ];

    public function installment()
    {
        return $this->belongsTo(SupplierPaymentInstallment::class, 'installment_id');
    }
}