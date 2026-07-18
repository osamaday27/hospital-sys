<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\adm_s_floors;
class adm_s_beds extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $fillable = ['name', 'type', 'room_id',  'user_id','status'];
    protected $table = 'adm_s_beds';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
      // العلاقة مع نموذج Building
    public function rooms()
    {
        return $this->belongsTo(adm_s_rooms::class, 'room_id');  // تحديد العلاقة
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