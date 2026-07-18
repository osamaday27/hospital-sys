<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class radiologymedicalservicessettings extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'rad_s_medicalservices';

    protected $fillable = [
        'name',
        'description',
        'device_id',
        'instructions',
        'icd10_code',
        'cpt_code',
        'attachments',
        'status',
    ];

    public $translatable = ['name', 'description', 'instructions'];

    protected $casts = [
        'attachments' => 'array',
    ];

    // العلاقات
    public function device()
    {
        return $this->belongsTo(radiologydevicessettings::class, 'device_id');
    }
}
