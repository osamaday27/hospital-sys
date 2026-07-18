<?php

namespace App\models\administrative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\administrative\inpatientbooking;
use App\models\general\adm_s_admission_reasons;
use App\models\general\adm_s_beds;

use App\Traits\TracksChanges;
class bookingattachment extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'adm_b_booking_attachments';

    // تحديد الأعمدة القابلة للتعبئة
    protected $fillable = ['booking_id', 'patient_id_front', 'patient_id_back', 'companion_id_front', 'companion_id_back', 'admission_order', 'medical_report', 'user_id'];

    // تحديد العلاقات (إذا لزم الأمر)
    public function booking()
    {
        return $this->belongsTo(inpatientbooking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // إذا كان لديك علاقات أخرى مثل زيارة المريض أو غيرها، يمكن إضافتها هنا
}