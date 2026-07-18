<?php
// app/Models/medical/AnesthesiaPlan.php

namespace App\Models\medical;

use Illuminate\Database\Eloquent\Model;

class AnaesthesiaPlan extends Model
{
    protected $table = 'm_d_anaesthesia_plans';

    protected $fillable = [
        'surgery_booking_id',
        'npo_hours',
        'sedation_score',
        'pending_investigations',
        'anaesthesia_type',
        'delivery_method',
        'anaesthetic_name',
        'fluid_management',
        'blood_booking',
        'premedication_none',
        'premedication',
        'special_monitoring',
        'post_outcome',
        'discharge_to',
        'post_analgesia_plan',
        'doctor_name',
        'assistant_name',
        'form_date',
        'form_time',
    ];

    protected $casts = [
        'anaesthesia_type' => 'array',
        'blood_booking' => 'array',
        'premedication' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(\App\Models\administrative\adm_b_surgery_bookings::class, 'surgery_booking_id');
    }
}