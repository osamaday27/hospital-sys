<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class EmployeeItemRequestDetail extends Model
{
    protected $fillable = [
        'request_id',
        'product_id',
        'unit_id',
        'quantity',
        'created_by',
    ];
    protected $table = 'employee_item_request_details_stroe';

    public function request()
    {
        return $this->belongsTo(EmployeeItemRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductData::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
