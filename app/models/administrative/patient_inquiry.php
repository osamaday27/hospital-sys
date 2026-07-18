<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class patient_inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_inquiries';

    protected $fillable = [
        'is_registered',
        'patient_id',
        'full_name',
        'gender',
        'age',
        'inquiry_date',
        'contact_methods',
        'inquiry',
        'status',
        'doctor_id',
        'referral_entity',
        'heard_about_us',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_registered'  => 'boolean',
        'inquiry_date'   => 'date',
        'contact_methods'=> 'array',
    ];

    /* العلاقات */
    public function patient()
    {
        // جدول المرضى عندك اسمه الفعلي "patient"
        return $this->belongsTo(\App\models\administrative\medical_record::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\models\hr\Employee::class, 'doctor_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
