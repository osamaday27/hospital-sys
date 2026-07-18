<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class inventoryaudit extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_audits';

    protected $fillable = [
        'code',
        'store_id',
        'audit_date',
        'start_date',
        'end_date',
        'user_id',
        'status',               // نص مفتوح
        'committee_user_ids',   // JSON (array of user IDs)
        'attachments',          // JSON (array of file paths / ids)
        'notes',
    ];

    protected $casts = [
        'audit_date'         => 'date',
        'committee_user_ids' => 'array',
        'attachments'        => 'array',
    ];

    /* علاقات */
    public function details()
    {
        return $this->hasMany(InventoryAuditDetail::class, 'inventory_audit_id');
    }

    public function store()
    {
        // مشروعك يستخدم موديل stores\store باسم كلاس صغير
        return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'store_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /** جلب مستخدمي لجنة الجرد من IDs المخزنة في JSON */
    public function committeeUsers()
    {
        $ids = array_filter((array) $this->committee_user_ids);
        if (empty($ids)) return collect();
        return \App\Models\User::whereIn('id', $ids)->get();
    }

    /** توليد كود فريد تلقائياً عند الإنشاء إذا لم يُرسل */
    protected static function booted()
    {
        static::creating(function (self $model) {
            if (empty($model->code)) {
                do {
                    $model->code = 'IA-' . now()->format('Ym') . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
                } while (self::where('code', $model->code)->exists());
            }

            // تأكيد أن JSON مخزن كـ array
            $model->committee_user_ids = array_values(array_unique(array_filter((array) ($model->committee_user_ids ?? []))));
            $model->attachments        = array_values(array_filter((array) ($model->attachments ?? [])));
        });

        static::saving(function (self $model) {
            // تنظيف القيم قبل الحفظ
            $model->committee_user_ids = array_values(array_unique(array_filter((array) ($model->committee_user_ids ?? []))));
            $model->attachments        = array_values(array_filter((array) ($model->attachments ?? [])));
        });
    }
}
