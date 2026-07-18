<?php
namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class specialties extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;

    protected $fillable = ['name_ar', 'name_en', 'logo', 'status', 'user_id'];
    protected $table = 'specialties';
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id(); // تعيين معرف المستخدم الحالي
            }
        });
    }

    public function parentList()
    {
        return $this->belongsTo(self::class, 'parent_id'); // إذا كان هناك علاقة أب-ابن
    }
}