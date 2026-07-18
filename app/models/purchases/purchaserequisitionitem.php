<?php

namespace App\Models\purchases;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\supply_chain\productdata;
class purchaserequisitionitem extends Model
{
        use SoftDeletes;

    use HasFactory;
    protected $table = 'purchase_requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_id',
        'requested_qty',
        'unit_id',
        'pending_qty',
        'current_balance',
        'other_stores_balance',
        'last_price',
        'notes',
    ];

    public function requisition()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }

    public function item()
    {
        return $this->belongsTo(productdata::class);
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\supply_chain\productunit::class);
    }
        public function product()
{
    return $this->belongsTo(productdata::class, 'item_id');
}
}
