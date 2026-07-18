<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User;

class followophthalmologyassessment extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $fillable = ['id_visit' ,'uava_bcva_left', 'uava_bcva_right','iop_left', 'iop_right','ant_segment_right', 'ant_segment_left',  'fundus', 'account_id', 'user_id', 'id_patient', 'id_reservation'];
    protected $table = 'm_b_follow_assessment';
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

?>