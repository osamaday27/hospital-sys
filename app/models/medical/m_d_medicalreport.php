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
use App\models\medical\m_d_medicalreporttemplate;

class m_d_medicalreport extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

     protected $table = 'm_d_medical_reports';

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'report_name',
        'doctor_id',
        'content',
        'report_date',
        'status',
    ];

  
    public function user()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function template()
{
    return $this->belongsTo(m_d_medicalreporttemplate::class, 'template_id');
}
public function doctor()
{
    return $this->belongsTo(User::class, 'doctor_id');
}

}