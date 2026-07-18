<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use App\models\User;
use App\models\general\currencies;
use App\Models\finance\glaccount;

class slocker extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $table = 'fin_s_locker';

    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'currency',
        'has_daily_limit',
        'daily_limit',          // تمت إضافته
        'status',               // تأكد من تعديل اسم العمود في قاعدة البيانات إذا كان "stauts"
        'gl_account_id',        // تمت إضافته
        'created_by',           // تمت إضافته
    ];

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    // ✅ العلاقة مع المستخدمين المرتبطين بالخزنة
    public function users()
    {
        return $this->belongsToMany(User::class, 'fin_s_locker_user', 'fin_s_locker_id', 'user_id');
    }

    // ✅ العلاقة مع العملة
    public function currencies()
    {
        return $this->belongsTo(currencies::class, 'currency')->withTrashed();
    }

    // ✅ العلاقة مع الحساب المحاسبي
    public function glAccount()
    {
        return $this->belongsTo(glaccount::class, 'gl_account_id');
    }

    // ✅ العلاقة مع المستخدم المنشئ
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}