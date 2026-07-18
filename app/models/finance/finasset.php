<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\User;

class finasset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_items';

    protected $fillable = [
        'name_ar',
        'name_en',
        'asset_type',
        'department',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'warranty_status',
        'warranty_expiration',
        'location',
        'operational_status',
        'status',
        'user_id',
    ];

    // 🟢 علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}