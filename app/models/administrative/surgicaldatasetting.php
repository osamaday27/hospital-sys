<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\administrative\admsclinic;
use App\models\finance\service;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class surgicaldatasetting extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    protected $table = 'adm_s_surgicaldatasettings';

    protected $fillable = ['service_id', 'name_ar', 'name_en', 'description_ar', 'description_en', 'category', 'technique', 'expected_duration', 'complexity', 'possible_complications_ar', 'possible_complications_en', 'preparation_instructions_ar', 'preparation_instructions_en', 'pre_op_tests', 'pre_op_medications', 'blood_compatibility_test', 'surgical_method', 'surgical_tools', 'anesthesia_type', 'anesthesia_medications', 'anesthesia_duration', 'ventilation_method'];

    protected $casts = [
        'pre_op_tests' => 'array',
        'pre_op_medications' => 'array',
        'surgical_tools' => 'array',
        'anesthesia_medications' => 'array',
    ];

    /**
     * علاقة مع جدول الخدمات المالية (fin_services)
     */
    public function services()
    {
        return $this->belongsTo(service::class, 'service_id');
    }
}