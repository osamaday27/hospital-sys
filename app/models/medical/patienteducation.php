<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User;
use App\models\finance\service;

class patienteducation extends Model
{
     use HasFactory, SoftDeletes, TracksChanges;
    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'm_d_patient_education';
   protected $fillable = [
        'assessment_date',
        'assessment_time',
        'department',
        'education_recipient',
        'relation_to_patient',
        'education_level',
        'learning_ability',
        'main_language',
        'other_language',
        'barriers',
        'interventions',
        'nurse_name',
        'nurse_date',
        'nurse_time',
        'education_sessions',
        'education_method',
        'evaluation',
        'action_taken',
    ];

    protected $casts = [
        'barriers' => 'array',
        'interventions' => 'array',
        'education_sessions' => 'array',
        'education_method' => 'array',
    ];
  
   public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }
    public function visit()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
  
}