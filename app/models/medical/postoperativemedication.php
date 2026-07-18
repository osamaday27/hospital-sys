<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class postoperativemedication extends Model
{
    protected $fillable = [
        'plan_id',
        'medication',
        'dose',
        'route',
        'frequency',
        'duration',
    ];
    protected $table = 'post_operative_medications';

    public function plan()
    {
        return $this->belongsTo(PostOperativePlan::class, 'plan_id');
    }
}