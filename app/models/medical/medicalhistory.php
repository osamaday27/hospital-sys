<?php
namespace App\models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User; // تأكد من استيراد نموذج User

class medicalhistory extends Model
{
    use HasFactory, TracksChanges;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'm_d_dental_medical_histories';
  protected $fillable = [
        'patient_id', 'id_reservation', 'id_visit', 'type_visit',
        'medical_conditions', 'medications', 'medical_treatment', 'medical_treatment_note',
        'serious_operation', 'serious_operation_note', 'taking_medication', 'taking_medication_note',
        'smoker', 'smoke_count', 'female', 'allergies', 'status',
    ];

    protected $casts = [
        'medical_conditions' => 'array',
        'medications' => 'array',
        'female' => 'array',
        'allergies' => 'array',
        'medical_treatment' => 'boolean',
        'serious_operation' => 'boolean',
        'taking_medication' => 'boolean',
        'smoker' => 'boolean',
    ];
}