<?php

namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionStoreReturn extends Model
{
    use SoftDeletes;

    protected $table = 'section_store_returns';

    protected $fillable = [
        'code',
        'request_number',
        'section_id',
        'store_id',
        'return_date',
        'product_id',
        'unit_id',
        'batch_no',
        'quantity',
        'notes',
        'created_by',
    ];

    protected $dates = ['return_date'];

    // العلاقات
    public function section()
    {
        return $this->belongsTo(\App\models\hr\section::class, 'section_id');
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\supply_chain\storemaindata::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\models\supply_chain\productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\supply_chain\unit::class, 'unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
