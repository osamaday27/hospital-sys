<?php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksChanges;

class mddreassessmentform extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $table = 'md_d_reassessment_forms';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'reservation_id',
        'cough_no',
        'cough_yes',
        'cough_details',
        'fever_no',
        'fever_yes',
        'fever_details',
        'rash_no',
        'rash_yes',
        'rash_details',
        'travel_no',
        'travel_yes',
        'travel_details',
        'wear_mask',
        'separate_patient',
        'notify_doctor_action',
        'accelerate_examination',
        'doctor_notified',
        'live_alone',
        'no_problem',
        'total_dependence',
        'movement_problems',
        'no_assistance',
        'walker',
        'wheelchair',
        'crutches',
        'other_assistance',
        'doctor_notified_functional',
        'pain_no',
        'pain_yes',
        'temperature',
        'respiratory_rate',
        'blood_pressure',
        'pulse',
        'random_sugar',
        'weight',
        'height',
        'help_patient_when_needed',
        'ensure_appropriate_aids',
        'review_medications',
        'refer_to_specialist',
        'avoid_crowding',
        'dont_move_alone',
        'dont_sudden_standing',
        'dont_leave_child_alone',
        'alert_medical_team',
        'self_care',
        'instructions_clear',
        'doctor_alert_summary',
        'social_exam',
        'social_exam_details',
        'special_needs_summary',
        'special_needs_details',
        'infectious_disease_summary',
        'infectious_disease_details',
        'pain_check_summary',
        'pain_check_details',
        'allergy_check',
        'allergy_details',
        'critical_results',
        'nutrition_check',
    ];
}