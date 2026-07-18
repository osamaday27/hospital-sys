<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetbrand extends Model
{
    use SoftDeletes;
    protected $table = 'asset_brands';
    protected $fillable = ['name','code','is_active'];

    public function models(){ return $this->hasMany(assetmodel::class, 'brand_id'); }
}
