<?php

namespace App\Models\Medical;


use Illuminate\Database\Eloquent\Model;

class operativeprocedurereport extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'booking_id',
        'preoperative_diagnosis',
        'postoperative_diagnosis',
        'surgeon_name',
        'first_assistant',
        'time_started',
        'time_ended',
        'anesthesiologist',
        'anesthesia_type',
        'anesthesia_other',
        'procedures_done',
        'operation_details',
        'blood_loss_ml',
        'blood_transfused_ml',
        'complication_occurred',
        'complication_details',
        'specimens_sent',
        'specimen_no',
        'specimen_origin',
        'specimen_size_description',
        'specimen_sent_to_lab',
        'specimen_pathology',
        'specimen_culture',
        'implantable_devices',
        'implant_details',
        'report_date',
        'report_time',
        'surgeon_signature',
        'created_by',
    ];
    protected $table = 'operative_procedure_reports';

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function booking()
    {
        return $this->belongsTo(SurgeryBooking::class, 'booking_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

  
}