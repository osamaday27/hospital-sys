<?php
namespace App\models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User; // تأكد من استيراد نموذج User

class mddentalhistory extends Model
{
    use HasFactory, TracksChanges;

    protected $table = 'm_d_dental_histories';

    protected $fillable = [
        'patient_id',
        'id_reservation',
        'id_visit',
        'type_visit',
        'dental_care',
        'past_treatment',
        'jaw_trauma_notes',
        'adverse_reaction_notes',
        'dental_fears_notes',
        'oral_health_state',
    ];

    protected $casts = [
        'past_treatment' => 'array',
    ];
}