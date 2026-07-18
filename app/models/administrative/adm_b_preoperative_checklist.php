<?php
// app/models/administrative/adm_b_preoperative_checklist.php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;

class adm_b_preoperative_checklist extends Model
{
    protected $fillable = [
        'booking_id',
        'operation_name',
        'equipment_status',
        'consent_signed',
        'site_marked',
        'physical_exam_done',
        'images_available',
        'labs_available',
        'allergy_recorded',
        'surgeon_signature',
        'nurse_signature',
        'checklist_date',
        'checklist_time',
    ];

    protected $casts = [
        'equipment_status' => 'array',
        'consent_signed' => 'string',
        'site_marked' => 'string',
        'physical_exam_done' => 'string',
        'images_available' => 'string',
        'labs_available' => 'string',
        'allergy_recorded' => 'string',
    ];
}