<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\administrative\admsclinic;
class adm_s_operation_schedules extends Model 
{

        protected $fillable = [
        'id_room', 'day', 'start_time', 'end_time', 'duration', 'branches_id','user_id','status'
    ];
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'adm_s_operation_schedules';
    
  
}