<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class operation_contract_pricing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'operation_contract_pricing';

    protected $fillable = [
        'contract_id',
        'surgery_id',
        'surgeon_fee',
        'assistant_fee',
        'anesthesia_fee',
        'anesthesia_type',
        'nursing_fee',
        'operation_room_fee',
        'additional_time_fee',
        'supplies_cost',
        'other_services',
        'discount',
        'vat',
        'total_price',
        'status',
        'created_by',
        'updated_by',
    ];

    // العلاقات

    public function contract()
    {
        return $this->belongsTo(contract_last_level::class, 'contract_id');
    }

    public function surgery()
    {
        return $this->belongsTo(service::class, 'surgery_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}