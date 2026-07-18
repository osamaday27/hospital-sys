<?php


namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class adm_s_complaint_problem extends Model
{
    use SoftDeletes;

    protected $table = 'adm_s_complaint_problems';

    protected $fillable = [
        'category_id',
        'name_en',
        'name_ar',
        'status',
    ];

    /**
     * العلاقة بجدول فئات الشكوى
     */
    public function category()
    {
        return $this->belongsTo(adm_s_complaint_category::class, 'category_id');
    }
}