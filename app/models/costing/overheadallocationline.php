<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class overheadallocationline extends Model
{
    use SoftDeletes;

    protected $table = 'overhead_allocation_lines';

    protected $fillable = [
        'rule_id','cost_center_id','percent','driver_value','allocated_amount',
    ];

    protected $casts = [
        'percent' => 'decimal:4',
        'driver_value' => 'decimal:4',
        'allocated_amount' => 'decimal:2',
    ];

    public function rule()
    {
        return $this->belongsTo(overheadallocationrule::class, 'rule_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(costcenter::class, 'cost_center_id');
    }
}