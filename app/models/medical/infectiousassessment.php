<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class infectiousassessment extends Model
{
    protected $table = 'infectious_assessments';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'cough_no','cough_yes','cough_details',
        'fever_no','fever_yes','fever_details',
        'rash_no','rash_yes','rash_details',
        'travel_no','travel_yes','travel_details',
        'wear_mask','separate_patient','notify_doctor_action','accelerate_examination',
        'notify_doctor_infectious_yes','notify_doctor_infectious_no',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}