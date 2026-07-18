<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class allergyassessment extends Model
{
    protected $table = 'allergy_assessments';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'allergies_no','allergies_yes',
        'allergy_food','allergy_medication','allergy_others','allergy_others_details',
        'notify_doctor_yes','notify_doctor_no',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}