<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class specialneedsassessment extends Model
{
    protected $table = 'special_needs_assessments';

    protected $fillable = [
        'patient_id', 'visit_id', 'reservation_id', 'user_add',
        'no_exercise','overweight','hates_school','family_issues',
        'normal_elderly','cannot_count','doesnt_know_facility',
        'physical_disability_yes','physical_disability_details',
        'weak_immunity_yes','weak_immunity_reason','chronic_pain_yes',
        'notify_doctor_special_yes','notify_doctor_special_no'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}