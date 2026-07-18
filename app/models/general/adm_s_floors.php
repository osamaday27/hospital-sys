<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\adm_s_buildings;
use App\models\general\adm_s_rooms;
use App\models\general\nursingstation;
class adm_s_floors extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $fillable = ['name_ar', 'name_en', 'type', 'rooms_count', 'building_id', 'user_id', 'status'];
    protected $table = 'adm_s_floors';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    // العلاقة مع نموذج Building
    public function building()
    {
        return $this->belongsTo(adm_s_buildings::class, 'building_id'); // تحديد العلاقة
    }
    // علاقة مع القوائم الأخرى إذا كانت هناك مستويات متعددة
    public function parentList()
    {
        return $this->belongsTo(self::class, 'id'); // إذا كان هناك علاقة أب-ابن
    }
    public function childLists()
    {
        // تأكد من استخدام العمود الصحيح في العلاقة
        return $this->hasMany(adm_s_rooms::class, 'floor_id'); // هنا يجب أن يكون 'floor_id' هو العمود الذي يربط الغرف بالطوابق
    }
    public function nursingStations()
    {
        return $this->hasMany(nursingstation::class, 'floor_id'); // تأكد من أن 'floor_id' هو العمود الصحيح في جدول nursingstations
    }
}