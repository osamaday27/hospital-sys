<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\adm_s_floors;
class adm_s_buildings extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $fillable = ['name_ar', 'name_en', 'address', 'floors_count', 'latitude', 'longitude', 'user_id','status'];
    protected $table = 'adm_s_buildings';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    public function parentList()
    {
        return $this->belongsTo(self::class, 'parent_id'); // إذا كان هناك علاقة أب-ابن
    }
    public function floors()
{
    return $this->hasMany(adm_s_floors::class, 'building_id');
}

}