<?php
// Model: mbdentalcomplaint.php

namespace App\models\medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class mbdiagnoseshispat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_d_diagnoseshispateye';

    protected $fillable = [
        'id_patient', 'id_reservation', 'id_visit', 'type_visit',
        'diagnosis', 'eye', 'remarks', 'user_id',  'diag_flag',  
    ];
}