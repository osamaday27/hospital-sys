<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class adm_b_surgery_supply extends Model
{
    protected $table = 'adm_b_surgery_supplies';
    use SoftDeletes;
    protected $fillable = [
        'surgery_booking_id',
        'supply_id',
        'unit_id',
        'quantity',
        'available_stock',
        'notes',
    ];
    protected $dates = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'surgery_booking_id');
    }

    public function supply()
    {
        return $this->belongsTo(\App\models\supply_chain\productdata::class, 'supply_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\models\supply_chain\unit::class, 'unit_id');
    }
}