<?php
// app/Models/Medical/SocialEconomicAssessment.php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class socialeconomicassessment extends Model
{
    use HasFactory;

    protected $table = 'social_economic_assessments';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'reservation_id',
        'user_add',
        'living_alone',
        'living_with_family',
        'living_with_others',
        'living_other_details',
        'special_needs_none',
        'hearing',
        'speech',
        'language_barrier',
        'other_needs_details',
        'single',
        'married',
        'has_children',
        'number_of_children',
        'smoking_no',
        'smoking_yes',
        'smoking_previous',
        'cigarettes_per_day',
        'no_psychological_issues',
        'anxiety',
        'anger',
        'depression',
        'can_afford',
        'insured',
        'cannot_afford',
        'no_violence',
        'violence_present',
        'violence_details',
        'not_working',
        'working',
        'occupation',
        'notify_doctor_yes',
        'notify_doctor_no',
    ];

    // علاقة بالمستخدم الذي أضاف السجل
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }

    // (اختياري) علاقات إضافية:
    public function patient()
    {
        return $this->belongsTo(\App\Models\Medical\Patient::class, 'patient_id');
    }

    public function visit()
    {
        return $this->belongsTo(\App\Models\Medical\PatientVisit::class, 'visit_id');
    }

    public function reservation()
    {
        return $this->belongsTo(\App\Models\Administrative\Reservation::class, 'reservation_id');
    }
}