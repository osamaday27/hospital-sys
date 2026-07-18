<?php

namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class namedictionary extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'gender', 'status', 'user_id'];
    protected $table = 'gs_name_dictionaries';
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}