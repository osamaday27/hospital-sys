<?php

namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicServiceLink extends Model
{
    use SoftDeletes;
    protected $table = 'clinic_service_links';

    protected $fillable = ['clinic_id', 'service_id', 'is_consultant', 'service_type', 'status', 'user_id'];

    public function clinic()
    {
        return $this->belongsTo(\App\Models\administrative\admsclinic::class, 'clinic_id');
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\finance\service::class, 'service_id');
    }
}