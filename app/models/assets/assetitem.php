<?php

namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;

// موديلاتك الحالية:
use App\Models\supply_chain\storemaindata as StoreMainData;
use App\Models\Suppliers\suppliersdata as SuppliersData;

class assetitem extends Model
{
    protected $table = 'asset_items';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'category_id',
        'brand_id',
        'model_id',
        'location_id',
        'store_id',
        'supplier_id',
        'serial_no',
        'purchase_date',
        'cost',
        'useful_life_months',
        'dep_method',
        'dep_rate',
        'status',
        'qr_code_path',
        'qty',
        'rfid_tag',
        'is_active',
        'residual_value',
        'in_service_date',
        'accumulated_depreciation',
        'net_book_value',
        // حقول مرتبطة بالإهلاك أضيفت مؤخراً
        'last_depreciation_date',
        'uop_total_estimated_units',
    ];

    protected $casts = [
        // تواريخ
        'purchase_date'          => 'date',
        'in_service_date'        => 'date',
        'last_depreciation_date' => 'date',

        // منطقية وأعداد صحيحة
        'is_active'                  => 'boolean',
        'qty'                        => 'integer',
        'useful_life_months'         => 'integer',
        'uop_total_estimated_units'  => 'integer',

        // أرقام عشرية
        'cost'                     => 'decimal:2',
        'residual_value'           => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'net_book_value'           => 'decimal:2',
        'dep_rate'                 => 'decimal:3', // متوافق مع (6,3)
    ];

    public function category()
    {
        return $this->belongsTo(assetcategory::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(assetbrand::class, 'brand_id');
    }
    public function model()
    {
        return $this->belongsTo(assetmodel::class, 'model_id');
    }
    public function location()
    {
        return $this->belongsTo(assetlocation::class, 'location_id');
    }

    // علاقات مع جداولك
    public function store()
    {
        return $this->belongsTo(StoreMainData::class, 'store_id');
    }
    public function supplier()
    {
        return $this->belongsTo(SuppliersData::class, 'supplier_id');
    }
    public function assignments()
    {
        return $this->hasMany(assetassignment::class, 'asset_id');
    }
    public function documents()
    {
        return $this->hasMany(assetdocument::class, 'asset_id');
    }

    public function assignmentsOpen() {
        return $this->hasMany(\App\models\assets\assetassignment::class, 'asset_id')
                    ->where('status', 'assigned')
                    ->whereDoesntHave('childReturn');
    }
}
