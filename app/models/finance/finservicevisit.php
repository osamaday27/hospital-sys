<?php

namespace App\models\finance;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\models\finance\service;
class finservicevisit extends Model
{
    use HasFactory, SoftDeletes;

    // Define the table associated with the model (optional if the table name is in plural form)
    protected $table = 'fin_services_visit';

    // Define the fillable attributes (these are the columns you can assign values to)
    protected $fillable = [
        'id_invoices',
        'id_visit',
        'id_services',
        'id_patient',
        'total_pricing',
        'patient_contribution',
        'contract_contribution',
        'discount',
        'quantity',
        'currency',
        'notes',
        'id_user',
        'account_id',
    ];

    // Optionally define the date format if required
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    // You can also define relationships if there are any, for example:
    public function service() {
         return $this->belongsTo(service::class, 'id_services');
     }

    // public function patient() {
    //     return $this->belongsTo(Patient::class, 'id_patient');
    // }
}