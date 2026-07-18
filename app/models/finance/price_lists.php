<?php

namespace App\models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\general\branch;
use App\models\general\currencies;
use App\models\User;
class price_lists extends Model
{
    use HasTranslations, TracksChanges, SoftDeletes;

    // اسم الجدول
    protected $table = 'fin_price_lists';

    // الأعمدة القابلة للملء
    protected $fillable = [
        'name', // اسم اللائحة (عربي/إنجليزي)
        'fiscal_year', // السنة المالية
        'branches', // الفروع المرتبطة
        'start_date', // تاريخ البداية
        'end_date', // تاريخ النهاية
        'list_type', // نوع القائمة
        'default_branch', // الفرع القائم بالإنشاء
        'user_id', // المستخدم الحالي
        'status', // الحالة (مثلاً مفعّلة أو غير مفعّلة)
        'currency', // الحالة (مثلاً مفعّلة أو غير مفعّلة)
    ];

    // الحقول القابلة للترجمة
    public $translatable = ['name'];

    // تفعيل SoftDeletes
    protected $dates = ['deleted_at'];

    // علاقة مع الفروع (اختياري: إذا كان هناك جدول فروع)

    // علاقة مع المستخدم (للمستخدم القائم بالإنشاء)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
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
    public function branch()
    {
        return $this->belongsTo(branch::class, 'branches');
    }

    public function getcurrency()
    {
        return $this->belongsTo(currencies::class, 'currency');
    }
}