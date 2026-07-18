<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User;

class OphthalmologyAssessment extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $fillable = ['id_visit', 'glasses_left', 'glasses_right', 'uava_bcva_left', 'uava_bcva_right', 'cycloplegic_refraction_left', 'cycloplegic_refraction_right', 'iop_left', 'iop_right', 'pupil_left', 'pupil_right', 'ocular_motility_left', 'ocular_motility_right', 'eyelid_and_lacrimal_left', 'eyelid_and_lacrimal_right', 'conjunctive_and_sclera_left', 'conjunctive_and_sclera_right', 'cornea_left', 'cornea_right', 'ac_and_iris_left', 'ac_and_iris_right', 'lens_left', 'lens_right', 'anterior_vitreous_left', 'anterior_vitreous_right', 'fundus', 'account_id', 'user_id', 'id_patient', 'id_reservation'];
    protected $table = 'm_b_op_initial_assessment';
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

?>