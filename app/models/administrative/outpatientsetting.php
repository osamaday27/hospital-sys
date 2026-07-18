<?php

namespace App\models\administrative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class outpatientsetting extends Model
{
    use SoftDeletes;

    protected $table = 'fin_outpatient_settings';

    protected $fillable = [
        'gl_account_id',
    ];

    protected $dates = ['deleted_at'];

    // علاقة بالحساب المحاسبي
    public function glAccount()
    {
        return $this->belongsTo(glaccount::class, 'gl_account_id');
    }
}