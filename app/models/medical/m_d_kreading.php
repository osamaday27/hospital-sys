<?php
namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User;
use App\models\finance\service;

class m_d_kreading extends Model
{
   use HasFactory, SoftDeletes;

   
    protected $table = 'm_d_k_readings'; // اسم الجدول
    protected $fillable = [
        'id_patient', 'id_reservation', 'id_visit', 'type_visit',
        'od_k1_number', 'od_k1_axis', 'od_k2_number', 'od_k2_axis', 'od_comment',
        'os_k1_number', 'os_k1_axis', 'os_k2_number', 'os_k2_axis', 'os_comment',
        'created_by'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}