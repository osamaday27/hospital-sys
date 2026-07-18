<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\general\adm_s_floors;
use App\models\general\specialties;
use App\Traits\TracksChanges;
use App\models\finance\service;
use App\Traits\Costing\HasCostCenters;
class admsclinic extends Model
{  
      use HasFactory, SoftDeletes, TracksChanges, HasCostCenters;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'adm_s_clinics';

    protected $fillable = ['name_ar', 'name_en', 'specialty_id', 'floor_id', 'clinic_code', 'logo', 'service_id','id_examination_service','id_follow_up_service','id_follow_up_opr_service','id_spe_examination_service','id_spe_follow_up_service','id_spe_follow_up_opr_service', 'status', 'asset_id', 'phone_number'];
    // العلاقة مع نموذج Building

    // علاقة مع القوائم الأخرى إذا كانت هناك مستويات متعددة
    public function parentList()
    {
        return $this->belongsTo(self::class, 'id'); // إذا كان هناك علاقة أب-ابن
    }
    public function childLists()
    {
        // تأكد من استخدام العمود الصحيح في العلاقة
        return $this->hasMany(self::class, 'id'); // هنا يجب أن يكون 'floor_id' هو العمود الذي يربط الغرف بالطوابق
    }
    // العلاقة مع التخصصات
    public function specialty()
    {
        return $this->belongsTo(specialties::class);
    }

    // العلاقة مع الطوابق
    public function floor()
    {
        return $this->belongsTo(adm_s_floors::class);
    }

    // العلاقة مع الخدمات
    public function service()
    {
        return $this->belongsTo(service::class);
    }

    // العلاقة مع الأصول
    public function asset()
    {
        return $this->belongsTo(FinAsset::class);
    }
      // العلاقة مع الخدمة الرئيسية
    public function mainService()
    {
        return $this->belongsTo(service::class, 'service_id');
    }

    // العلاقة مع خدمة الفحص
    public function examinationService()
    {
        return $this->belongsTo(service::class, 'id_examination_service');
    }

    // العلاقة مع خدمة المتابعة
    public function followUpService()
    {
        return $this->belongsTo(service::class, 'id_follow_up_service');
    }

    // العلاقة مع خدمة العمليات
    public function followUpOperationService()
    {
        return $this->belongsTo(service::class, 'id_follow_up_opr_service');
    }
}