<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderCommittee extends Model
{
    protected $table = 'purchase_order_committees';

    protected $fillable = [
        'purchase_order_id',
        'employee_id',
        'department_id',
        'job_position',
        'is_chair',
    ];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\hr\Employee::class, 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\hr\Department::class, 'department_id');
    }



}
