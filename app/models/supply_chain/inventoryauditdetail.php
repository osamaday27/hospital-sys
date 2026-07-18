<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class inventoryauditdetail extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_audit_details';

    protected $fillable = [
        'inventory_audit_id',
        'product_id',
        // للمتوافقية مع نسخة المايجريشن بدون DBAL أبقينا كلا العمودين ممكن الاستخدام
        'product_unit_id',     // الجديد: FK -> product_units
        'units_id',            
        'batch_no',
        'expiry_date',
        'system_quantity',
        'actual_quantity',
        'difference_quantity',
        'status',              // surplus | shortage | match
        'reason',
        'notes',
    ];

    protected $casts = [
        'expiry_date'         => 'date',
        'system_quantity'     => 'decimal:4',
        'actual_quantity'     => 'decimal:4',
        'difference_quantity' => 'decimal:4',
    ];

    /* علاقات */
    public function audit()
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Supply_Chain\ProductData::class, 'product_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(\App\Models\supply_chain\productunit::class, 'product_unit_id');
    }

    /** سكوب سريع لجلب تفاصيل محضر محدد */
    public function scopeForAudit($query, $auditId)
    {
        return $query->where('inventory_audit_id', $auditId);
    }

    /** ضبط الفرق والحالة تلقائياً قبل الحفظ */
    protected static function booted()
    {
        static::saving(function (self $row) {
            // لو difference مش متحسب، احسبه من الفعلي - النظامي
            if ($row->actual_quantity !== null && $row->system_quantity !== null) {
                $row->difference_quantity = (string) ((float) $row->actual_quantity - (float) $row->system_quantity);
            }

            // تحديد الحالة بناءً على الفرق لو لم تُرسل أو كانت غير صحيحة
            $valid = ['surplus', 'shortage', 'match'];
            if (!in_array($row->status, $valid, true)) {
                $diff = (float) ($row->difference_quantity ?? 0);
                $row->status = $diff == 0.0 ? 'match' : ($diff > 0.0 ? 'surplus' : 'shortage');
            }
        });
    }
}
