<?php

namespace App\Models\supply_chain;

use App\models\general\branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class productcategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_categories';

    protected $fillable = [
        'name_en',
        'name_ar',
        'type',
        'status',
        'branch',
        'sort',
        'category',
    ];

    protected $casts = [
        'branch' => 'array',
        'status' => 'boolean',
    ];
    public function branchRelation()
    {
        return $this->belongsTo(branch::class, 'branch');
    }


    // يمكنك تعريف الـ Accessors هنا للترجمة مثلا
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
}
