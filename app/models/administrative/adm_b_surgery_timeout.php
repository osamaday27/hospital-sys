<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class adm_b_surgery_timeout extends Model
{
    use HasFactory;

    protected $fillable = [
        'surgery_booking_id',
        'procedure',
        'pre_op_diagnosis',
        'patient_identity',
        'correct_side',
        'correct_procedure',
        'availability_of_implants',
        'anesthesia_concerns',
        'antibiotic_prophylaxis_given',
        'essential_imaging_displayed',
        'timeout_documented',
        'assistant_surgeon',
        'anesthesiologist',
        'sterile_nurse',
        'timeout_time',
        'timeout_date',
        'procedure_start_time',
        'procedure_end_time',
        'surgeon_signature',
        'nurse_signature',
    ];
}