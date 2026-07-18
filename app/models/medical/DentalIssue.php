<?php
namespace App\models\Medical;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\User; // تأكد من استيراد نموذج User

class DentalIssue extends Model
{
    use HasFactory, TracksChanges;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'm_d_dental_issues';

    // الحقول القابلة للتعبئة (Mass Assignment)
    protected $fillable = [
        'patient_id',
        'id_visit',
        'id_reservation',
        'type_visit',
        'tooth', // أضف هذا
        'complaint',
        'diagnosis',
        'treatment_plan',
        'missing',
        'filled',
        'crowned',
        'abscess',
        'implant', // أضف هذا
        'extracted', // أضف هذا
        'history',
        'request_type',
        'service_id',
        'request_date',
        'status',
        'user_id', // حقل الـ user_id
    ];

    // التحويل التلقائي
    protected $casts = [
        'affected_surfaces' => 'array', // تحويل الحقل إلى Array تلقائيًا
    ];

    // العلاقة بين DentalIssue و User (كل سجل في DentalIssue يحتوي على user_id)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // تحديد العلاقة مع نموذج User
    }
}