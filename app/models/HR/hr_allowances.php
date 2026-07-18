<?php

namespace App\models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_allowances extends Model
{
    use HasFactory;

    protected $table = 'hr_allowances';

    protected $fillable = [
        'name',
        'allowance_id',
        'amount',
        'contract_id',
    ];

    public function allowance_type()
    {
        return $this->belongsTo(hr_allowances_types::class, 'allowance_id');
    }

    public function contract()
    {
        return $this->belongsTo(\App\models\hr\hr_contracts::class, 'contract_id');
    }
} 