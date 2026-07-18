<?php


namespace app\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class receivinginspectingitem extends Model
{
    use SoftDeletes;

    protected $table = 'receiving_inspecting_items';

    protected $fillable = [
        'receiving_inspecting_id','product_id','unit_id','batch_no','expiry_date',
        'request_qty','po_qty','delivered_qty','accepted_qty','rejected_qty',
        'cost_price','rejection_reason','accepted_unit_id','delivered_qty_display','accepted_qty_display','rejected_qty_display'
    ];

    public function document() {
        return $this->belongsTo(ReceivingInspecting::class, 'receiving_inspecting_id');
    }
    public function product() {
        return $this->belongsTo(\App\Models\Supply_Chain\ProductData::class, 'product_id');
    }
    public function unit() {
        return $this->belongsTo(\App\Models\Supply_Chain\ProductUnit::class, 'unit_id');
    }
        public function acceptedUnit()
    {
        return $this->belongsTo(\App\Models\supply_chain\unit::class, 'accepted_unit_id');
    }
}
