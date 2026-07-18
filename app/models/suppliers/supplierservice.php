<?php

namespace App\Models\suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierService extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers_services';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'price',
        'description',
        'notes',
        'status',
    ];
}
