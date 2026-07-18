<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetdepreciationline extends Model
{
    use SoftDeletes;

    protected $table = 'asset_depreciation_lines';

    protected $fillable = [
        'run_id','asset_id',
        'dep_method','useful_life_months','dep_rate','residual_value','cost_basis','qty',
        'uop_estimated_total','uop_units_period',
        'amount','accum_before','accum_after','nbv_before','nbv_after',
        'location_id','branch_id','in_service_date','remarks',
        'created_by'
    ];

    protected $casts = [
        'in_service_date' => 'date',
        'amount'          => 'decimal:2',
        'accum_before'    => 'decimal:2',
        'accum_after'     => 'decimal:2',
        'nbv_before'      => 'decimal:2',
        'nbv_after'       => 'decimal:2',
        'residual_value'  => 'decimal:2',
        'cost_basis'      => 'decimal:2',
    ];

    public function run()   { return $this->belongsTo(assetdepreciationrun::class, 'run_id'); }
    public function asset() { return $this->belongsTo(assetitem::class, 'asset_id'); }
}
