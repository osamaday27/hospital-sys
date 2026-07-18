<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class fallriskscreening extends Model
{
    protected $table = 'fall_risk_screenings';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'assist_patient','ensure_aids','review_medications','refer_conditions','avoid_crowding',
        'advise_no_self_move','advise_no_sudden_stand','advise_no_left_alone_child','advise_report_dizziness',
        'notify_doctor_yes','notify_doctor_no',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}