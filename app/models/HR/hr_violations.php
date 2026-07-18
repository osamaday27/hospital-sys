<?php
namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class hr_violations extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'violation_name_en',
        'violation_name_ar'
    ];

}