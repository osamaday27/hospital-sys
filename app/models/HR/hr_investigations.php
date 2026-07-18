<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class hr_investigations extends Model
{
    protected $table = 'hr_investigations';
    
    protected $fillable = [
        'employee_id',
        'investigation_address',
        'investigation_type',
        'investigation_details',
        'action_taken'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
} 