<?php

namespace App\Models\supply_chain;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class storekeepermodel extends Model
{
    use HasFactory;
    protected $table = 'store_keepers';

    protected $fillable = [
        'store_id',
        'user_id',
        'can_issue',
        'can_add',
        'can_inventory',
        'can_report',
        'is_main_keeper',
        'status',
    ];

    // العلاقة مع جدول المخازن
    public function store()
    {
        return $this->belongsTo(\App\Models\supply_chain\StoreMainData::class, 'store_id');
    }

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
