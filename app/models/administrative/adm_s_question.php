<?php

namespace App\Models\administrative;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class adm_s_question extends Model
{
    use HasFactory, SoftDeletes,TracksChanges;

    protected $table = 'adm_s_questions';

    protected $fillable = [
        'name_ar',
        'name_en',
        'type_id',
        'answer_id',
        'text',
        'created_by',
    ];
    protected $casts = [
        'text' => 'boolean',
    ];

    public function answerValues()
{
    return $this->hasMany(\App\Models\Administrative\adm_s_answer_values::class, 'type_id', 'answer_id');
}

    public function questionnaireType()
    {
        return $this->belongsTo(adm_s_questionnaires::class, 'type_id');
    }

    public function answerType()
    {
        return $this->belongsTo(adm_s_answer_type::class, 'answer_id');
    }
}
