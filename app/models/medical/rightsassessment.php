<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class rightsassessment extends Model
{
    protected $table = 'rights_assessments';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'rights_yes','rights_no','rights_action',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_add');
    }
}