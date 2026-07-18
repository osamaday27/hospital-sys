<?php

namespace App\Models\HR;

use App\Models\HR\Employee;
use App\Models\HR\hr_salary_lines;
use App\Traits\TaxCalculationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class hr_payslips extends Model
{
    use TaxCalculationTrait;
    
    protected $table = 'hr_payslips';
    
    protected $fillable = [
        'employee_id',
        'date_from',
        'date_to',
        'total_amount',
        'gross_amount',
        'tax_amount',
        'net_amount',
        'status'
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'total_amount' => 'float',
        'gross_amount' => 'float',
        'tax_amount' => 'float',
        'net_amount' => 'float'
    ];

    /**
     * Get the employee that owns the payslip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the salary lines for the payslip.
     */
    public function salary_lines(): HasMany
    {
        return $this->hasMany(hr_salary_lines::class, 'payslip_id');
    }

    /**
     * Calculate and update tax for this payslip
     */
    public function calculateTax()
    {
        try {
            $taxCalculation = $this->calculatePayslipTax($this->id);
            
            $this->update([
                'tax_amount' => $taxCalculation['monthly_tax'],
                'net_amount' => $this->gross_amount - $taxCalculation['monthly_tax']
            ]);

            return $taxCalculation;
        } catch (\Exception $e) {
            \Log::error('Tax calculation failed for payslip ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get tax calculation details without saving
     */
    public function getTaxPreview()
    {
        return $this->calculatePayslipTax($this->id);
    }

    /**
     * Boot method to automatically calculate tax when payslip is created/updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($payslip) {
            // Auto-calculate tax when gross_amount is set
            if ($payslip->gross_amount && !$payslip->tax_amount) {
                $payslip->calculateTax();
            }
        });
    }
} 