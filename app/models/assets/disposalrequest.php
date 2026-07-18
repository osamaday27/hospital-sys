<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class disposalrequest extends Model
{
    use SoftDeletes;

    protected $table = 'disposal_requests';

    protected $fillable = [
        'request_no','method','status','reason','notes',
        'requested_at','submitted_at','approved_at','executed_at','closed_at',
        'requested_by','submitted_by','created_by','updated_by'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'executed_at'  => 'datetime',
        'closed_at'    => 'datetime',
    ];

    public function items(){ return $this->hasMany(DisposalRequestItem::class, 'request_id'); }
    public function approvals(){ return $this->hasMany(DisposalApproval::class, 'request_id')->where('context','disposal')->orderBy('level'); }
    public function disposal(){ return $this->hasOne(Disposal::class, 'request_id'); }
    public function attachments(){ return $this->hasMany(AssetDisposalAttachment::class, 'request_id'); }
}
