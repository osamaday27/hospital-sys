<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class painassessment extends Model
{
    protected $table = 'pain_assessments';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'pain_no','pain_yes',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}