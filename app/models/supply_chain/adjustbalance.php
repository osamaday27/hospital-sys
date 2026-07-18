<?php

 namespace App\models\supply_chain;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\supply_chain\storemaindata;
use App\models\supply_chain\productdata;
use App\models\supply_chain\productunit;

class adjustbalance extends Model
{
    use SoftDeletes;

    protected $table = 'adjust_balance';

    protected $fillable = [
        'code',
        'store_id',
        'movement_datetime',
        'adjust_type',          // increase | decrease
        'product_id',
        'product_unit_id',
        'quantity',
        'batch_no',
        'balance',
        'sale_price',
        'cost_price',
        'expiry_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'movement_datetime' => 'datetime',
        'expiry_date'       => 'date',
        'quantity'          => 'decimal:5',
        'balance'           => 'decimal:5',
        'sale_price'        => 'decimal:2',
        'cost_price'        => 'decimal:2',
    ];

    /* علاقات */
    public function store()
    {
        return $this->belongsTo(storemaindata::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(productdata::class, 'product_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(productunit::class, 'product_unit_id');
    }

    /* Scopes لطيفة لو حبيت تستخدمها لاحقًا */
    public function scopeIncrease($q){ return $q->where('adjust_type', 'increase'); }
    public function scopeDecrease($q){ return $q->where('adjust_type', 'decrease'); }
}
