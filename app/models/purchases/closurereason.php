<?php

namespace App\Models\purchases;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class closurereason extends Model
{
    use SoftDeletes;
    protected $table = 'closure_reasons';

    protected $fillable = [
        'name_ar',
        'name_en',
        'status',
    ];
}
