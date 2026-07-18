<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class radiologydevicestypessettings extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'rad_s_devicestypes';

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'status',
    ];

    public $translatable = ['name', 'description','category'];
     public function devices()
    {
        return $this->hasMany(radiologydevicessettings::class, 'device_type_id');
    }
}
