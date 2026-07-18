<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\models\hr\Employee; 

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_ne',
        'name_ar',
        'parent_department',
        'manager_id',
        'status',
    ];
    protected $table = 'hr_department';

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_department');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
