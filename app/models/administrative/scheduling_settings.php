<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\administrative\admsclinic;
class scheduling_settings extends Model 
{

       protected $fillable = [
        'id_doctor',
        'id_clinic',
        'day',
        'first_shift_from',
        'first_shift_to',
        'second_shift_from',
        'second_shift_to',
        'third_shift_from',
        'third_shift_to',
        'account_id',
        'status',
        'user_id',
    ];
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'adm_s_scheduling';
    
    public function doctor(){
        return $this->belongsTo (Employee::class, 'id_doctor');
    }
      public function clinic(){
        return $this->belongsTo (admsclinic::class, 'id_clinic');
    }
}