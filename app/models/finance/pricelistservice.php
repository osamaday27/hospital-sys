<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\Finance\price_lists;
use App\models\finance\service;
use App\models\User;

class pricelistservice extends Model
{
    use TracksChanges, SoftDeletes;

    // اسم الجدول
    protected $table = 'fin_price_list_service';

    // الأعمدة القابلة للملء
    protected $fillable = [
        'price_list_id',  // معرف القائمة
        'service_id',     // معرف الخدمة
        'hospital_price', // سعر المستشفى
        'special_price',  // السعر الخاص
        'status',         // الحالة (مفعلة/غير مفعلة)
        'user_id',        // معرف المستخدم الذي أنشأ السجل
    ];

    // تفعيل SoftDeletes
    protected $dates = ['deleted_at'];

    // العلاقة مع قائمة الأسعار
    public function priceList()
    {
        return $this->belongsTo(price_lists::class, 'price_list_id');
    }

    // العلاقة مع الخدمات
    public function service()
    {
        return $this->belongsTo(service::class, 'service_id');
    }

    // العلاقة مع المستخدم (القائم بالإنشاء)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // تخصيص عرض الحالة
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? trans('status.active') : trans('status.inactive');
    }
}