<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class productstorebalance extends Model
{
    use SoftDeletes;

    protected $table = 'product_store_balances';

    protected $fillable = [
        'product_id',
        'store_id',
        'batch_no',
        'expiry_date',
        'quantity_in_base_unit',
        'units_id', 
        'quantity',
        
    ];
    public function store()
{
    return $this->belongsTo(storemaindata::class, 'store_id');
}

 // العلاقات المطلوبة للاستعلام with(['product','store','unit'])
    public function product()
    {
        return $this->belongsTo(productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(unit::class, 'units_id');
    }

   
    protected $dates = ['expiry_date'];
}