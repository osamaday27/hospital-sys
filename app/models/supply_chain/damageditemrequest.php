<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class damageditemrequest extends Model
{
    use SoftDeletes;

    protected $table = 'damaged_item_requests';

    protected $fillable = [
        'request_number',
        'store_id',
        'request_date',
        'reason',
        'user_id',
        'status',
    ];

    public function store()
    {
        return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items()
    {
        return $this->hasMany(damageditemrequestitem::class, 'request_id');
    }
}