<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hr_contracts extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_name',
        'employee_id',
        'start_date',
        'end_date',
        'job_id',
        'department_id',
        'salary_structure_type',
        'status'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function job()
    {
        return $this->belongsTo(jobs::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
