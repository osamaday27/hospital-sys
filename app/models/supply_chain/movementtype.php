<?php

namespace App\Models\supply_chain;




use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class movementtype extends Model
{
    use SoftDeletes;
    protected $table = 'movement_types';

    protected $fillable = [
        'impact_balance',
        'movement_type',
        'unit',
    ];
}

