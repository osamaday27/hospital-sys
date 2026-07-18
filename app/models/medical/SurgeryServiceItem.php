<?php
// app/Models/Medical/SurgeryServiceItem.php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurgeryServiceItem extends Model
{
    use HasFactory;

    protected $table = 'surgery_service_items';

    protected $fillable = [
        'surgery_id',
        'service_id',
        'quantity',
        'service_type',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'surgery_id' => 'integer',
        'service_id' => 'integer',
        'quantity'   => 'float',
    ];

    public function surgery()
    {
        // لو العملية نفسها محفوظة كـ service في fin_services
        return $this->belongsTo(\App\models\finance\service::class, 'surgery_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(\App\models\finance\service::class, 'service_id', 'id');
    }
}