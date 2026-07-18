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

class m_d_eye_examinations_eom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_d_eye_examinations_eom';

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'doctor_id',
        'right_sr', 'right_io', 'right_ir', 'right_so', 'right_lr', 'right_mr',
        'left_sr', 'left_io', 'left_ir', 'left_so', 'left_lr', 'left_mr',
        'eom_image_path',
    ];
    /**
     * Get the user who created the surgery record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}