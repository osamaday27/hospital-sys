<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class adm_s_residencies extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $fillable = ['name_ar', 'name_en', 'type', 'created_by','status'];
    protected $table = 'adm_s_residencies';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
      // العلاقة مع نموذج Building
  
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