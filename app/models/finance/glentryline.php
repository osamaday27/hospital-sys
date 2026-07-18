<?php

namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class glentryline extends Model
{
    use SoftDeletes;

    protected $table = 'fin_b_gl_entry_lines';

    protected $fillable = [
        'entry_id',
        'account_id',
        'debit',
        'credit',
    ];

    // السطر يتبع قيد رئيسي
    public function entry()
    {
        return $this->belongsTo(glentry::class, 'entry_id');
    }

    // الحساب الخاص بالسطر
    public function account()
    {
        return $this->belongsTo(glaccount::class, 'account_id');
    }
}