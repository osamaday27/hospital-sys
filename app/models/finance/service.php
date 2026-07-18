<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;

class service extends Model
{
    use HasTranslations, TracksChanges, SoftDeletes;

    protected $fillable = [
        'name','description','patient_type','age_restriction_from','age_restriction_to',
        'repeat_duration','confidentiality','sublevel_id','default_branch','user_id',
        'status','hospital_code','contract_service_name_ar','contract_service_name_en'
    ];

    public $timestamps = true;
    public $translatable = ['name', 'description'];
    protected $dates = ['deleted_at'];
    protected $table = 'fin_services';

    public function sublevel()
    {
        return $this->belongsTo(servicesublevel::class, 'sublevel_id');
    }

    public function pricelistservices()
    {
        return $this->hasMany(pricelistservice::class, 'service_id');
    }
    public function bookingSurgery()
{
    return $this->hasMany(BookingSurgery::class, 'surgery_id');
}

}