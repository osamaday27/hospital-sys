<?php

namespace App\Models\suppliers;

use App\Models\Suppliers\suppliersdata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierOpeningBalance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier_opening_balances';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'supplier_id',
        'year',
        'credit_balance',
        'debit_balance',
    ];

    /**
     * العلاقة مع مورد
     */
    public function supplier()
    {
        return $this->belongsTo(suppliersdata::class);
    }
}
