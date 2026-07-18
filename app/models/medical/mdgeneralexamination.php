<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\models\User;

class mdgeneralexamination extends Model
{
    use HasFactory, SoftDeletes,TracksChanges;

    protected $table = 'm_d_general_examinations';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'general_appearance',
        'head_neck', 'comment_head_neck',
        'chest', 'comment_chest',
        'abdomen', 'comment_abdomen',
        'cns', 'comment_cns',
        'heart', 'comment_heart',
        'git', 'comment_git',
        'edema', 'comment_edema',
        'other', 'comment_other',
        'user_id',
    ];

   public function patient()
    {
        return $this->belongsTo(medical_record::class, 'patient_id');
    }
    public function visit()
    {
        return $this->belongsTo(patient_visits::class, 'visit_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}