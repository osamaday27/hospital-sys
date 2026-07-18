<?php
namespace App\Models\medical;

use Illuminate\Database\Eloquent\Model;

class intraoperativeanesthesiarecord extends Model
{
    protected $table = 'intraoperative_anesthesia_records';

    protected $fillable = [
        'surgery_booking_id',
        'changed_from_pre_anesthesia',
        'gcs', 'pulse', 'bp', 'spo2', 'npo', 'pre_assessment_time',
        'monitoring_data', 'remarks',
        'fluid_in', 'fluid_out', 'urine', 'blood_loss', 'net_balance',
        'ventilation', 'vt', 'rr',
        'anesthesia_technique', 'medications',
        'airway', 'monitor', 'iv_cannula',
        'unusual_event', 'post_op_condition', 'discharge_to',
        'bp_post', 'hr_post', 'rr_post',
        'anesthesiologist_signature', 'signature_datetime'
    ];

    protected $casts = [
        'monitoring_data' => 'array',
        'remarks' => 'array',
        'medications' => 'array',
        'airway' => 'array',
        'monitor' => 'array',
        'iv_cannula' => 'array',
    ];
}