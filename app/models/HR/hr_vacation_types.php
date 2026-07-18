<?php

namespace App\models\hr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class hr_vacation_types extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'take_vacation_in',
        'approval_type',
        'has_allocation',
        'no_salary'
    ];

}