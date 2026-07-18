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

class m_d_subjectiverefraction extends Model
{
     use HasFactory, SoftDeletes;

    protected $table = 'm_d_subjective_refractions';

    protected $fillable = [
        'id_patient', 'id_visit', 'id_reservation', 'type_visit', 'refraction_type',
        'od_sph', 'od_cyl', 'od_axis', 'od_near_add', 'od_ucva', 'od_near_uava', 'od_bcva', 'od_near_bcva',
        'os_sph', 'os_cyl', 'os_axis', 'os_near_add', 'os_ucva', 'os_near_uava', 'os_bcva', 'os_near_bcva',
        'ou_ucva', 'ou_near_uava', 'ou_bcva', 'ou_near_bcva',
        'ipd', 'od_comment', 'os_comment', 'created_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}