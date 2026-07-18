<?php

namespace App\Models\supply_chain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class storageconditionmodel extends Model
{
    use SoftDeletes;
    protected $table = 'storage_conditions';

    protected $fillable = [
        'name_en',
        'name_ar',
        'status',
    ];
}
