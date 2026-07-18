<?php


namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class returnreasonmodel extends Model
{
    use SoftDeletes;
    protected $table = 'return_reasons';

    protected $fillable = [
        'name_en',
        'name_ar',
        'status',
    ];
}
