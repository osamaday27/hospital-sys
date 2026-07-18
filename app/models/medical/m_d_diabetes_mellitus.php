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

class m_d_diabetes_mellitus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_d_diabetes_mellitus';

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'type',
        'controlled',
        'medication',
        'hba1c',
        'duration',
        'duration_unit',
        'created_by',
    ];


    /**
     * Get the user who created the surgery record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}