<?php
// app/Models/finance/InvoiceCancellationRequest.php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class invoicecancellationrequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_invoice_cancellation_requests';

    protected $fillable = [
        'invoice_id',
        'cancellation_reason',
        'cancellation_notes',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoices::class, 'invoice_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function reason()
{
    return $this->belongsTo(cancellationreason::class, 'cancellation_reason');
}

}