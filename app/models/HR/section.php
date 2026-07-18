<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\models\hr\Employee; 
use App\Traits\Costing\HasCostCenters;
class section extends Model
{
    use HasFactory, HasCostCenters;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_ne',
        'name_ar',
        'department',
        'manager_id',
        'status',
    ];
    protected $table = 'hr_sections';

    public function department_name()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}