<?php

namespace App\Models\costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class servicebomitem extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'service_bom_items';

    protected $fillable = [
        'bom_id','line_no','item_type','inventory_item_id','resource_rate_id','cost_center_id',
        'description','unit','qty','unit_cost','total_cost','duration_minutes',
    ];

    public $translatable = ['description'];

    protected $casts = [
        'qty'         => 'decimal:4',
        'unit_cost'   => 'decimal:4',
        'total_cost'  => 'decimal:2',
    ];

    public function bom()
    {
        return $this->belongsTo(servicebom::class, 'bom_id');
    }
}