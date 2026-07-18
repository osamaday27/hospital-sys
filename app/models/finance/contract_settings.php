<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class contract_settings extends Model 
{
    use HasTranslations;

       protected $fillable = [
        'name',
        'id_company',
        'id_pricing',
        'discount',
        'discount_type',
        'patient_contribution',
        'patient_contribution_type',
        'account_id',
        'user_id',
        

    ];
    public $timestamps = true;
    public $translatable = ['name'];
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'contract';
    public function company(){
        return $this->belongsTo (company_settings::class, 'id_company');
    }
    public function pricing(){
        return $this->belongsTo (pricing_settings::class, 'id_pricing');
    }
}