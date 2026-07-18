<?php

namespace App\models\administrative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
class doctorapology extends Model
{
    protected $fillable = [
        'doctor_id', 'clinic_id', 'date_from', 'date_to', 'shifts', 'status', 'reason', 'notes'
    ];
    protected $table = 'adm_s_doctor_apologies';

    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
     public function doctor(){
        return $this->belongsTo (Employee::class, 'id_doctor');
    }
 
     public function clinic(){
        return $this->belongsTo (admsclinic::class, 'clinic_id');
    }
     public function patient_visits(){
        return $this->belongsTo (patient_visits::class, 'visit_id');
    }
     protected $casts = [
        'shifts' => 'array',  // هذه الخطوة تضمن التعامل مع الـ JSON كمصفوفة
    ];
}