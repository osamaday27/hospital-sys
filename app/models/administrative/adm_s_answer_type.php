<?php

namespace App\models\administrative;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\models\User;


class adm_s_answer_type  extends Model
{
    use SoftDeletes,TracksChanges;

    protected $fillable = ['name_en', 'name_ar', 'type', 'created_by'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}