<?php

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\General\Branch;
use App\Models\General\Countries;
use App\Models\General\Government;
use App\Models\General\City;
use App\Models\Suppliers\SuppliersGroup;
use App\Models\Suppliers\SupplierCompany;

class suppliersdata extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers_data';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'suppliersgroup_id',
        'branch_id',
        'suppliercompany_id',
        'supplier_type',
        'tax_card_number',
        'commercial_registration_number',
        'company_field',
        'contact_methods',
        'country_id',
        'government_id',
        'city_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'contact_methods' => 'array',
    ];

    /**
     * علاقات الموديل
     */
    // مجموعة الموردين
    public function group()
    {
        return $this->belongsTo(SuppliersGroup::class, 'suppliersgroup_id');
    }

    // الفرع
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // شركة المورد
    public function company()
    {
        return $this->belongsTo(SupplierCompany::class, 'suppliercompany_id');
    }

    // الدولة
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    // المحافظة
    public function government()
    {
        return $this->belongsTo(Government::class, 'government_id');
    }

    // المدينة
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    // منشئ السجل
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function invoices()
{
    return $this->hasMany(\App\Models\Suppliers\SupplierInvoice::class, 'supplier_id');
}

public function payments()
{
    return $this->hasManyThrough(
        \App\Models\Suppliers\SupplierPayment::class,
        \App\Models\Suppliers\SupplierInvoice::class,
        'supplier_id',      // المفتاح في جدول الفواتير
        'supplier_invoice_id', // المفتاح في جدول المدفوعات
        'id',               // المفتاح في جدول الموردين
        'id'                // المفتاح في جدول الفواتير
    );
}

public function alerts()
{
    return $this->hasMany(\App\Models\Suppliers\supplieralert::class, 'supplier_id');
}

public function openingBalances()
{
    return $this->hasMany(\App\Models\Suppliers\SupplierOpeningBalance::class, 'supplier_id');
}

}