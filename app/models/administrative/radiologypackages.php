<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class radiologypackages extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'rad_s_packages';
    protected $guarded = [];

    // ✅ الحقول المترجمة
    public $translatable = ['name', 'description', 'services'];

    // ✅ كاست للـ JSON
    protected $casts = [
        'name'        => 'array',
        'description' => 'array',
        'services'    => 'array',
    ];
}
