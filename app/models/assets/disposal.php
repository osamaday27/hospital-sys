<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class disposal extends Model
{
    use SoftDeletes;

    protected $table = 'disposals';

    protected $fillable = [
        'request_id','method',
        'buyer_name','buyer_id_number','sale_invoice_no','sale_amount','vat_amount','fees_amount','net_amount',
        'donee_name','donation_doc_no',
        'destruction_method','destruction_doc_no','witnessed_by',
        'exec_snapshot_total_cost','exec_snapshot_total_accum_dep','exec_snapshot_total_nbv','exec_snapshot_total_residual',
        'executed_by','executed_at','posted_flag','posted_at','created_by','updated_by'
    ];

    protected $casts = [
        'sale_amount'                   => 'decimal:2',
        'vat_amount'                    => 'decimal:2',
        'fees_amount'                   => 'decimal:2',
        'net_amount'                    => 'decimal:2',
        'exec_snapshot_total_cost'      => 'decimal:2',
        'exec_snapshot_total_accum_dep' => 'decimal:2',
        'exec_snapshot_total_nbv'       => 'decimal:2',
        'exec_snapshot_total_residual'  => 'decimal:2',
        'executed_at'                   => 'datetime',
        'posted_at'                     => 'datetime',
        'posted_flag'                   => 'boolean',
    ];

    public function request(){ return $this->belongsTo(DisposalRequest::class, 'request_id'); }
    public function items(){ return $this->hasMany(DisposalItem::class, 'disposal_id'); }
}
