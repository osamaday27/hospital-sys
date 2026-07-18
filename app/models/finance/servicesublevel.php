<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;

class servicesublevel extends Model
{
    use HasTranslations , TracksChanges;
    protected $fillable = ['name', 'hospital_code', 'level_id','default_branch','user_id','status'];
    public $translatable = ['name'];
    protected $table = 'fin_service_sublevels';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
  
    // Define the inverse of the relationship with servicesublevel
     public function level1()
    {
        return $this->belongsTo(servicelevel::class, 'level_id');
    }
 // علاقة مع الخدمات
    public function services()
    {
        return $this->hasMany(service::class, 'sublevel_id');
    }
  
}