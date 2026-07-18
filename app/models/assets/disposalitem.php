<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class disposalitem extends Model
{
    use SoftDeletes;

    protected $table = 'disposal_items';

    protected $fillable = [
        'disposal_id','request_item_id','asset_id','qty_executed',
        'exec_snapshot_cost','exec_snapshot_accum_dep','exec_snapshot_nbv','exec_snapshot_residual','notes'
    ];

    protected $casts = [
        'qty_executed'           => 'integer',
        'exec_snapshot_cost'     => 'decimal:2',
        'exec_snapshot_accum_dep'=> 'decimal:2',
        'exec_snapshot_nbv'      => 'decimal:2',
        'exec_snapshot_residual' => 'decimal:2',
    ];

    public function disposal(){ return $this->belongsTo(Disposal::class, 'disposal_id'); }
    public function requestItem(){ return $this->belongsTo(DisposalRequestItem::class, 'request_item_id'); }
    public function asset(){ return $this->belongsTo(assetitem::class, 'asset_id'); }
}
