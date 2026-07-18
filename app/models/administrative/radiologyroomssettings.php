<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;


class radiologyroomssettings extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'rad_s_rooms';

    protected $fillable = [
        'code',
        'name',
        'building_id',
        'floor_id',
        'branch_id',
        'status',
        'capacity_per_hour',
    ];
    public $translatable = ['name'];

    // ✅ خلي name يتحول تلقائيًا إلى array
    protected $casts = [
        'name' => 'array',
    ];

    // العلاقات
    public function building()
    {
        return $this->belongsTo(\App\models\general\adm_s_buildings::class, 'building_id');
    }

    public function floor()
    {
        return $this->belongsTo(\App\models\general\adm_s_floors::class, 'floor_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\models\general\branch::class, 'branch_id');
    }
      public function devices()
    {
        return $this->hasMany(radiologydevicessettings::class, 'room_id');
    }
}
