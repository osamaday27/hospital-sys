<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class costcenter extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'cost_centers';

    protected $fillable = [
        'parent_id','code','name','type','status','notes','user_id',
    ];

    public $translatable = ['name'];

    // علاقات هرمية
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($q)
    {
        return $q->where('status','active');
    }

   public static function types(): array
{
    return [
        // وحدات طبية
        'clinic',        // العيادات الخارجية
        'surgery',       // العمليات الجراحية
        'lab',           // المختبرات
        'imaging',       // الأشعة
        'inpatient',     // التنويم الداخلي
        'pharmacy',      // الصيدلية
        'emergency',     // الطوارئ
        'icu',           // العناية المركزة
        'nicu',          // حضّانات الأطفال
        'dialysis',      // الغسيل الكلوي
        'physiotherapy', // العلاج الطبيعي
        'endoscopy',     // المناظير
        'dental',        // الأسنان
        'ophthalmology', // العيون
        'maternity',     // الولادة
        'oncology',      // الأورام
        'cardiology',    // القلب

        // وحدات إدارية وخدمية
        'admin',         // الإدارة
        'hr',            // الموارد البشرية
        'finance',       // المالية
        'it',            // تقنية المعلومات
        'maintenance',   // الصيانة
        'housekeeping',  // الخدمات الفندقية
        'security',      // الأمن
        'catering',      // التغذية
        'laundry',       // المغسلة
        'transportation',// النقل
        'store',         // المخازن

        // مراكز داعمة
        'research',      // البحث العلمي
        'education',     // التدريب والتعليم
        'marketing',     // التسويق
        'other'          // أخرى
    ];
}


    public static function statuses(): array
    {
        return ['active','inactive'];
    }
    // داخل class costcenter
public function assignments()
{
    return $this->hasMany(\App\Models\costing\costcenterassignment::class, 'cost_center_id');
}

}