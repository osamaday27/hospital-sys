<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class resourcerate extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'resource_rates';

    protected $fillable = [
        'code','name','type','cost_center_id','unit','rate','status','user_id',
    ];

    public $translatable = ['name'];

    public static function types(): array
    {
        return ['doctor','nurse','room','device','bed','other'];
    }

    public static function units(): array
    {
        return ['minute','hour','day'];
    }

    public static function statuses(): array
    {
        return ['active','inactive'];
    }

    public function costCenter()
    {
        return $this->belongsTo(costcenter::class,'cost_center_id');
    }
}