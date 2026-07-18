<?php
namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientItemRequestItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_id', 'product_id', 'unit_id', 'quantity',
        'created_by', 'updated_by',
    ];

    public function request()
    {
        return $this->belongsTo(ClientItemRequest::class, 'request_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\supply_chain\productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\supply_chain\unit::class, 'unit_id');
    }
}