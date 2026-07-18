<?php

namespace App\Models\purchases;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class deliveryplace extends Model
{
    use SoftDeletes;
    protected $table = 'delivery_places';

    protected $fillable = [
        'name_ar',
        'name_en',
        'status',
    ];
}
