<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankMachine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fin_bank_machines';

    protected $fillable = [
        'bank_id',
        'branch_id',
        'machine_code',
        'machine_type',
        'location',
        'status',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\General\Branch::class, 'branch_id');
    }
}