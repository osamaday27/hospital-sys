<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetaudit extends Model
{
    use SoftDeletes;

    protected $table = 'asset_audits';

    protected $fillable = [
        'code','title','status','notes',
        'scope_location_id','scope_store_id',
        'expected_count','scanned_count','matched_count','mismatch_count','missing_count','extra_count',
        'started_at','finished_at',
        'created_by','started_by','finished_by',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(assetauditline::class, 'audit_id');
    }

    // ملصق الحالة مترجم
    public function getStatusLabelAttribute(): string
    {
        return trans('assets.audit.s_'.$this->status);
    }

    // تحديث الإحصاءات من البنود
    public function recomputeTotals(): void
    {
        $q = $this->lines();
        $this->scanned_count  = (clone $q)->whereIn('result',['matched','location_mismatch','store_mismatch','status_mismatch','extra'])->count();
        $this->matched_count  = (clone $q)->where('result','matched')->count();
        $this->mismatch_count = (clone $q)->whereIn('result',['location_mismatch','store_mismatch','status_mismatch'])->count();
        $this->missing_count  = (clone $q)->where('result','missing')->count();
        $this->extra_count    = (clone $q)->where('result','extra')->count();
        $this->save();
    }
}
