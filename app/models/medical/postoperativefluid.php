<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class postoperativefluid extends Model
{
    protected $fillable = [
        'plan_id',
        'fluid_name',
        'volume',
        'additive_name',
        'infusion_rate',
    ];
    protected $table = 'post_operative_fluids';

    public function plan()
    {
        return $this->belongsTo(PostOperativePlan::class, 'plan_id');
    }
}