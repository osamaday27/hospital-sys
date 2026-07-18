<?php
namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;

class departmentItemRequest extends Model
{
    protected $fillable = [
        'department_id',
        'store_id',
        'request_date',
        'reason',
        'status',
        'request_number',
        'created_by'
        
    ];
    protected $table = 'department_item_requests_stores';

    public function department()
    {
        return $this->belongsTo(\App\Models\hr\section::class);
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
        return $this->hasMany(departmentItemRequestDetail::class, 'request_id');
    }
    public function items()
    {
        return $this->details(); // Alias
    }
    
}