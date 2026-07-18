<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientStoreReturn extends Model
{
    use SoftDeletes;

    protected $table = 'client_store_returns';

    protected $fillable = [
        'code','request_number','client_id','store_id','return_date',
        'product_id','unit_id','batch_no','quantity','notes','created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'quantity'    => 'decimal:6',
    ];

    public function client()
    {
        // جدول المرضى اسمه patient
        return $this->belongsTo(\App\models\administrative\medical_record::class, 'client_id');
    }

    public function store()
    {
        return $this->belongsTo(\App\models\supply_chain\storemaindata::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\models\supply_chain\productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\models\supply_chain\unit::class, 'unit_id');
    }
}
