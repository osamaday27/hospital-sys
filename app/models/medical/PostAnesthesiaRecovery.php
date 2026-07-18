<?php
// app/Models/medical/PostAnesthesiaRecovery.php

namespace App\Models\medical;

use Illuminate\Database\Eloquent\Model;

class PostAnesthesiaRecovery extends Model
{
    protected $table = 'post_anesthesia_recoveries';

    protected $fillable = [
        'surgery_booking_id',
        'procedure_done',
        'recovery_time_start',
        'recovery_time_end',
        'surgeon_name',
        'anesthesiologist_name',
        'anesthesia_type',
        'sedation_type',
        'status_vitals',
        'level_of_consciousness',
        'lines_connected',
        'lines_description',
        'nurse_followup',
        'medications',
        'fluids',
        'unusual_events',
        'transfer_status',
        'discharge_criteria_met',
        'discharge_location',
        'discharge_time',
        'aldrete_score',
        'followup_instructions',
    ];

    protected $casts = [
        'anesthesia_type' => 'array',
        'status_vitals' => 'array',
        'nurse_followup' => 'array',
        'medications' => 'array',
        'fluids' => 'array',
        'transfer_status' => 'array',
        'aldrete_score' => 'array',
        'followup_instructions' => 'array',
    ];
}