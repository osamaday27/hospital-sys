<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurgeryDrug extends Model
{
    use HasFactory;

    protected $table = 'surgery_drugs';

    protected $fillable = [
        'surgery_id',
        'drug_id',
        'dose',
        'unit',
        'notes',
        'user_id',
    ];

    public function surgery()
    {
        return $this->belongsTo(\App\Models\Finance\Service::class, 'surgery_id');
    }

    public function drug()
    {
        return $this->belongsTo(\App\models\supply_chain\productdata::class, 'drug_id');
    }
}