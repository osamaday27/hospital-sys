<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class nutritionalscreening extends Model
{
    protected $table = 'nutritional_screenings';

    protected $fillable = [
        'patient_id','visit_id','reservation_id','user_add',
        'special_diet',
        'sugar_high','sugar_low','sugar_normal',
        'salt_high','salt_low','salt_normal',
        'protein_high','protein_low','protein_normal',
        'fat_high','fat_low','fat_normal',
        'nutrition_normal','nutrition_nausea','nutrition_vomiting',
        'nutrition_weight_loss','nutrition_dysphagia',
        'mouth_normal','mouth_dry','mouth_inflammation',
        'teeth_normal','teeth_loss','teeth_denture',
        'women_na','pregnant','breastfeeding',
        'elderly_na','elderly_over70',
        'risk_low','risk_high',
        'notify_doctor_yes','notify_doctor_no',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class,'user_add');
    }
}