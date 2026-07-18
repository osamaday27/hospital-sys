<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetlocation extends Model
{
    use SoftDeletes;
    protected $table = 'asset_locations';
    protected $fillable = ['parent_id','type','name_ar','name_en','code','is_active'];

    public function parent(){ return $this->belongsTo(self::class, 'parent_id'); }
    public function children(){ return $this->hasMany(self::class, 'parent_id'); }
}
