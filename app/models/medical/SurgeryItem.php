<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurgeryItem extends Model
{
    use HasFactory;

    protected $table = 'surgery_items';

    protected $fillable = [
        'surgery_id',
        'item_id',
        'quantity',
        'unit',
        'notes',
        'user_id',
    ];

    public function surgery()
    {
        return $this->belongsTo(\App\Models\Finance\Service::class, 'surgery_id');
    }

    public function item()
    {
        return $this->belongsTo(\App\Models\Supply_Chain\Productdata::class, 'item_id');
    }
}