<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\admsclinic;
use App\models\hr\Employee;
use App\Traits\TracksChanges;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\general\specialties;

class ms_allergies_list extends Model
{
   use HasFactory, SoftDeletes, TracksChanges,HasTranslations;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'm_s_allergies_list';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'name', 'code', 'description', 'type', 'severity','specialization','status','user_id'
    ];
     public $translatable = ['name'];
       // العلاقة مع التخصصات
    public function specialty()
    {
        return $this->belongsTo(specialties::class, 'specialization');
    }
}