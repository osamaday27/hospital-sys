<?php
// app/models/administrative/adm_b_surgery_supply_issue_item.php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;

class adm_b_surgery_supply_issue_item extends Model
{
    protected $table = 'adm_b_surgery_supply_issue_items';
    protected $fillable = [
        'issue_id','product_id','unit_id',
        'quantity_requested','quantity_issued','quantity_in_base_unit',
        'batch_no','expiry_date'
    ];

    public function issue()
    {
        return $this->belongsTo(adm_b_surgery_supply_issue::class, 'issue_id');
    }
      // ✅ علاقات المنتج والوحدة للعرض
    public function product()
    {
        return $this->belongsTo(\App\Models\supply_chain\productdata::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\supply_chain\unit::class, 'unit_id');
    }
}