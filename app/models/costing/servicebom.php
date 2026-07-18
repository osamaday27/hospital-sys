<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\models\finance\service; // موديل الخدمة عندك

class servicebom extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'service_boms';

    protected $fillable = [
        'service_id','code','name','version','status','is_active','notes','user_id',
    ];

    public $translatable = ['name'];

    public static function statuses(): array
    {
        return ['draft','approved','archived'];
    }

    public function service()
    {
        return $this->belongsTo(service::class, 'service_id');
    }

    public function items()
    {
        return $this->hasMany(servicebomitem::class, 'bom_id')->orderBy('line_no');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->where('status', 'approved');
    }
}