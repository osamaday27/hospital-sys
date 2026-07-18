<?php

// app/Models/finance/FinBGlClosureLine.php
namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinBGlClosureLine extends Model {
    use SoftDeletes;

    protected $fillable = ['closure_id','account_id','debit','credit'];

    public function closure() {
        return $this->belongsTo(FinBGlClosure::class, 'closure_id');
    }
}