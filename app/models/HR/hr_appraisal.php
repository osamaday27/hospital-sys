<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_appraisal extends Model
{
    use HasFactory;

    protected $table = 'hr_appraisal';

    protected $fillable = [
        'employee_id',
        'employee_code',
        'job_id',
        'department',
        'hiring_date',
        'appraisal_interval',
    ];

    protected $casts = [
        'hiring_date' => 'date',
    ];

    /**
     * Get the employee associated with this appraisal
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the job associated with this appraisal
     */
    public function job()
    {
        return $this->belongsTo(jobs::class, 'job_id');
    }

    /**
     * Get the department associated with this appraisal
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    /**
     * Get the appraisal details for this appraisal
     */
    public function details()
    {
        return $this->hasMany(hr_appraisal_details::class, 'appraisal_id');
    }

    /**
     * Get the appraisal configuration for this job
     */
    public function appraisalConfig()
    {
        return $this->belongsTo(hr_appraisal_conf::class, 'job_id', 'job_id');
    }

    /**
     * Calculate total achieved percentage
     */
    public function getTotalAchievedPercentageAttribute()
    {
        return $this->details->sum('achieved_percentage');
    }

    /**
     * Calculate total difference
     */
    public function getTotalDifferenceAttribute()
    {
        return $this->details->sum('difference');
    }
} 