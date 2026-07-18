<?php


namespace App\models\administrative;

use App\models\hr\section;
use App\models\hr\Department;
use Illuminate\Database\Eloquent\Model;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\adm_s_complaint_problem;


class adm_b_complaint extends Model
{
    use SoftDeletes;

    protected $table = 'adm_b_complaints';

    protected $fillable = [
        'title',
        'registered_patient',
        'patient_id',
        'visit_id',
        'patient_name',
        'patient_age',
        'patient_gender',
        'problem_id',
        'priority',
        'complaint_date',
        'department_id',
        'sections_id',
        'complaint',
        'attachments',
        'complaint_place',
        'contact_information',
    ];

    protected $casts = [
        'contact_information' => 'array',
    ];

    // يمكنك تعريف العلاقات مع باقي الجداول
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }

    public function visit()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }

    public function problem()
    {
        return $this->belongsTo(adm_s_complaint_problem::class, 'problem_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function section()
    {
        return $this->belongsTo(section::class, 'sections_id');
    }

    public function statuses()
    {
        return $this->hasMany(adm_b_complaint_status::class, 'complaint_id');
    }
}