<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\adm_s_floors;
use App\models\hr\Employee;
use App\models\general\adm_s_rooms;

class nursingstation extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $table = 'adm_s_nursing_stations';
    protected $fillable = ['name_ar', 'name_en', 'floor_id', 'room_count', 'manager_id', 'type', 'status', 'notes', 'created_by'];

    public $timestamps = true;
    protected $dates = ['deleted_at'];
    // العلاقة مع نموذج Building
    public function floor()
    {
        return $this->belongsTo(adm_s_floors::class, 'floor_id'); // تحديد العلاقة
    }
    // علاقة مع القوائم الأخرى إذا كانت هناك مستويات متعددة
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
    // علاقة مع القوائم الأخرى إذا كانت هناك مستويات متعددة
    public function parentList()
    {
        return $this->belongsTo(self::class, 'id'); // إذا كان هناك علاقة أب-ابن
    }
    public function childLists()
    {
        // تأكد من استخدام العمود الصحيح في العلاقة
        return $this->hasMany(adm_s_floors::class, 'floor_id'); // هنا يجب أن يكون 'floor_id' هو العمود الذي يربط الغرف بالطوابق
    }
    public function rooms()
    {
        return $this->hasMany(adm_s_rooms::class, 'nursing_stations_id');
    }
}