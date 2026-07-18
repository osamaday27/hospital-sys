<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class disposalrequestitem extends Model
{
    use SoftDeletes;

    protected $table = 'disposal_request_items';

    protected $fillable = [
        'request_id','asset_id',
        'qty_requested','qty_approved','qty_executed',
        'snapshot_cost','snapshot_accum_dep','snapshot_nbv','snapshot_residual',
        'remarks'
    ];

    protected $casts = [
        'qty_requested'     => 'integer',
        'qty_approved'      => 'integer',
        'qty_executed'      => 'integer',
        'snapshot_cost'     => 'decimal:2',
        'snapshot_accum_dep'=> 'decimal:2',
        'snapshot_nbv'      => 'decimal:2',
        'snapshot_residual' => 'decimal:2',
    ];

    public function request(){ return $this->belongsTo(DisposalRequest::class, 'request_id'); }
    public function asset(){ return $this->belongsTo(assetitem::class, 'asset_id'); }
}
