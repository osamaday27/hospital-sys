<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetcategory extends Model
{
    use SoftDeletes;
    protected $table = 'asset_categories';
    protected $fillable = [
        'parent_id','name_ar','name_en','code','useful_life_months','default_dep_method','default_dep_rate','is_active'
    ];

    public function parent(){ return $this->belongsTo(self::class, 'parent_id'); }
    public function children(){ return $this->hasMany(self::class, 'parent_id'); }
}
