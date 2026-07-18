<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_appraisal_conf extends Model
{
    use HasFactory;

    protected $table = 'hr_appraisal_conf';

    protected $fillable = [
        'job_id',
    ];

    /**
     * Get the job associated with this appraisal configuration
     */
    public function job()
    {
        return $this->belongsTo(jobs::class, 'job_id');
    }

    /**
     * Get the terms associated with this appraisal configuration
     */
    public function terms()
    {
        return $this->belongsToMany(hr_appraisal_terms_conf::class, 'hr_appraisal_conf_terms', 'appraisal_conf_id', 'term_id');
    }
} 