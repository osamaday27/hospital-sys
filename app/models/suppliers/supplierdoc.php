<?php

namespace App\Models\suppliers;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierDoc extends Model
{
    use SoftDeletes;

    protected $table = 'supplierdocs';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'supplier_id',
        'file_type',
        'file_name',
        'file_path',
    ];

    /**
     * العلاقة بالمورد
     */
    public function supplier()
    {
        // يفترض أن موديل المورد هو SuppliersData في نفس الـ namespace
        return $this->belongsTo(suppliersdata::class, 'supplier_id');
    }
}
