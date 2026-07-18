<?php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\User;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
    
        'doctor_id',
        'prescription_date',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(medical_record::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function visit()
    {
        return $this->belongsTo(patient_visits::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}