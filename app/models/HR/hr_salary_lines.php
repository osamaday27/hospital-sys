<?php

namespace App\Models\HR;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class hr_salary_lines extends Model
{
    protected $table = 'hr_salary_lines';
    
    protected $fillable = [
        'employee_id',
        'payslip_id',
        'salary_rule_id',
        'salary_rule_type',
        'amount'
    ];

    protected $casts = [
        'amount' => 'float'
    ];

    /**
     * Get the employee that owns the salary line.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the payslip that owns the salary line.
     */
    public function payslip(): BelongsTo
    {
        return $this->belongsTo(hr_payslips::class, 'payslip_id');
    }

    /**
     * Get the salary rule that owns the salary line.
     */
    public function salary_rule(): BelongsTo
    {
        return $this->belongsTo(hr_salary_rules::class, 'salary_rule_id');
    }
} 