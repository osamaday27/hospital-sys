<?php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraOralIntraOralExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'id_reservation',
        'id_visit',
        'type_visit',
        'general_appearance',
        'mobility',
        'lesions_or_scars',
        'other_notes',
        'lymph_node',
        'head_neck_notes',
        'tmj',
        'tmj_notes',
        'asymmetries',
        'lips_labial_mucosa',
        'buccal_vestibular_mucosa',
        'hard_soft_palate',
        'tongue',
        'floor_of_mouth',
        'gingiva_alveolar_mucosa',
        'further_examinations',
    ];
}