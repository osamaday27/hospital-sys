<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetdisposalattachment extends Model
{
    use SoftDeletes;

    protected $table = 'asset_disposal_attachments';

    protected $fillable = [
        'request_id','type','file_path','original_name','notes','uploaded_by'
    ];

    public function request(){ return $this->belongsTo(DisposalRequest::class, 'request_id'); }
}
