<?php
namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class storeItemRequest extends Model
{
    protected $table = 'store_item_requests_stores';

    protected $fillable = [
        'store_to_id',
        'store_id',
        'request_date',
        'reason',
        'status',
        'request_number',
        'created_by',
    ];

    // المخزن المصدر
    public function store()
    {
        return $this->belongsTo(StoreMainData::class, 'store_id');
    }

    // المخزن الوجهة (تصحيح المفتاح)
    public function store_to()
    {
        return $this->belongsTo(StoreMainData::class, 'store_to_id');
    }

    // منشئ الطلب
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // التفاصيل (الاسم الأصلي)
    public function details()
    {
        return $this->hasMany(storeItemRequestDetail::class, 'request_id');
    }

    // alias باسم items عشان with(['items.product','items.unit']) يشتغل
    public function items()
    {
        return $this->hasMany(storeItemRequestDetail::class, 'request_id');
    }
}