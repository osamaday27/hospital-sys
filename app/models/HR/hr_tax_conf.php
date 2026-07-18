<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class hr_tax_conf extends Model
{
    use HasFactory;
    protected $table = 'hr_tax_conf';


    protected $fillable = [
        'name',
        'status',
        'exemption_margin',
        'segment_1',
        'segment_2',
        'segment_3',
        'segment_4',
        'segment_5',
        'segment_6',
        'segment_7',
        'segment_8',
        'segment_9',
        'segment_10',
    ];

    protected $attributes = [
        'status' => false,
        'exemption_margin' => 20000.00,
        'segment_1' => 40000.00,
        'segment_2' => 55000.00,
        'segment_3' => 70000.00,
        'segment_4' => 200000.00,
        'segment_5' => 400000.00,
        'segment_6' => 600000.00,
        'segment_7' => 700000.00,
        'segment_8' => 800000.00,
        'segment_9' => 900000.00,
        'segment_10' => 1200000.00,
    ];


    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // When enabling a configuration, disable all others
            if ($model->status) {
                self::where('id', '!=', $model->id)->update(['status' => false]);
            }
        });
    }
    
}