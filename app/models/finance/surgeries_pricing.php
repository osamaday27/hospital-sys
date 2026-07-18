<?php
namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class surgeries_pricing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_s_surgeries_pricing';

    protected $fillable = [
        'fin_contract_last_level_id',
        'fin_price_lists_id',
        'fin_services_id',
        'base_price',
        'standard_time',
        'extra_time_cost',
        'main_surgeon_fees',
        'assistants_fees',
        'anesthesia_fees',
        'anesthesia_type',
        'nursing_fees',
        'extra_services',
        'surgical_supplies',
        'or_room_base_price',
        'or_extra_time_price',
        'contract_discount',
        'vat',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'assistants_fees' => 'array',
        'extra_services' => 'array',
        'surgical_supplies' => 'array',
    ];

    public function contract() { return $this->belongsTo(FinContractLastLevel::class, 'fin_contract_last_level_id'); }
    public function priceList() { return $this->belongsTo(PriceList::class, 'fin_price_lists_id'); }
    public function service() { return $this->belongsTo(Service::class, 'fin_services_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
}