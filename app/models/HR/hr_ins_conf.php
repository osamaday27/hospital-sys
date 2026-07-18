<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class hr_ins_conf extends Model
{
    use HasFactory;
    protected $table = 'hr_ins_conf';


    protected $fillable = [
        'name',
        'status',
        'employee_share',
        'company_share',
        'social_insurance_margin',
    ];

    protected $attributes = [
        'status' => false,
        'employee_share' => 11.00,
        'company_share' => 18.75,
        'social_insurance_margin' => 12600.00,
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