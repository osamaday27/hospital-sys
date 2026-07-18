<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class productunit extends Model
{
    use SoftDeletes;

    protected $table = 'product_units';

    protected $fillable = [
        'product_id',
        'unit_large_id',
        'unit_small_id',
        'conversion_rate',
        'cost_price',
        'sale_price',
        'unit_type',
    ];

    protected $casts = [
        'conversion_rate' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(productdata::class, 'product_id')->withTrashed();
    }

    // علاقة مع وحدة القياس الكبيرة
    public function largeUnit()
    {
        return $this->belongsTo(Unit::class, 'unit_large_id')->withTrashed();
    }

    // علاقة مع وحدة القياس الصغيرة
    public function smallUnit()
    {
        return $this->belongsTo(Unit::class, 'unit_small_id')->withTrashed();
    }
          // علاقة مع وحدة القياس الكبيرة
   public function unitLarge()
   {
       return $this->belongsTo(unit::class, 'unit_large_id');
   }

   // علاقة مع وحدة القياس الصغيرة
   public function unitSmall()
   {
       return $this->belongsTo(unit::class, 'unit_small_id');
   }
}
