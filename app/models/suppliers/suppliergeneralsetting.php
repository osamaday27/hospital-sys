<?php

namespace App\Models\suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class suppliergeneralsetting extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_general_settings';

    protected $fillable = [
        'notify_return_from_warehouse',
        'notify_discount_on_service_invoice',
        'notify_discount_on_supply_invoice',
        'rounding_allowed_value',
    ];

    /**
     * إشعارات الخصم والإضافة المرتبطة بهذا الإعداد العام
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(suppliersnotification::class, 'general_setting_id');
    }
}