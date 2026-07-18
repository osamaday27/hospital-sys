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

class mbdiseaseshispat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_b_diseases_his_pat';

    protected $fillable = ['id_patient',    'diag_flag',  
 'id_visit', 'id_reservation', 'type_visit', 'diseases', 'eye', 'for_number', 'for_period', 'remarks', 'status', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}