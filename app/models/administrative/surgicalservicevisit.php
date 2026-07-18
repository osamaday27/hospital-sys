<?php
namespace App\Models\administrative;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surgicalservicevisit extends Model
{
    use HasFactory;

    protected $table = 'surgical_service_visits';

    protected $fillable = [
        'invoice_id',
        'visit_id',
        'service_id',
        'total',
        'contract_value',
        'patient_value',
        'discount',
        'quantity',
        'patient_id',
    ];

    public function service()
    {
        return $this->belongsTo(\App\Models\finance\service::class, 'service_id');
    }
}