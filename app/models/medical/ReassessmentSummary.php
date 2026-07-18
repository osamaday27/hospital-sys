<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class ReassessmentSummary extends Model
{
    protected $table = 'reassessment_summaries';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'self_care','instructions_clear','doctor_alert',
        'social_exam','social_exam_details',
        'special_needs','special_needs_details',
        'infectious_disease','infectious_disease_details',
        'pain_check','pain_level',
        'allergy_check','allergy_details',
        'critical_results','nutrition_check',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}