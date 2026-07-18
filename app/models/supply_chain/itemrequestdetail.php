<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRequestDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'item_request_details';

    protected $fillable = [
        'item_request_id',
        'item_id',
        'status',
        'notes',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // العلاقة مع الطلب الرئيسي
    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    // العلاقة مع الصنف
    public function item()
    {
        return $this->belongsTo(productdata::class, 'item_id');
    }
}
