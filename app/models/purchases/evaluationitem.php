<?php

namespace App\Models\purchases;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class evaluationitem extends Model
{
    use SoftDeletes;
    protected $table = 'evaluation_items';

    protected $fillable = [
        'name_ar',
        'name_en',
        'status',
    ];
}
