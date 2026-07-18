<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Suppliers\SuppliersData;

class representative extends Model
{
    use SoftDeletes;

    protected $table = 'representatives';

    protected $fillable = [
        'supplier_id',
        'name_ar',
        'name_en',
        'job_title',
        'email',
        'phone',
        'status',
    ];

    /**
     * العلاقة بالمورد (SuppliersData)
     */
    public function supplier()
    {
        return $this->belongsTo(SuppliersData::class, 'supplier_id');
    }
}
