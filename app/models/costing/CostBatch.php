<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBatch extends Model
{
    use SoftDeletes;

    protected $table = 'cost_batches';

    protected $fillable = [
        'code','period_start','period_end','scope_type','scope_ids',
        'status','direct_count','overhead_count','direct_total','overhead_total',
        'grand_total','notes','user_id',
    ];

    protected $casts = [
        'scope_ids' => 'array',
        'period_start' => 'date',
        'period_end'   => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(CostTransaction::class, 'batch_id');
    }
}