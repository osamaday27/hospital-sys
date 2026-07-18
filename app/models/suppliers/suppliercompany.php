<?php

namespace App\Models\suppliers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\General\Countries;
use App\Models\General\Government;
use App\Models\General\City;
class SupplierCompany extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_companies';

    protected $fillable = [
        'manual_code',
        'name_ar',
        'name_en',
        'activity',
        'commercial_registration_no',
        'tax_card_no',
        'domain',
        'phone',
        'email',
        'fax',
        'country_id',
        'government_id',
        'city_id',
        'notes',
        'status',
    ];

    // العلاقات
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    public function government()
    {
        return $this->belongsTo(Government::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
