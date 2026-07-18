<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class mdischargeplanning extends Model
{
    protected $table = 'discharge_plannings';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'care_place',
        'help_at_home_yes','help_at_home_no',
        'self_care_yes','self_care_no',
        'instructions_clear_yes','instructions_clear_no',
        'need_assist_reminder','need_assist_medication','need_assist_stairs','need_assist_toilet','need_assist_bathing',
        'doctor_alert_yes','doctor_alert_no',
        'social_exam_normal','social_exam_abnormal','social_exam_details',
        'special_needs_yes','special_needs_no','special_needs_details',
        'infectious_yes','infectious_no','infectious_details',
        'functional_normal','functional_abnormal',
        'pain_no','pain_yes','pain_degree',
        'allergy_no','allergy_yes','allergy_details',
        'critical_results_yes','critical_results_no',
        'nutrition_normal','nutrition_abnormal',
        'nurse_signature','nurse_id','nurse_date','nurse_time',
        'doctor_signature','doctor_id','doctor_date','doctor_time',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}