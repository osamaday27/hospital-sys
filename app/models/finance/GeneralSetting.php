<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $table = 'fin_general_settings';

    protected $fillable = [
        'default_currency_id',
    ];

    public function defaultCurrency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }
}