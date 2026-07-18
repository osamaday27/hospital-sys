<?php

namespace App\models\administrative;
use App\models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksChanges;

class how_to_identify_place extends Model
{
    use HasFactory, SoftDeletes, TracksChanges;
    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'adm_s_how_to_identify_place';

    protected $fillable = ['name_ar','name_en', 'status', 'user_add'];

  
    public function user()
    {
        return $this->belongsTo(User::class, 'user_add');
    }
}