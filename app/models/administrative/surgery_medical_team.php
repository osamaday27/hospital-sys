<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\hr\employee;
use App\models\user;
use App\models\administrative\adm_b_surgery_bookings;

class surgery_medical_team extends Model
{
    use HasFactory, SoftDeletes;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'adm_b_surgery_medical_team';

    // الحقول القابلة للتعديل (mass assignment)
    protected $fillable = [
        'surgery_bookings_id', 
        'medical_team_id', 
        'user_id', 
        'role', 
        'status'
    ];

    // العلاقة مع `adm_b_surgery_bookings` (حجز الجراحة)
    public function booking()
    {
        return $this->belongsTo(adm_b_surgery_bookings::class, 'surgery_bookings_id');
    }

    // العلاقة مع `employee` (الفريق الطبي)
    public function medical_team()
    {
        return $this->belongsTo(employee::class, 'medical_team_id');
    }

    // العلاقة مع `user` (المستخدم الذي أضاف أو عين الفريق)
    public function user()
    {
        return $this->belongsTo(user::class, 'user_id');
    }
    // العلاقة مع `employee` (المستخدم الذي أضاف أو عين الفريق)
    public function employee()
    {
        return $this->belongsTo(employee::class, 'medical_team_id');
    }
    // داخل App\models\administrative\surgery_medical_team


}