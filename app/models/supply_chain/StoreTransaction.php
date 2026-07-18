<?php

namespace App\Models\supply_chain;



use Illuminate\Database\Eloquent\Model;
use App\Models\supply_chain\storemaindata;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'opening_balances',
        'add_items',
        'return_to_supplier',
        'return_from_department',
        'return_from_customer',
        'return_balance_from_store',
        'issue_to_customer',
        'issue_to_department',
        'transfer_balance_to_store',
        'inventory',
        'items_destruction',
        'issue_to_employee',
        'return_from_employee',
        'balance_adjustment_increase',
        'balance_adjustment_decrease',
        'issue_to_supplier',
        'return_balance_to_store',
        'additional_opening_balance',
        'issue_to_another_branch',
        'add_from_another_branch',
    ];

    public function store()
    {
        return $this->belongsTo(storemaindata::class, 'store_id');
    }
}
