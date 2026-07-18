<?php

namespace App\models\general;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TracksChanges;
class nationalities extends Model
{

    use HasTranslations,TracksChanges;
    protected $fillable = [
        'name',
        'default',
        'id_country',
        'status',

    ];
    public $translatable = ['name'];
    protected $table = 'nationalities_settings';
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function countries(){
        return $this->belongsTo (countries::class, 'id_country');
    }

}