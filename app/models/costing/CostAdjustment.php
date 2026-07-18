<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostAdjustment extends Model
{
    use SoftDeletes;

    protected $table = 'cost_adjustments';

    protected $fillable = [
        'batch_id',
        'case_type','case_id',
        'service_id','cost_center_id',
        'line_type','item_type',
        'adj_type','amount',
        'effective_date','reference','reason',
        'status','user_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'amount' => 'decimal:6',
    ];

    public static function statuses(): array
    {
        return ['draft','approved','posted'];
    }

    public static function caseTypes(): array
    {
        return ['opd','inpatient','surgery','lab','radiology','other'];
    }

    public static function lineTypes(): array
    {
        return ['direct','overhead'];
    }

    public static function itemTypes(): array
    {
        return ['supply','drug','resource','misc','overhead'];
    }

    public static function adjTypes(): array
    {
        return ['increase','decrease'];
    }

    // علاقات
    public function batch() { return $this->belongsTo(CostBatch::class, 'batch_id'); }
}