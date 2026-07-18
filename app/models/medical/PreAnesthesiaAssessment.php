<?php

namespace App\Models\medical;

use Illuminate\Database\Eloquent\Model;

class PreAnesthesiaAssessment extends Model
{
    protected $table = 'pre_anesthesia_assessments';

    protected $fillable = [
        'surgery_booking_id',
        'weight', 'height', 'age', 'gender',
        'diagnosis', 'surgeon_unit', 'proposed_operation',
        'history', 'drug_therapy', 'physical_exam', 'investigations',
        'investigations_comments', 'lab_works',
        'asa', 'consent', 'fit_for_operation', 'fit_for_operation_specify',
        'prepare_fresh_blood', 'prepare_rbc', 'prepare_ffp', 'prepare_platelets',
        'please_give_1', 'please_give_2', 'anesthesia_plan',
        'physician_signature', 'form_date', 'form_time', 'user_id'
    ];

    protected $casts = [
        'history' => 'array',
        'drug_therapy' => 'array',
        'physical_exam' => 'array',
        'investigations' => 'array',
        'lab_works' => 'array',
    ];
}