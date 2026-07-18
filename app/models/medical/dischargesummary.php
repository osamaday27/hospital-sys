<?php

namespace App\Models\Medical;


use Illuminate\Database\Eloquent\Model;

class dischargesummary extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'booking_id',
        'final_diagnosis',
        'operation_done',
        'significant_history',
        'case_progression',
        'patient_condition',
        'diet_instruction',
        'diet_other',
        'implanted_device',
        'implanted_device_instructions',
        'follow_up_activity',
        'follow_up_visit',
        'emergency_instructions',
        'notes',
        'copy_given',
        'doctor_name',
        'doctor_signature',
        'report_date',
        'report_time',
        'created_by',
    ];
    protected $table = 'discharge_summaries';
    public function medications()
    {
        return $this->hasMany(\App\Models\Medical\DischargeSummaryMedication::class, 'discharge_summary_id');
    }
    

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function booking()
    {
        return $this->belongsTo(SurgeryBooking::class, 'booking_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

  
}