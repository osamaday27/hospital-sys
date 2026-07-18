<?php
namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class adm_s_complaint_responsible extends Model
{
    use SoftDeletes;

    protected $table = 'adm_s_complaint_responsible';

    protected $fillable = [
        'responsibles',
        'status',
    ];
}