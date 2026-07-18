<?php
namespace App\Models\administrative;

use Illuminate\Database\Eloquent\Model;

class follow_up_request extends Model
{
    protected $fillable = [
        'patient_id', 'visit_id', 'doctor_id', 'clinic_id',
        'request_date', 'followup_date', 'notes', 'user_id'
    ];

    public function patient()
    {
        return $this->belongsTo(\App\models\administrative\medical_record::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\models\hr\Employee::class, 'doctor_id');
    }

    public function clinic()
    {
        return $this->belongsTo(\App\models\administrative\admsclinic::class, 'clinic_id');
    }
}