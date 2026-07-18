<?php



namespace app\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class receivinginspecting extends Model
{
    use SoftDeletes;

    protected $table = 'receiving_inspecting';

    protected $fillable = [
        'supplier_id','store_id','purchase_order_id',
        'code','document_date','notes','receipt_notes','status'
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    // علاقات مرجعية
    public function supplier() {
        return $this->belongsTo(\App\Models\Suppliers\SuppliersData::class, 'supplier_id');
    }
    public function store() {
        return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'store_id');
    }
    public function purchaseOrder() {
        return $this->belongsTo(\App\Models\purchases\purchaseorder::class, 'purchase_order_id');
    }

    // تفاصيل وموافقات
    public function items() {
        return $this->hasMany(\App\Models\supply_chain\receivinginspectingitem::class, 'receiving_inspecting_id');
    }
    public function approvals() {
        return $this->hasMany(\App\Models\supply_chain\ReceivingInspectingApproval::class, 'receiving_inspecting_id');
    }
    public function acceptedUnit(){ return $this->belongsTo(\App\Models\supply_chain\unit::class, 'accepted_unit_id'); }

}
