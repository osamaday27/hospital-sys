<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\models\administrative\radiologydevicestypessettings;
use App\models\general\specialties;

class radiologymedicalreports extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'rad_s_medicalreports';

    protected $fillable = [
        'title',
        'template',
        'device_type_id',
        'specialty_id',
        'instructions',
        'status',
        'doctor_signature',
        'allow_print',
        'allow_pdf',
        'allow_portal',
        'allow_email',
    ];

    public $translatable = ['title', 'instructions'];

    /**
     * 🔗 علاقة مع نوع الجهاز
     */
    public function deviceType()
    {
        return $this->belongsTo(
            radiologydevicestypessettings::class,   // الموديل
            'device_type_id',                      // المفتاح الأجنبي هنا
            'id',                                  // العمود الأساسي في جدول الأجهزة
            'deviceType'                           // اسم العلاقة
        );
    }

    /**
     * 🔗 علاقة مع التخصص
     */
    public function specialty()
    {
        return $this->belongsTo(
            specialties::class,    // الموديل
            'specialty_id',        // المفتاح الأجنبي هنا
            'id',                  // العمود الأساسي في جدول التخصصات
            'specialty'            // اسم العلاقة
        );
    }
}
