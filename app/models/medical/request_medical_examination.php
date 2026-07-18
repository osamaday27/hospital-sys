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

class request_medical_examination extends Model
{
     use HasFactory, SoftDeletes, TracksChanges;


    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'm_d_request_medical_examination';
     protected $fillable = [
        'patient_id',
        'visit_id',
        'requested_by',
        'service_id',
        'eye',
        'preliminary_diagnosis',
        'clinical_info',
        'test_type',
        'status',
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
     // العلاقة مع الخدمات
    public function service()
    {
        return $this->belongsTo(service::class, 'service_id');
    }
}