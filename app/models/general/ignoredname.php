<?php

namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ignoredname extends Model
{
    protected $table = 'ignored_names';

    protected $fillable = [
        'name',
    ];
}