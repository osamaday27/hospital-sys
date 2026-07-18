<?php


namespace App\models\administrative;

use App\models\User;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\administrative\adm_s_answer_type;


class adm_s_answer_values extends Model
{

    use SoftDeletes,TracksChanges;
    protected $table = 'adm_s_answer_values';
    protected $fillable = ['name_en', 'name_ar', 'type_id', 'value'];


    // علاقة مع جدول الأنواع
    public function type()
    {
        return $this->belongsTo(adm_s_answer_type::class, 'type_id');
    }
}