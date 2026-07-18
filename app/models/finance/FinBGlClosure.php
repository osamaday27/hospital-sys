<?php
// app/Models/finance/FinBGlClosure.php
namespace App\Models\finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinBGlClosure extends Model {
    use SoftDeletes;

    protected $fillable = [
        'period_start','period_end','type','status','created_by','approved_by'
    ];

    public function lines() {
        return $this->hasMany(FinBGlClosureLine::class, 'closure_id');
    }
}