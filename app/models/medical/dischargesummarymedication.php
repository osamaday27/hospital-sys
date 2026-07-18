<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class DischargeSummaryMedication extends Model
{
    protected $fillable = [
        'discharge_summary_id',
        'medication_name',
        'dose_route',
        'frequency',
    ];
    protected $table = 'discharge_summary_medications';
    public function dischargeSummary()
    {
        return $this->belongsTo(\App\Models\DischargeSummary::class, 'discharge_summary_id');
    }
    
}