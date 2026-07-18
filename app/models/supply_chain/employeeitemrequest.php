<?php
namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class EmployeeItemRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'store_id',
        'request_date',
        'reason',
        'status',
        'request_number',
        'created_by'
        
    ];
    protected $table = 'employee_item_requests_stores';

    public function employee()
    {
        return $this->belongsTo(\App\Models\hr\Employee::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreMainData::class, 'store_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(EmployeeItemRequestDetail::class, 'request_id');
    }
    public function items()
{
    return $this->details(); // Alias
}

}