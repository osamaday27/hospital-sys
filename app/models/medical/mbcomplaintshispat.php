<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User;

class mbcomplaintshispat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_b_complaints_his_pat';

    protected $fillable = [
        'id_patient',
        'id_visit',
        'id_reservation',
        'type_visit',
        'complaints',
        'complan_date', // تاريخ الشكوى 
        'complants_id', // معرف الشكوى
        'status',
        'user_id',
        'eye',
        'onset',
        'duration',
        'course',
        'remarks',
    ];

  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}