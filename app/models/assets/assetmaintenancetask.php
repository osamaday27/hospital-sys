<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class assetmaintenancetask extends Model
{
    use SoftDeletes;

    protected $table = 'asset_maintenance_tasks';

    protected $fillable = [
        'maintenance_id','description','performed_at','hours','cost','technician_name','notes'
    ];

    protected $casts = [
        'performed_at' => 'date',
        'hours'        => 'decimal:2',
        'cost'         => 'decimal:2',
    ];

    public function maintenance()
    {
        return $this->belongsTo(assetmaintenance::class,'maintenance_id');
    }
}
