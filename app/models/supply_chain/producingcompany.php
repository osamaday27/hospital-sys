<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class producingcompany extends Model
{
    use SoftDeletes;

    /**
     * الحقول القابلة للملء.
     *
     * @var array
     */
    protected $table = 'producing_companies';
    protected $fillable = [
        'name_en',
        'name_ar',
        'address',
        'notes'
    ];
}
