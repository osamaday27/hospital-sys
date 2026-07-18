<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetauditline extends Model
{
    use SoftDeletes;

    protected $table = 'asset_audit_lines';

    protected $fillable = [
        'audit_id','asset_id','asset_code',
        'expected_location_id','expected_store_id','expected_status',
        'found_location_id','found_store_id','found_status',
        'scan_count','last_scanned_at','result',
        'apply_change','scanned_by','resolved_by','resolved_at','notes'
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
        'resolved_at'     => 'datetime',
        'apply_change'    => 'boolean',
        'scan_count'      => 'integer',
        'draft_asset_data' => 'array',
    ];

    public function audit(){ return $this->belongsTo(assetaudit::class, 'audit_id'); }
    public function asset(){ return $this->belongsTo(assetitem::class, 'asset_id'); }

    // تحديد نتيجة السطر حسب المتوقع والموجود
    public function recomputeResult(): void
    {
        if (is_null($this->asset_id)) {
            $this->result = 'extra';
        } elseif (is_null($this->found_location_id) && is_null($this->found_store_id) && is_null($this->found_status)) {
            $this->result = 'missing';
        } else {
            $sameLoc   = ($this->expected_location_id ?: null) === ($this->found_location_id ?: null);
            $sameStore = ($this->expected_store_id   ?: null) === ($this->found_store_id   ?: null);
            $sameStat  = ($this->expected_status     ?: null) === ($this->found_status     ?: null);

            if ($sameLoc && $sameStore && $sameStat) {
                $this->result = 'matched';
            } elseif (!$sameLoc) {
                $this->result = 'location_mismatch';
            } elseif (!$sameStore) {
                $this->result = 'store_mismatch';
            } else {
                $this->result = 'status_mismatch';
            }
        }
        $this->save();
    }
    // App\Models\assets\AuditLine.php
public function expLocation()  { return $this->belongsTo(\App\models\assets\assetlocation::class,'exp_location_id'); }
public function foundLocation(){ return $this->belongsTo(\App\models\assets\assetlocation::class,'found_location_id'); }
public function expStore()     { return $this->belongsTo(\App\Models\supply_chain\storemaindata::class,'exp_store_id'); }
public function foundStore()   { return $this->belongsTo(\App\Models\supply_chain\storemaindata::class,'found_store_id'); }

}
