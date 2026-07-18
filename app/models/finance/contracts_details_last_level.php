<?php
namespace App\models\finance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\models\finance\contract_last_level;
class contracts_details_last_level extends Model
{
    use HasFactory, TracksChanges, SoftDeletes,HasTranslations;

   // تحديد اسم الجدول
    protected $table = 'fin_contracts_details_last_level';

    // تحديد الحقول التي يمكن ملؤها (Mass Assignment)
    protected $fillable = [
        'last_level_id',
        'status',
        'user_id',
        'discount_percentage_all_services',
        'patient_contribution_all_services',
        'discount_percentage_clinic_services',
        'patient_contribution_clinic_services',
        'discount_percentage_inpatient_services',
        'patient_contribution_inpatient_services',
        'discount_percentage_surgical_services',
        'patient_contribution_surgical_services',
        'discount_percentage_lab_services',
        'patient_contribution_lab_services',
        'discount_percentage_emergency_services',
        'patient_contribution_emergency_services',
        'discount_percentage_rad_services',
        'patient_contribution_rad_services',
        'discount_percentage_medications',
        'patient_contribution_medications',
        'discount_percentage_supplies',
        'patient_contribution_supplies',
        'services_require_approval',
        'services_not_covered',
    ];

    // تحديد العلاقة مع نموذج "FinContractlastLevel"
    public function finContractlastLevel()
    {
        return $this->belongsTo(FinContractlastLevel::class, 'last_level_id');
    }

    // تحديد العلاقة مع نموذج المستخدم (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public $timestamps = true;
    public $translatable = ['name'];
    
}