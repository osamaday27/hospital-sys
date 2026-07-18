<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\specialties;

class doctor_order extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
 // اسم الجدول في قاعدة البيانات
    protected $table = 'm_d_lab_xray_requests';

    // الحقول القابلة للتعبئة (Mass Assignment)
    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'request_type',
        'service_id',
        'request_date',
        'status',
        'user_id',
    ];

}