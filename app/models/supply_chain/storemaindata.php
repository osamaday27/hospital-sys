<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\general\branch;

class storemaindata extends Model
{
    use SoftDeletes;

    protected $table = 'store_main_date';

    protected $fillable = [
        'manual_code',
        'name_ar',
        'name_en',
        'status',
        'store_type',
        'address_ar',
        'address_en',
        'branch_id',
        'contact_methods',
        'pricing_method',
        'daily_avg_consumption',
        'profit_margin',
        'min_order_quantity',
        'max_order_quantity',
        'opening_balance',
        'is_inventory_allowed',
        'is_virtual',
        'related_stores_id',
    ];

    protected $casts = [
        'contact_methods'         => 'array',
        'is_inventory_allowed'    => 'boolean',
        'is_virtual'              => 'boolean',
        'opening_balance'         => 'decimal:2',
        'profit_margin'           => 'decimal:2',
        'daily_avg_consumption'   => 'decimal:2',
        'related_stores_id'       => 'array',
    ];
    

    // العلاقة مع الفروع
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
