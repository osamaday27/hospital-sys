<?php

namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class supplychaingeneralsetting extends Model
{
    use SoftDeletes;

    /**
     * اسم الجدول المرتبط بالموديل.
     *
     * @var string
     */
    protected $table = 'supply_chain_general_settings';

    /**
     * الحقول التي يمكن تعبئتها.
     *
     * @var array
     */
    protected $fillable = [
        'cost_policy',
        'sale_policy',
        'item_coding_method',
        'item_code_length',
        'gift_cost',
        'barcode_labels',
        'store_user_link',
        'general_serial_method',
        'financial_year_code',
        'auto_substitution',
        'expenditure_days',
        'dont_update_sale_price',
    ];
    protected $casts = [
        'expenditure_days' => 'array',
        'auto_substitution' => 'boolean',
    ];
    /**
     * الحقول التي يجب التعامل معها كتاريخ.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
