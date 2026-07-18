<?php


namespace App\models\administrative;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class adm_s_complaint_category extends Model
{
    use SoftDeletes;

    protected $table = 'adm_s_complaint_categories';
    protected $fillable = [
        'name_en',
        'name_ar',
        'status',
    ];

    /**
     * علاقة الفئة بالمشاكل المرتبطة بها
     */
    public function problems()
    {
        return $this->hasMany(adm_s_complaint_problem::class, 'category_id');
    }
}