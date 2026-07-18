<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class functionalassessment extends Model
{
    protected $table = 'functional_assessments';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'live_alone','no_problem','total_dependence','movement_problems',
        'no_assistance','walker','wheelchair','crutches','other_assistance',
        'notify_doctor_functional_yes','notify_doctor_functional_no',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}