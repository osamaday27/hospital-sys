<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\models\administrative\adm_b_surgery_bookings;
use App\Models\User;

class surgerystatushistory extends Model
{
    use HasFactory;

    protected $table = 'adm_b_surgery_status_history';

    protected $fillable = [
        'surgery_booking_id',
        'status',
        'note',
        'status_changed_at',
        'changed_by',
    ];

    protected $dates = ['status_changed_at'];

    // العلاقة مع الحجز الجراحي
    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'surgery_booking_id');
    }

    // المستخدم الذي غيّر الحالة
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // اسم الحالة مترجم
    public function getTranslatedStatusAttribute()
    {
        return trans('surgical_trans.status_' . $this->status);
    }
}