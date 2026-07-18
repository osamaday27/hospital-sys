<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetassignment extends Model
{
    use SoftDeletes;

    protected $table = 'asset_assignments';

    protected $fillable = [
        'parent_id',                 // ربط سجل الإرجاع بسجل الإسناد الأصلي (اختياري)
        'asset_id',
        'assignable_type','assignable_id','assignable_name',
        'assigned_at','due_back_at','returned_at','qty',
        'status','handover_path','notes',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'due_back_at' => 'date',
        'returned_at' => 'date',
        'qty'         => 'integer',

    ];

    /* ===================== العلاقات ===================== */

    // الأصل
    public function asset()
    {
        return $this->belongsTo(assetitem::class, 'asset_id');
    }

    // السجل الأصل (في حال كان هذا السجل عملية "إرجاع")
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // سجلات الإرجاع التابعة لهذا الإسناد
    public function returns()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /* ===================== Scopes/Helpers ===================== */

    /**
     * السجلات المفتوحة (إسناد جاري)
     * - الحالة assigned
     */
    public function scopeOpen($q)
    {
        return $q->where('status', 'assigned');
    }

    /**
     * هل الأصل مسند حاليًا (يوجد سجل مفتوح)
     */
    public function scopeOpenForAsset($q, int $assetId)
    {
        return $q->open()->where('asset_id', $assetId);
    }

    /**
     * هل السجل متأخر عن الإرجاع؟
     * متأخر إذا كان Assigned وله due_back_at < اليوم
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'assigned'
            && $this->due_back_at !== null
            && now()->toDateString() > $this->due_back_at->toDateString();
    }
}
