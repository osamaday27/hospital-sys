<?php
namespace App\Models\administrative;

use Illuminate\Database\Eloquent\Model;

class AbsenceReason extends Model
{
    protected $table = 'adm_s_absence_reasons';
    protected $fillable = ['name_ar', 'name_en', 'status'];
}