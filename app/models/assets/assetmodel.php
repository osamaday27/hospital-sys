<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetmodel extends Model
{
    use SoftDeletes;
    protected $table = 'asset_models';
    protected $fillable = ['brand_id','name','code','is_active'];

    public function brand(){ return $this->belongsTo(assetbrand::class, 'brand_id'); }
}
