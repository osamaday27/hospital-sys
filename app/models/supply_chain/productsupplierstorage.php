<?php

namespace App\Models\supply_chain;


use App\Models\suppliers\suppliersdata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class productsupplierstorage extends Model
{
    use SoftDeletes;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'product_supplier_storages';

    // الحقول القابلة للتعبئة (mass assignable)
    protected $fillable = [
        'product_id',
        'supplier_ids',
        'storage_condition_ids',
    ];

    // قوالب تحويل القيم
    protected $casts = [
        'supplier_ids'          => 'array',
        'storage_condition_ids' => 'array',
    ];

    /**
     * علاقة الصنف
     */
    public function product()
    {
        return $this->belongsTo(ProductData::class, 'product_id');
    }

    /**
     * الحصول على كائنات الموردين المرتبطة
     */
    public function suppliers()
    {
        return suppliersdata::whereIn('id', $this->supplier_ids)->get();
    }

    /**
     * الحصول على كائنات ظروف التخزين المرتبطة
     */
    public function storageConditions()
    {
        return StorageConditionModel::whereIn('id', $this->storage_condition_ids)->get();
    }
}
