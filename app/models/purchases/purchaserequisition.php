<?php

namespace app\Models\purchases;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\purchases\purchaserequisitionitem;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class purchaserequisition extends Model
{
        use SoftDeletes;

    use HasFactory;
    protected $table = 'purchase_requisitions';

    protected $fillable = [
        'requisition_number',
        'requisition_date',
        'warehouse_id',
        'department_id',
        'preferred_supplier_id',
        'reason',
        'attachments',
        'status',
        'created_by',
    ];
    protected $casts = [
        'attachments' => 'array',
    ];
    public function items()
    {
        return $this->hasMany(purchaserequisitionitem::class, 'requisition_id');
    }

    public function warehouse()
{
    return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'warehouse_id');
}


    public function department()
    {
        return $this->belongsTo(\App\Models\hr\Department::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Suppliers\suppliersdata::class, 'preferred_supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }


}
