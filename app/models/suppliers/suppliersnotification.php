<?php

namespace App\Models\suppliers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suppliersnotification extends Model
{
    use HasFactory;
    protected $table = 'supplier_notifications';

    protected $fillable = [
        'general_setting_id',
        'label',
        'value',
        'value_type',
        'action_type',
        'category',
    ];

    public function generalSetting()
    {
        return $this->belongsTo(suppliergeneralsetting::class, 'general_setting_id');
    }
}