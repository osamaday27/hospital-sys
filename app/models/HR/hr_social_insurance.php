<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\HR\Employee;

class hr_social_insurance extends Model
{
    protected $table = 'hr_social_insurance';

    protected $fillable = [
        'employee_id',
        'insurance_status',
        'subscription_date',
        'insurance_office',
        'insurance_number',
        'health_status',
        'insurance_company',
        'main_salary',
        'shared_salary'
    ];

    protected $casts = [
        'insurance_status' => 'boolean',
        'health_status' => 'boolean',
        'subscription_date' => 'date',
        'main_salary' => 'decimal:2',
        'shared_salary' => 'decimal:2'
    ];

    /**
     * Get the employee that owns the social insurance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
} 