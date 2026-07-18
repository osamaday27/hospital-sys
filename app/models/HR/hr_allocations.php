<?php

namespace App\models\hr;
use App\models\hr\hr_vacation_types;
use App\models\hr\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class hr_allocations extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocation_name',
        'vacation_type',
        'mode',
        'valid_from',
        'valid_to',
        'amount',
        'description'
    ];

    public function vacation_type_name()
    {
        return $this->belongsTo(hr_vacation_types::class, 'vacation_type');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_hr_allocation', 'hr_allocation_id', 'employee_id');
    }

}