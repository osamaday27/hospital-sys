<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;

class CostTransaction extends Model
{
    protected $table = 'cost_transactions';

    protected $fillable = [
        'batch_id','case_type','case_id','service_id','cost_center_id',
        'line_type','item_type','description','qty','unit_cost','total_cost','meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(CostBatch::class, 'batch_id');
    }
}