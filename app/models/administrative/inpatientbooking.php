<?php

namespace App\models\administrative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\administrative\medical_record;
use App\models\general\adm_s_admission_reasons;
use App\models\general\adm_s_beds;
use App\models\general\adm_s_rooms;
use App\models\general\nursingstation;

use App\Traits\TracksChanges;
class inpatientbooking extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'adm_s_inpatient_booking';

    // تحديد الأعمدة القابلة للتعبئة
    protected $fillable = ['patient_id', 'bed_id', 'patient_status', 'case_type', 'case_severity', 'booking_date', 'expected_discharge_date', 'admission_source_id', 'referring_physician_id', 'treating_physician_id', 'referred_hospital', 'referring_physician_notes', 'specialty_notes', 'room_id', 'nursing_station_id', 'full_room_booking', 'residency_level', 'blood_donation', 'admission_reason', 'manual_booking_number', 'booking_notes', 'initial_diagnosis', 'physician_request_notes', 'visit_id','status', 'created_by'];

    // تحديد العلاقات (إذا لزم الأمر)
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }

    public function bed()
    {
        return $this->belongsTo(adm_s_beds::class, 'bed_id');
    }

    public function room()
    {
        return $this->belongsTo(adm_s_rooms::class, 'room_id');
    }

    public function nursingStation()
    {
        return $this->belongsTo(nursingstation::class, 'nursing_station_id');
    }

    public function admissionSource()
    {
        return $this->belongsTo(adm_s_admission_reasons::class, 'admission_source_id');
    }

    public function referringPhysician()
    {
        return $this->belongsTo(Employee::class, 'referring_physician_id');
    }

    public function attendingPhysician()
    {
        return $this->belongsTo(Employee::class, 'attending_physician_id');
    }
  

    // إذا كان لديك علاقات أخرى مثل زيارة المريض أو غيرها، يمكن إضافتها هنا
}