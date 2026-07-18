<?php

namespace App\models\hr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class hr_public_holidays extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from',
        'to',
        'description'
    ];


}