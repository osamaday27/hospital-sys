<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksChanges;

class claim extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $table = 'fin_claims';

    protected $fillable = [
        'invoice_id',
        'patient_id',
        'contract_id',
        'status',
        'period_from',   // ✅ جديد
        'period_to',     // ✅ جديد
        'submission_date',
        'approval_date',
        'payment_date',
        'total_claim',
        'total_patient_share',
        'total_contract_share',
        'total_discount',
        'notes',
        'user_id',
        'account_id',
    ];

    // (اختياري) كاست للتواريخ
    protected $casts = [
        'period_from'    => 'date',
        'period_to'      => 'date',
        'submission_date'=> 'date',
        'approval_date'  => 'date',
        'payment_date'   => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Finance\invoices::class, 'invoice_id');
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\Administrative\medical_record::class, 'patient_id');
    }

    public function contract()
    {
        return $this->belongsTo(\App\Models\Finance\contract_last_level::class, 'contract_id');
    }

    public function lines()
    {
        return $this->hasMany(\App\Models\Finance\claimline::class, 'claim_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /** علاقة الفواتير المرتبطة بالمطالبة (invoices.claim_id = claims.id) */
    public function invoices()
    {
        return $this->hasMany(\App\Models\Finance\invoices::class, 'claim_id');
    }
}