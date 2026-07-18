<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_appraisal_details extends Model
{
    use HasFactory;

    protected $table = 'hr_appraisal_details';

    protected $fillable = [
        'appraisal_id',
        'term_id',
        'achieved_percentage',
        'difference',
    ];

    protected $casts = [
        'achieved_percentage' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    /**
     * Get the appraisal that owns this detail
     */
    public function appraisal()
    {
        return $this->belongsTo(hr_appraisal::class, 'appraisal_id');
    }

    /**
     * Get the term associated with this detail
     */
    public function term()
    {
        return $this->belongsTo(hr_appraisal_terms_conf::class, 'term_id');
    }

    /**
     * Calculate difference when achieved percentage is set
     */
    public function setAchievedPercentageAttribute($value)
    {
        $this->attributes['achieved_percentage'] = $value;
        
        if ($this->term && $this->term->percentage) {
            $this->attributes['difference'] = $value - $this->term->percentage;
        }
    }
} 