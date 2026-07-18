<?php
namespace App\models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User; // تأكد من استيراد نموذج User

class mddinstruction extends Model
{
    use HasFactory;

    protected $table = 'md_d_instructions';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'reservation_id',
        'doctor_id',
        'nurse_id',
        'instruction',
        'importance',
        'status',
        'started_at',
        'completed_at',
        'time_specific',
        'scheduled_at',
        'repeat_count',
        'repeat_interval',
        'on_repeat_action',
        'notes',
    ];

    // العلاقات
    public function patient()
    {
        return $this->belongsTo(\App\Models\administrative\medical_record::class, 'patient_id');
    }

    public function visit()
    {
        return $this->belongsTo(\App\Models\administrative\patient_visits::class, 'visit_id');
    }

    public function reservation()
    {
        return $this->belongsTo(\App\Models\administrative\reservations::class, 'reservation_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_id');
    }

    public function nurse()
    {
        return $this->belongsTo(\App\Models\User::class, 'nurse_id');
    }
}