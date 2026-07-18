<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class radiologydevicessettings extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'rad_s_devices';

    protected $fillable = [
        'name', 'description', 'device_type_id', 'room_id',
        'status', 'installation_date', 'maintenance_date', 'vendor', 'serial_no'
    ];

    public $translatable = ['name', 'description'];

    // 🔗 العلاقات
    public function type()
    {
        return $this->belongsTo(radiologydevicestypessettings::class, 'device_type_id');
    }

    public function room()
    {
        return $this->belongsTo(radiologyroomssettings::class, 'room_id');
    }
}
