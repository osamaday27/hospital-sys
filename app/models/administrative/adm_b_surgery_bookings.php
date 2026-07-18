<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\administrative\admsclinic;
use App\models\administrative\medical_record;
use App\models\general\adm_s_rooms_surgical;
use App\models\finance\service;
use App\models\finance\advancepayment;
use App\models\administrative\surgicaldatasetting;
use App\models\User;
use App\models\medical\ms_diseases_list;
class adm_b_surgery_bookings extends Model
{
    protected $fillable = ['cancel_reason', 'surgical_id', 'room_id', 'procedure_side', 'patient_id', 'booking_date', 'scheduled_from', 'scheduled_to', 'status', 'created_by', 'visit_id', 'contract_id' , 'case_type' 
];
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'adm_b_surgery_bookings';

    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }
    public function visit()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }
    public function room()
    {
        return $this->belongsTo(adm_s_rooms_surgical::class, 'room_id');
    }
  
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function bookingSurgeries()
{
    return $this->hasMany(BookingSurgery::class, 'booking_id');
}


    public function diagnoses()
    {
        return $this->belongsToMany(ms_diseases_list::class, 'booking_diagnosis', 'booking_id', 'diagnosis_id');
    }
    // داخل App\models\administrative\adm_b_surgery_bookings
    public function medicalTeam()
    {
        return $this->hasMany(\App\models\administrative\surgery_medical_team::class, 'surgery_bookings_id');
    }
    public function statusHistory()
    {
        return $this->hasMany(\App\Models\Administrative\SurgeryStatusHistory::class, 'surgery_booking_id');
    }
    // ✅ العلاقة الجديدة لحجوزات الدفعات المقدمة
    public function advancePayments()
    {
        return $this->hasMany(advancepayment::class, 'booking_id');
    }
      public function services()
    {
        return $this->belongsToMany(\App\Models\Finance\service::class, 'booking_surgeries', 'booking_id', 'surgery_id')
            ->withPivot('side') // علشان تجيب العمود side
            ->withTimestamps();
    }
    public function contract()
{
    return $this->belongsTo(\App\Models\Finance\contract_last_level::class, 'contract_id');
}

}