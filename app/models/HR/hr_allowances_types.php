<?php

namespace App\models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_allowances_types extends Model
{
    use HasFactory;

    protected $table = 'hr_allowances_types';

    protected $fillable = [
        'name_ar',
        'name_en',
    ];
} 