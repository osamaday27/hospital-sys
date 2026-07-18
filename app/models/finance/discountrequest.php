<?php
// app/Models/finance/discountrequest.php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\finance\invoices;
use App\Models\User;

class discountrequest extends Model
{
    use SoftDeletes;

    protected $table = 'fin_discount_requests';

    protected $fillable = [
        'invoice_id',
        'user_id',
        'amount',
        'reason',
        'status',
        'admin_note',
        'action_by',
        'action_date',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoices::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}