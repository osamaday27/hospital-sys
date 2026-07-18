<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class costcenterassignment extends Model
{
    use SoftDeletes;

    protected $table = 'cost_center_assignments';

    protected $fillable = [
        'cost_center_id',
        'assignable_type',
        'assignable_id',
        'status',
        'effective_from',
        'effective_to',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to'   => 'date',
    ];

    public function costcenter()
    {
        return $this->belongsTo(costcenter::class, 'cost_center_id');
    }

    public function assignable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }
}