<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class writeoffmodel extends Model
{
    use SoftDeletes;
    protected $table = 'write_offs';

    protected $fillable = [
        'name_en',
        'name_ar',
        'status',
    ];
}
