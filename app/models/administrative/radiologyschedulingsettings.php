<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class radiologyschedulingsettings extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'rad_s_schedulingsettings';

    protected $fillable = [
        'name',
        'description',
        'room_id',
        'device_id',
        'shift_type',
        'start_time',
        'end_time',
        'is_holiday',
        'is_maintenance',
        'status',
    ];

    public $translatable = ['name', 'description'];

    // العلاقات
    public function room()
    {
        return $this->belongsTo(radiologyroomssettings::class, 'room_id');
    }

    public function device()
    {
        return $this->belongsTo(radiologydevicessettings::class, 'device_id');
    }
    
}
