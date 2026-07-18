<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_buildings;
use App\Traits\Costing\HasCostCenters;
class adm_s_rooms_surgical extends Model
{
    use HasFactory, SoftDeletes, TracksChanges, HasCostCenters;
    protected $fillable = ['name_en', 'name_ar', 'type', 'building', 'floor', 'status', 'user_id',];
    protected $table = 'adm_s_rooms_surgical';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    // العلاقة مع نموذج Building
    public function floors()
    {
        return $this->belongsTo(adm_s_floors::class, 'floor'); // تحديد العلاقة
    }
     public function building()
    {
        return $this->belongsTo(adm_s_buildings::class, 'building'); // تحديد العلاقة
    }
  
 
    // علاقة مع القوائم الأخرى إذا كانت هناك مستويات متعددة
    public function parentList()
    {
        return $this->belongsTo(self::class, 'parent_id'); // إذا كان هناك علاقة أب-ابن
    }
    public function childLists()
    {
        return $this->hasMany(self::class, 'parent_id'); // القوائم الفرعية
    }

 

}