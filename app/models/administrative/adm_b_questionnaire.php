<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\medical_record;
use App\models\administrative\adm_s_questionnaires;
use App\models\administrative\patient_visits;
use App\models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksChanges;

class adm_b_questionnaire extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $table = 'adm_b_questionnaires';

    protected $fillable = [
        'patient_id',
        'patient_visits_id',
        'questionnair',
        'status',
        'attachments',
        'created_by',
        'closed_by',
    ];

    protected $casts = [
        'attachments' => 'array', // تحويل البيانات المخزنة في JSON إلى مصفوفة تلقائيًا
    ];

    /**
     * العلاقة مع جدول المرضى (Patient)
     */
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }

    /**
     * العلاقة مع جدول زيارات المرضى (PatientVisits)
     */
    public function patientVisit()
    {
        return $this->belongsTo(patient_visits::class, 'patient_visits_id');
    }

    /**
     * العلاقة مع جدول الاستبيانات (AdmSQuestionnaire)
     */
    public function questionnaire()
    {
        return $this->belongsTo(adm_s_questionnaires::class, 'questionnair');
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ السجل (User)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المستخدم الذي أغلق الاستبيان (User)
     */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
