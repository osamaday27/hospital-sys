<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\specialties;

class mdpainassessment_doc extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
 
    // اسم الجدول في قاعدة البيانات
    protected $table = 'm_d_pain_assessment_doc';

    // الحقول القابلة للتعديل
    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'id_mdpainassessment',
        'assessment',
        'degree',
        'pain_location',
        'continuous_intermittent',
        'pain_type',
        'increases_during',
        'decreases_during',
        'treatment',
        'reassessment',
        'status',
        'user_id',
    ];

}