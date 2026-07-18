<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\finance\service;
use App\models\User;

class reservations extends Model
{
    use SoftDeletes;

    // اسم جدول الحجوزات في قاعدة البيانات
    protected $table = 'adm_s_reservations';

    // القيم الافتراضية للحقول المالية
    protected $attributes = [
        'payment_method' => '',
        'total' => 0.0,
        'patient_contribution' => 0.0,
        'contract_contribution' => 0.0,
        'discount' => 0.0,
        'paid_amount' => 0.0,
        'remaining_amount' => 0.0,
    ];

    // الحقول المسموح بالتعبئة الجماعية
    protected $fillable = [
        'id_patient',
        'visit_id',
        'clinic_id',
        'id_doctor',
        'date_visit',
        'date_reservations',
        'time_slot',
        'reservation_type',
        'q_number',
        'status',
        'contract',
        'reason_cancel_reservation',
        'payment_method',
        'total',
        'patient_contribution',
        'contract_contribution',
        'discount',
        'paid_amount',
        'remaining_amount',
        'reservation_note',
        'confirmed_at',
        'user_id',
        'account_id',
        'contract_id',
        'confirmed_at',
        'arrival_confirmed_at',
        'case_type',
        'reservation_source', // ✅ الحقل الجديد
        'cancel_reason_id',
        'cancel_note',
    ];

    // أنواع البيانات الخاصة بالحقول
    protected $casts = [
        'date_visit' => 'date',
        'date_reservations' => 'date',
        'confirmed_at' => 'datetime',
        'total' => 'decimal:2',
        'patient_contribution' => 'decimal:2',
        'contract_contribution' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'arrival_confirmed_at' => 'datetime',
    ];

    // تواريخ الحذف الناعم وتأكيد الحجز
    protected $dates = ['deleted_at', 'confirmed_at'];

    /**
     * علاقة الطبيب
     */
    public function doctor()
    {
        return $this->belongsTo(Employee::class, 'id_doctor');
    }
    public function cancelReason()
    {
        return $this->belongsTo(\App\Models\administrative\absencereason::class, 'cancel_reason_id');
    }
    /**
     * علاقة المريض
     */
    public function patient()
    {
        return $this->belongsTo(medical_record::class, 'id_patient');
    }

    /**
     * علاقة العيادة
     */
    public function clinic()
    {
        return $this->belongsTo(admsclinic::class, 'clinic_id');
    }

    /**
     * علاقة زيارة المريض
     */
    public function patient_visits()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }

    /**
     * علاقة نوع الخدمة
     */
    public function service()
    {
        return $this->belongsTo(service::class, 'reservation_type');
    }

    /**
     * علاقة المستخدم الذي أنشأ الحجز
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function contract()
    {
        return $this->belongsTo(\App\Models\Finance\contract_last_level::class, 'contract_id');
    }
    public function absenceReason()
    {
        return $this->belongsTo(AbsenceReason::class, 'cancel_reason_id');
    }
}