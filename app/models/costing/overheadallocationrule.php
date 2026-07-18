<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class overheadallocationrule extends Model
{
    use SoftDeletes;

    protected $table = 'overhead_allocation_rules';

    protected $fillable = [
        'overhead_pool_id','method','driver_code','total_amount',
        'total_percent','total_driver','status','notes','user_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_percent' => 'decimal:4',
        'total_driver' => 'decimal:4',
    ];

    public static function methods(): array
    {
        return ['percent','driver'];
    }

    public static function statuses(): array
    {
        return ['draft','approved','posted'];
    }

    public function pool()
    {
        return $this->belongsTo(overheadpool::class, 'overhead_pool_id');
    }

    public function lines()
    {
        return $this->hasMany(overheadallocationline::class, 'rule_id');
    }
}