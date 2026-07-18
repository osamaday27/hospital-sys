<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'item_requests';

    protected $fillable = [
        'request_number',
        'request_date',
        'user_id',
        'store_id',

    ];

    protected $dates = [
        'request_date',
        'deleted_at',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(\App\models\User::class);
    }

    // العلاقة مع تفاصيل الطلب
    public function details()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }
        // العلاقة مع المستخدم
    public function store()
    {
        return $this->belongsTo(storemaindata::class);
    }
}
