<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\adm_s_floors;
use App\models\general\nursingstation;
use App\models\general\adm_s_residencies;

class adm_s_rooms extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $fillable = ['name', 'type', 'floor_id', 'nursing_stations_id', 'user_id', 'status','residency_id'];
    protected $table = 'adm_s_rooms';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    // العلاقة مع نموذج Building
    public function floor()
    {
        return $this->belongsTo(adm_s_floors::class, 'floor_id'); // تحديد العلاقة
    }
    public function nursingstation()
    {
        return $this->belongsTo(nursingstation::class, 'nursing_stations_id'); // تحديد العلاقة
    }
    public function residencies()
    {
        return $this->belongsTo(adm_s_residencies::class, 'residency_id'); // تحديد العلاقة
    }
    // العلاقة بين الغرفة والأسرة
    public function beds()
    {
        return $this->hasMany('App\models\general\adm_s_beds', 'room_id', 'id');
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