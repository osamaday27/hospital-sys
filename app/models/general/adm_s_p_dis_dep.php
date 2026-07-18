<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\hr\Department;
class adm_s_p_dis_dep extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $fillable = ['hr_department_id','created_by','status'];
    protected $table = 'adm_s_p_dis_dep';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
      // العلاقة مع نموذج Building
  
        // علاقة مع القوائم الأخرى إذا كانت هناك مستويات متعددة
         public function departments()
    {
        return $this->belongsTo(Department::class, 'hr_department_id'); // تحديد العلاقة
    }
    public function parentList()
    {
        return $this->belongsTo(self::class, 'parent_id'); // إذا كان هناك علاقة أب-ابن
    }
    public function childLists()
    {
        return $this->hasMany(self::class, 'parent_id'); // القوائم الفرعية
    }
}