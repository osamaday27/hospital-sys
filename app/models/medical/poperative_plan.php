<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class poperative_plan extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'booking_id',
        'surgery_procedure',
        'transfer_to',
        'positioning',
        'positioning_other',
        'mobility',
        'mobility_other',
        'feeding',
        'feeding_other',
        'required_monitoring',
        'wound',
        'urine_output',
        'instructions_post_anesthesia',
        'investigations',
        'general_instructions',
        'follow_up',
        'doctor_name',
        'date',
        'time',
        'created_by',
    ];
    protected $table = 'post_operative_plans';

    public function medications()
    {
        return $this->hasMany(PostOperativeMedication::class, 'plan_id');
    }

    public function fluids()
    {
        return $this->hasMany(PostOperativeFluid::class, 'plan_id');
    }
}