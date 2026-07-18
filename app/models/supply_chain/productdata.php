<?php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use  App\models\supply_chain\productcategory;

class productdata extends Model
{
    use SoftDeletes;
    protected $table = 'products_data';

    protected $fillable = [
        'system_code',
        'name_ar',
        'name_en',
        'product_category_id',
        'status',
        'description',
        'barcode',
        'product_code',
        'unified_code',
        'expiration_date',
        'profit_margin',
        'vat',
        'vat_value',
        'product_type',
        'is_critical',
        'ledger_number',
        'images',
    ];
    protected $casts = [
        'images' => 'array',
        'is_critical' => 'boolean',

    ];
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
public function productUnits()
{
    return $this->hasMany(productunit::class, 'product_id');
}

/* فى الغالب تحتاج أيضاً علاقة للوحدة الافتراضية، مثلاً أصغر وحدة */
public function defaultProductUnit()
{
    return $this->hasOne(productunit::class, 'product_id')
                ->where('unit_type', 'small');   // أو أى شرط يعتبره نظامك «الوحدة الافتراضية»
}
public function productstorebalance()
{
    return $this->hasMany(\App\Models\supply_chain\ProductStoreBalance::class, 'product_id');
}

}
