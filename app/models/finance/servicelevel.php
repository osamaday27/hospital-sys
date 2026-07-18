<?php

namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;

class servicelevel extends Model
{
    use HasTranslations , TracksChanges;
    protected $fillable = ['name', 'hospital_code', 'level_type','default_branch','user_id','status'];
    public $translatable = ['name'];
    protected $table = 'fin_service_levels';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
  
    // Define the inverse of the relationship with servicesublevel
    public function servicesublevels()
    {
        return $this->hasMany(servicesublevel::class, 'level_id'); // The foreign key is 'level_id'
    }

  
}