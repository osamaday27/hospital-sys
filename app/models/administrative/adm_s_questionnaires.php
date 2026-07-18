<?php

namespace App\Models\administrative;

use App\Traits\TracksChanges;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class adm_s_questionnaires extends Model
{
    use SoftDeletes,TracksChanges;

    protected $fillable = ['name_ar', 'name_en', 'type', 'status'];


}
