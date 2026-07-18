<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
use App\models\finance\contract_last_level;
use App\models\general\nationalities;
use App\models\general\city;
use App\models\general\countries;
use App\models\general\area;
use App\models\general\government;
use App\Traits\patienttrackingtrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class medical_record extends Model
{
    use HasFactory, SoftDeletes, TracksChanges, patienttrackingtrait;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'patient';

    // تحديد الأعمدة القابلة للتعيين mass assignable
 protected $fillable = [
    'medical_number',
    'full_name',
    'first_name_ar', 'second_name_ar', 'third_name_ar', 'fourth_name_ar',
    'first_name_en', 'second_name_en', 'third_name_en', 'fourth_name_en',
    'date_of_birth', 'gender', 'marital_status',
    'phone_number', 'phone_number2', 'whatsapp', 'email',
    'national_id', 'passport',
    'blood_type', 'allergies', 'chronic_diseases',
    'the_best_way_to_communicate', 'insurance_contracting',
    'nationality', 'country', 'governorate', 'city', 'area',
    'full_address',
    'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation','emergency_contact_address',
    'jop_id', 'how_to_identify_place_id',
    'status', 'data_confirmed', 'notes',
    'img', 'default_branch', 'user_id', 'account_id'
];

    // تحديد الأعمدة التي سيتم تحديد تاريخ الإنشاء وتاريخ التعديل لها
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */
    public function contract()
    {
        return $this->belongsTo(contract_last_level::class, 'insurance_contracting');
    }

    public function nationalities()
    {
        return $this->belongsTo(nationalities::class, 'nationality');
    }

    public function countries()
    {
        return $this->belongsTo(countries::class, 'country');
    }

    public function government()
    {
        return $this->belongsTo(government::class, 'governorate');
    }

    public function getcity()
    {
        return $this->belongsTo(city::class, 'city');
    }

    public function getarea()
    {
        return $this->belongsTo(area::class, 'area');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(patient_visits::class, 'patient_id');
    }

    /**
     * العلاقة مع الملاحظات
     */
    public function notes(): HasMany
    {
        return $this->hasMany(note::class, 'patient_id');
    }

    /*
    |--------------------------------------------------------------------------
    | خصائص مشتقة
    |--------------------------------------------------------------------------
    */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }

        $dob = Carbon::parse($this->date_of_birth);
        $now = Carbon::now();
        $diff = $dob->diff($now);

        return sprintf("%d سنة, %d شهر, %d يوم", $diff->y, $diff->m, $diff->d);
    }

    /*
    |--------------------------------------------------------------------------
    | أدوات مساعدة داخلية
    |--------------------------------------------------------------------------
    */
    /**
     * تنظيف رقم الهاتف: إزالة أي شيء غير أرقام (0-9)
     */
    protected static function normalizeDigits(?string $value): ?string
    {
        if (!$value) return $value;
        // إزالة كل ما ليس رقماً (تشمل المسافات والرموز وعلامات +)
        return preg_replace('/\D+/', '', $value);
    }

    /**
     * تجهيز رقم طبي padded (مثلاً 0001) للمقارنة لو أنت بتخزنه بدون padding
     */
    protected static function possibleMedicalNumbers(?string $term): array
    {
        if (!$term) return [];
        $onlyDigits = preg_replace('/\D+/', '', $term);
        if ($onlyDigits === '') return [];
        // جرّب أكثر من طول padding حسب مشروعك
        return [
            $onlyDigits,
            str_pad($onlyDigits, 4, '0', STR_PAD_LEFT),
            str_pad($onlyDigits, 6, '0', STR_PAD_LEFT),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | سكوب البحث الأساسي
    |--------------------------------------------------------------------------
    */
    /**
     * scopeSearch: بحث ذكي في المريض بالاسم/القومي/الهاتف/الرقم الطبي/جواز/واتساب
     * - يدعم تقسيم كلمات الاسم (full_name) للبحث الجزئي
     * - يجمع شروط OR في مجموعة واحدة لتجنب أخطاء المنطق
     * - يطبع الهواتف بدون رموز للمقارنة
     */

      public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '' || mb_strlen($term) < 2) {
            return $query;
        }

        $like   = '%' . str_replace('%', '\%', $term) . '%';
        $digits = self::normalizeDigits($term);
        $medicalVariants = self::possibleMedicalNumbers($term);

        $nameTokens = collect(preg_split('/\s+/u', $term, -1, PREG_SPLIT_NO_EMPTY))
            ->filter(fn ($t) => mb_strlen($t) >= 2)
            ->values();

        return $query->where(function ($q) use ($like, $digits, $medicalVariants, $nameTokens, $term) {

            // الاسم الكامل
            $q->orWhere('full_name', 'like', $like);

            // كل جزء من الاسم (AND داخل full_name)
            if ($nameTokens->isNotEmpty()) {
                $q->orWhere(function ($qq) use ($nameTokens) {
                    foreach ($nameTokens as $tk) {
                        $qq->where('full_name', 'like', '%' . str_replace('%', '\%', $tk) . '%');
                    }
                });
            }

            // قومي / جواز
            $q->orWhere('national_id', 'like', $like)
              ->orWhere('passport', 'like', $like);

            // ==============================
            //    الرقم الطبي (المهم هنا)
            // ==============================

            // 1) جرّب المطابقة الخام زي ما المستخدم كتب (تدعم 66119-2024)
            $q->orWhere('medical_number', 'like', $like);

            // 2) لو استخدمنا Variants رقمية، جرّب مساواة مباشرة عليهم
            if (!empty($medicalVariants)) {
                $q->orWhereIn('medical_number', $medicalVariants);
            }

            // 3) مطابقة بدون رموز داخل SQL (تشيل مسافات/شرطات/أقواس/نقاط/زائد)
            if ($digits) {
                $q->orWhereRaw("
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(IFNULL(medical_number,''),' ',''),'-',''),'(',''),')',''),'+',''),'.','')
                    LIKE ?
                ", ['%' . $digits . '%']);
            }

            // هواتف مطبّعة
            if ($digits) {
                $q->orWhereRaw("
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(IFNULL(phone_number,''),' ',''),'-',''),'(',''),')',''),'+',''),'.','')
                    LIKE ?
                ", ['%' . $digits . '%'])
                  ->orWhereRaw("
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(IFNULL(phone_number2,''),' ',''),'-',''),'(',''),')',''),'+',''),'.','')
                    LIKE ?
                  ", ['%' . $digits . '%'])
                  ->orWhereRaw("
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(IFNULL(whatsapp,''),' ',''),'-',''),'(',''),')',''),'+',''),'.','')
                    LIKE ?
                  ", ['%' . $digits . '%']);
            } else {
                $q->orWhere('phone_number', 'like', $like)
                  ->orWhere('phone_number2', 'like', $like)
                  ->orWhere('whatsapp', 'like', $like);
            }
        });
    }
public function job()
{
    return $this->belongsTo(jops::class, 'jop_id');
}
public function getMaritalStatusTextAttribute()
{
    $statuses = [
        1 => 'أعزب',
        2 => 'متزوج',
        3 => 'أرمل',
        4 => 'مطلق',
    ];

    return $statuses[$this->marital_status] ?? '-';
}

}