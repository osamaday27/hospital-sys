<?php

// app/Models/supply_chain/ReceivingInspectingApproval.php
namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class receivinginspectingapproval extends Model
{
    use SoftDeletes;

    protected $table = 'receiving_inspecting_approvals';

    protected $fillable = [
        'receiving_inspecting_id','employee_id','is_chairman','responsibilities',
        'status','remarks'
    ];

    protected $casts = [
        'is_chairman' => 'boolean',
    ];

    public function document() {
        return $this->belongsTo(ReceivingInspecting::class, 'receiving_inspecting_id');
    }
    public function employee() {
        return $this->belongsTo(\App\Models\hr\Employee::class, 'employee_id');
    }
}
