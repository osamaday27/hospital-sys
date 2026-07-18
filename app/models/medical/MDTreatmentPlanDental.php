<?php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MDTreatmentPlanDental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_d_treatment_plans_dental';

    protected $fillable = [
        'patient_id',
        'id_reservation',
        'id_visit',
        'type_visit',
        'tooth',
        'seq',
        'clinical_radiographic_findings',
        'department',
        'treatment_options',
        'faculty_signature',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}