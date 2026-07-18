<?php

namespace App\models\administrative;

use App\models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class adm_b_complaint_status extends Model
{
    use SoftDeletes;

    protected $table = 'adm_b_complaint_status';

    protected $fillable = [
        'name_en',
        'name_ar',
        'responsible_id',
        'complaint_id',
        'action',
        'updated_by',
    ];

    protected $casts = [
        'responsible_id' => 'array',
    ];

    public function complaint()
    {
        return $this->belongsTo(adm_b_complaint::class, 'complaint_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}