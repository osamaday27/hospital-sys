<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class radiologyquestionssettings extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'rad_s_questions';

    protected $fillable = [
        'question',
        'answer_type',
        'options',
        'active',
    ];

    public $translatable = ['question'];

    protected $casts = [
        'options' => 'array',
        'active'  => 'boolean',
    ];
}
