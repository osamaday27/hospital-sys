<?php

namespace App\models\administrative;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Model;


use App\models\administrative\adm_s_question;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\adm_b_questionnaire;
use App\models\administrative\adm_s_answer_values;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class adm_b_questionnaires_details extends Model
{
    use HasFactory,SoftDeletes,TracksChanges;

    protected $table = 'adm_b_questionnaires_details';

    protected $fillable = [
        'questionnair_id',
        'question_id',
        'answer_id',
        'notes',
        'text',
    ];

    /**
     * العلاقة مع جدول adm_b_questionnaires
     */
    public function questionnaire()
    {
        return $this->belongsTo(adm_b_questionnaire::class, 'questionnair_id');
    }

    /**
     * العلاقة مع جدول adm_s_questions
     */
    public function question()
    {
        return $this->belongsTo(adm_s_question::class, 'question_id');
    }

    /**
     * العلاقة مع جدول adm_s_answer_values
     */
    public function answer()
    {
        return $this->belongsTo(adm_s_answer_values::class, 'answer_id');
    }
}