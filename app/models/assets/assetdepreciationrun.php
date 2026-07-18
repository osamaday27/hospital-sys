<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetdepreciationrun extends Model
{
    use SoftDeletes;

    protected $table = 'asset_depreciation_runs';

    protected $fillable = [
        'period','status','notes','created_by','posted_by','posted_at'
    ];

    /**
     * تنسيقات الحقول الزمنية
     * - period كـ تاريخ بسيط (YYYY-MM-DD)
     * - posted_at / created_at / updated_at كـ datetime
     */
    protected $casts = [
        'period'     => 'date',
        'posted_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * إلحاق خصائص للعرض تلقائيًا عند تحويل الموديل إلى Array/JSON
     */
    protected $appends = [
        'status_label',
        'period_ymd',
    ];

    /* ===================== العلاقات ===================== */

    public function lines()
    {
        return $this->hasMany(assetdepreciationline::class, 'run_id');
    }

    // المستخدم الذي أنشأ التشغيل
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // المستخدم الذي رحّل التشغيل
    public function poster()
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by');
    }

    /* ===================== Accessors للعرض ===================== */

    /**
     * لافتة الحالة مترجمة — ex: draft → "مسودة"
     */
    public function getStatusLabelAttribute(): string
    {
        return trans('assets.depreciation.status_' . ($this->status ?? 'draft'));
    }

    /**
     * تاريخ الفترة بصيغة YYYY-MM-DD جاهز للعرض في الجدول/الواجهات
     */
    public function getPeriodYmdAttribute(): ?string
    {
        return $this->period ? $this->period->toDateString() : null;
    }
    public function run(){ return $this->belongsTo(\App\models\assets\assetdepreciationrun::class, 'run_id'); }

}
