<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\administrative\medical_record;
use App\Models\hr\Employee;

class OphthalmicSurgicalReport extends Model
{
    use HasFactory;
    protected $table = 'ophthalmic_surgical_reports';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'booking_id',
        'doctor_id',
        'report_type',
        'surgery_date',
        'eye',
        'anesthesia',
        'surgeon',
        'preoperative_diagnosis',
        'postoperative_diagnosis',
        'surgical_procedure',
        'operative_details',
        'complications',
        'materials_used',
        'instrumentation',
        'iridectomy_type',
        'status',
        'created_by',
    ];
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Employee::class, 'doctor_id');
    }
}