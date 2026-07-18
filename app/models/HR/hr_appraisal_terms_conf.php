<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_appraisal_terms_conf extends Model
{
    use HasFactory;

    protected $table = 'hr_appraisal_terms_conf';

    protected $fillable = [
        'category',
        'term_name',
        'percentage',
    ];

    /**
     * Get the appraisal configurations that use this term
     */
    public function appraisalConfigurations()
    {
        return $this->belongsToMany(hr_appraisal_conf::class, 'hr_appraisal_conf_terms', 'term_id', 'appraisal_conf_id');
    }
} 