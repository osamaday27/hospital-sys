<?php

namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetconversion extends Model
{
    use SoftDeletes;

    protected $table = 'asset_conversions';

    protected $fillable = [
        'receiving_inspecting_id',
        'receiving_item_id',
        'product_id',
        'store_id',
        'batch_no',
        'expiry_date',
        'unit_id',
        'qty_in_unit',
        'qty_in_base',
        'cost_price',
        'asset_item_id',
        'conversion_date',
        'user_id',
        'notes',
        'status',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'expiry_date'     => 'date',
        'conversion_date' => 'datetime',
        'qty_in_unit'     => 'decimal:3',
        'qty_in_base'     => 'decimal:3',
        'cost_price'      => 'decimal:4',
        'cancelled_at'    => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(\App\models\assets\assetitem::class, 'asset_item_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\supply_chain\productdata::class, 'product_id')->withTrashed();
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'store_id')->withTrashed();
    }

    public function receiving()
    {
        return $this->belongsTo(\App\Models\supply_chain\receivinginspecting::class, 'receiving_inspecting_id');
    }

    public function receivingItem()
    {
        return $this->belongsTo(\App\Models\supply_chain\receivinginspectingitem::class, 'receiving_item_id');
    }
}
