<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class storeproductmodel extends Model
{
    use SoftDeletes;

    protected $table = 'store_products';

    protected $fillable = [
        'store_id',
        'product_id',
        'status',

        // الحقول الجديدة
        'order_min_qty',
        'order_min_unit_id',
        'order_max_qty',
        'order_max_unit_id',
        'reorder_point_qty',
        'reorder_point_unit_id',
    ];

    protected $casts = [
        'order_min_qty'      => 'decimal:6',
        'order_max_qty'      => 'decimal:6',
        'reorder_point_qty'  => 'decimal:6',
    ];

    // العلاقات
    public function store()
    {
        return $this->belongsTo(storemaindata::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\models\supply_chain\productdata::class, 'product_id')
                    ->withTrashed(); // لو المنتج بيتحذف سوفت
    }

    public function orderMinUnit()
    {
        return $this->belongsTo(\App\models\supply_chain\productunit::class, 'order_min_unit_id')
                    ->withTrashed();
    }

    public function orderMaxUnit()
    {
        return $this->belongsTo(\App\models\supply_chain\productunit::class, 'order_max_unit_id')
                    ->withTrashed();
    }

    public function reorderPointUnit()
    {
        return $this->belongsTo(\App\models\supply_chain\productunit::class, 'reorder_point_unit_id')
                    ->withTrashed();
    }
}
