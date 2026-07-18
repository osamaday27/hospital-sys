<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class vitalsign extends Model
{
     use HasFactory, SoftDeletes, TracksChanges;


    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'm_b_vital_signs';
    protected $fillable = [
        'id_patient', 'id_visit', 'id_reservation', 'type_visit','temperature', 'heart_rate', 'respiratory_rate',
        'systolic_bp', 'diastolic_bp', 'oxygen_saturation', 'blood_sugar',
        'weight', 'height', 'bmi', 'pain_scale', 'notes', 'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
   
}