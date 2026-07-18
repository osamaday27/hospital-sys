<?php
// app/models/administrative/adm_b_surgery_supply_issue.php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;

class adm_b_surgery_supply_issue extends Model
{
    protected $table = 'adm_b_surgery_supply_issues';
    protected $fillable = [
        'surgery_booking_id','visit_id','patient_id','store_id',
        'movement_number','status','user_id','notes'
    ];

    public function items()
    {
        return $this->hasMany(adm_b_surgery_supply_issue_item::class, 'issue_id');
    }
}