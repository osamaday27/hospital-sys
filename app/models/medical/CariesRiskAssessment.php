<?php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CariesRiskAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'id_reservation',
        'id_visit',
        'type_visit',
        'high_risk',
        'moderate_risk',
        'protective_factors',
        'brushing_frequency',
        'brushing_method',
        'brush_type',
        'interproximal_cleansing',
        'caries_risk_overall',
        'recommendations_given',
        'date_given',
        'faculty_signature',
        'date_reviewed',
        'status',
        'created_by',
    ];

    protected $casts = [
        'high_risk' => 'array',
        'moderate_risk' => 'array',
        'protective_factors' => 'array',
        'recommendations_given' => 'boolean',
    ];
}