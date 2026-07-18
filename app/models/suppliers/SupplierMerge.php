<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;

class SupplierMerge extends Model
{
    protected $table = 'supplier_merges';

    protected $fillable = [
        'main_supplier_id',
        'merged_supplier_id',
        'user_id',
        'affected_tables',
        'is_reverted'
    ];

    protected $casts = [
        'affected_tables' => 'array',
        'is_reverted' => 'boolean',
    ];

    public function mainSupplier()
    {
        return $this->belongsTo(suppliersdata::class, 'main_supplier_id');
    }

    public function mergedSupplier()
    {
        return $this->belongsTo(suppliersdata::class, 'merged_supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}