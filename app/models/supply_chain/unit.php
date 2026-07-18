<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'name_ar',
        'name_en',
        'unit_type',
    ];

    // العلاقات مع جدول product_units
    public function productUnitsLarge()
    {
        return $this->hasMany(ProductUnit::class, 'unit_large_id');
    }

    public function productUnitsSmall()
    {
        return $this->hasMany(ProductUnit::class, 'unit_small_id');
    }

    // التحقق قبل الحذف
    public function canBeDeleted(): bool
    {
        return !$this->productUnitsLarge()->exists() && !$this->productUnitsSmall()->exists();
    }
}
