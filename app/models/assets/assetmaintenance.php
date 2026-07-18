<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// الموردين
use App\Models\Suppliers\suppliersdata as SuppliersData;

class assetmaintenance extends Model
{
    use SoftDeletes;

    protected $table = 'asset_maintenances';

    protected $fillable = [
        'code','asset_id','maintenance_type','status','priority',
        'reported_at','started_at','finished_at',
        'summary','description','vendor_id','cost_total',
        'next_due_date','maintenance_request_id','attachment_path','user_id'
    ];

    protected $casts = [
        'reported_at' => 'date',
        'started_at'  => 'date',
        'finished_at' => 'date',
        'next_due_date' => 'date',
        'cost_total'  => 'decimal:2',
    ];

    public function asset()   { return $this->belongsTo(assetitem::class,'asset_id'); }
    public function vendor()  { return $this->belongsTo(SuppliersData::class,'vendor_id'); }
    public function tasks()   { return $this->hasMany(assetmaintenancetask::class,'maintenance_id'); }

    // مجموع تكلفة البنود (إن أحببت استخدامه)
    public function getTasksSumAttribute()
    {
        return $this->tasks()->sum('cost');
    }
}
