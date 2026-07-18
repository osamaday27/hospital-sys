<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class mbexaminationhispat extends Model
{
    use HasFactory, SoftDeletes;

   protected $table = 'm_b_examination_his_pat';

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'examination',
        'status',
        'user_id'
    ];

  
}