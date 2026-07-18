<?php

namespace App\models\general;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;

class branch extends Model
{
    use HasTranslations, TracksChanges, SoftDeletes;
    protected $fillable = ['name', 'address', 'city', 'phone', 'email','status','user_id'];
    // الحقول القابلة للترجمة
    public $translatable = ['name'];

    // تفعيل SoftDeletes
    protected $dates = ['deleted_at'];
    // اسم الجدول
    protected $table = 'branches';
}