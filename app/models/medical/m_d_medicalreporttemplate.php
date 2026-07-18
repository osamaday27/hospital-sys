<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class m_d_medicalreporttemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_s_medical_report_templates';

    protected $fillable = [
        'name',
        'content',
        'description',
        'category',
        'is_global',
        'is_active',
        'specialty_id',
        'user_id',
        'created_by',
        'updated_by',
    ];

    // 🩺 علاقة التخصص الرئيسي
    public function specialty()
    {
        return $this->belongsTo(\App\Models\General\specialties::class, 'specialty_id');
    }

    // ⚕️ علاقة التخصصات المتعددة المسموح لها
    public function specialties()
    {
        return $this->belongsToMany(\App\Models\General\specialties::class, 'm_s_medical_report_template_specialty', 'template_id', 'specialty_id');
    }

    // 👥 علاقة المستخدمين المسموح لهم
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'm_s_medical_report_template_user', 'template_id', 'user_id');
    }

    // 👨‍💻 المستخدم الذي أنشأ القالب
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}