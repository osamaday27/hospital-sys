<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class operation_invoice_detail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'operation_invoice_details';

    protected $fillable = [
        'invoice_id',
        'reservation_id',
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
        'subtotal',
        'discount',
        'vat_rate',
        'vat_amount',
        'total_price',
        'patient_value',
        'contract_value',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'invoice_id'      => 'integer',
        'reservation_id'  => 'integer',
        'surgery_id'      => 'integer',
        'surgeon_fee'     => 'float',
        'assistant_fee'   => 'float',
        'anesthesia_fee'  => 'float',
        'nursing_fee'     => 'float',
        'operation_room_fee' => 'float',
        'additional_time_fee'=> 'float',
        'supplies_cost'   => 'float',
        'other_services'  => 'float',
        'subtotal'        => 'float',
        'discount'        => 'float',
        'vat_rate'        => 'float',
        'vat_amount'      => 'float',
        'total_price'     => 'float',
        'patient_value'   => 'float',
        'contract_value'  => 'float',
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\models\finance\invoices::class, 'invoice_id');
    }

    public function surgery()
    {
        return $this->belongsTo(\App\models\finance\service::class, 'surgery_id');
    }
}