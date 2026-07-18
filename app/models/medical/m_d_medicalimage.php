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
use App\models\administrative\reservations;
class m_d_medicalimage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'path', 'type', 'notes',
        'patient_id', 'visit_id', 'reservation_id',
        'user_id', 'status'
    ];
   // علاقات اختيارية لو محتاجها
    public function patient() {
        return $this->belongsTo(medical_record::class);
    }

    public function visit() {
        return $this->belongsTo(patient_visits::class);
    }

    public function reservation() {
        return $this->belongsTo(reservations::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}