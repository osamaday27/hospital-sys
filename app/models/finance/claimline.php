<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksChanges;

class claimline extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $table = 'fin_claim_lines';

    protected $fillable = [
        'claim_id',
        'service_id',
        'quantity',
        'unit_price',
        'gross',
        'discount',
        'patient_share',
        'contract_share',
        'net_payable',
        'notes',
        'user_id',
    ];

    public function claim()
    {
        return $this->belongsTo(\App\Models\Finance\claim::class, 'claim_id');
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Finance\service::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}