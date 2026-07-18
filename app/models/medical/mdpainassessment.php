<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\specialties;

class mdpainassessment extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
 
    protected $table = 'm_d_pain_assessment'; // اسم الجدول

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'digital_assessment',
        'face_assessment',
        'child_assessment',
        'pain_type',
        'notes',
        'status',
        'user_id',
    ];

}