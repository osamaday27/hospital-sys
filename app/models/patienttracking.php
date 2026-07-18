<?php

namespace App\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;

class patienttracking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_tracking';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'step_name',
        'step_status',
        'assigned_by',
        'assigned_to',
        'notes',
        'start_time',
        'end_time',
    ];

    /**
     * العلاقة مع جدول المرضى
     */
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }

    /**
     * العلاقة مع جدول الزيارات
     */
    public function visit()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }

    /**
     * العلاقة مع المستخدم الذي سجل الخطوة
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * العلاقة مع المستخدم المسؤول عن تنفيذ الخطوة
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}