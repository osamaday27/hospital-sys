<?php

namespace App\Traits;

use App\models\hr\hr_tax_conf;

trait TaxCalculationTrait
{
    /**
     * Calculate Egyptian progressive income tax
     * 
     * @param float $monthlyBasicSalary
     * @param float $monthlyAllowances
     * @param float $termAllowances (additional allowances for this payslip period)
     * @return array ['annual_tax' => float, 'monthly_tax' => float, 'breakdown' => array]
     */
    public function calculateEgyptianIncomeTax($monthlyBasicSalary, $monthlyAllowances = 0, $termAllowances = 0)
    {
        // Get active tax configuration
        $taxConfig = hr_tax_conf::where('status', true)->first();
        
        if (!$taxConfig) {
            throw new \Exception('No active tax configuration found');
        }

        // Step 1: Calculate annual taxable income
        $monthlyTotalIncome = $monthlyBasicSalary + $monthlyAllowances + $termAllowances;
        $annualTotalIncome = $monthlyTotalIncome * 12;
        
        // Step 2: Apply personal exemption
        $exemptionMargin = $taxConfig->exemption_margin; // Default: 20,000 EGP
        $taxableAnnualIncome = max(0, $annualTotalIncome - $exemptionMargin);
        
        // Step 3: Apply progressive tax brackets
        $taxBreakdown = $this->calculateProgressiveTax($taxableAnnualIncome, $taxConfig);
        
        // Step 4: Calculate monthly tax
        $annualTax = $taxBreakdown['total_tax'];
        $monthlyTax = $annualTax / 12;
        
        return [
            'annual_income' => $annualTotalIncome,
            'exemption_margin' => $exemptionMargin,
            'taxable_annual_income' => $taxableAnnualIncome,
            'annual_tax' => $annualTax,
            'monthly_tax' => round($monthlyTax, 2),
            'effective_tax_rate' => $annualTotalIncome > 0 ? round(($annualTax / $annualTotalIncome) * 100, 2) : 0,
            'breakdown' => $taxBreakdown['brackets']
        ];
    }

    /**
     * Calculate progressive tax based on Egyptian tax brackets (2025)
     * 
     * @param float $taxableIncome
     * @param hr_tax_conf $taxConfig
     * @return array
     */
    private function calculateProgressiveTax($taxableIncome, $taxConfig)
    {
        // Egyptian tax brackets and rates for 2025
        $brackets = [
            ['min' => 0, 'max' => $taxConfig->segment_1, 'rate' => 0.00],           // 0 - 40,000: 0%
            ['min' => $taxConfig->segment_1, 'max' => $taxConfig->segment_2, 'rate' => 0.10],  // 40,001 - 55,000: 10%
            ['min' => $taxConfig->segment_2, 'max' => $taxConfig->segment_3, 'rate' => 0.15],  // 55,001 - 70,000: 15%
            ['min' => $taxConfig->segment_3, 'max' => $taxConfig->segment_4, 'rate' => 0.20],  // 70,001 - 200,000: 20%
            ['min' => $taxConfig->segment_4, 'max' => $taxConfig->segment_5, 'rate' => 0.225], // 200,001 - 400,000: 22.5%
            ['min' => $taxConfig->segment_5, 'max' => PHP_FLOAT_MAX, 'rate' => 0.25],          // 400,001+: 25%
        ];

        $totalTax = 0;
        $bracketBreakdown = [];
        $remainingIncome = $taxableIncome;

        foreach ($brackets as $bracket) {
            if ($remainingIncome <= 0) {
                break;
            }

            // Calculate taxable amount in this bracket
            $bracketMin = $bracket['min'];
            $bracketMax = $bracket['max'];
            $bracketRate = $bracket['rate'];

            // Determine how much income falls in this bracket
            $incomeInBracket = 0;
            if ($taxableIncome > $bracketMin) {
                $incomeInBracket = min($remainingIncome, $bracketMax - $bracketMin);
                if ($taxableIncome <= $bracketMax) {
                    $incomeInBracket = $taxableIncome - $bracketMin;
                }
            }

            if ($incomeInBracket > 0) {
                $taxForBracket = $incomeInBracket * $bracketRate;
                $totalTax += $taxForBracket;

                $bracketBreakdown[] = [
                    'range' => $bracketMin . ' - ' . ($bracketMax == PHP_FLOAT_MAX ? '∞' : number_format($bracketMax)),
                    'rate' => ($bracketRate * 100) . '%',
                    'income_in_bracket' => $incomeInBracket,
                    'tax_amount' => round($taxForBracket, 2)
                ];

                $remainingIncome -= $incomeInBracket;
            }
        }

        return [
            'total_tax' => round($totalTax, 2),
            'brackets' => $bracketBreakdown
        ];
    }

    /**
     * Get total contract allowances for an employee
     * 
     * @param int $contractId
     * @return float
     */
    public function getTotalContractAllowances($contractId)
    {
        try {
            $contract = \App\models\hr\hr_contracts::with('allowances')->find($contractId);
            
            if (!$contract) {
                return 0;
            }

            return $contract->allowances->sum('amount') ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error getting contract allowances: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total term allowances from payslip
     * 
     * @param int $payslipId
     * @return float
     */
    public function getPayslipTermAllowances($payslipId)
    {
        try {
            $payslip = \App\Models\HR\hr_payslips::with('salary_lines')->find($payslipId);
            
            if (!$payslip) {
                return 0;
            }

            // Sum all positive salary line items that are allowances
            // Filter by amount > 0 to get allowances (positive values)
            // Exclude basic salary and other standard deductions
            return $payslip->salary_lines
                ->where('amount', '>', 0)
                ->sum('amount') ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error getting payslip term allowances: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate tax for a specific payslip
     * 
     * @param int $payslipId
     * @return array
     */
    public function calculatePayslipTax($payslipId)
    {
        $payslip = \App\Models\HR\hr_payslips::with(['employee.contracts'])->find($payslipId);
        
        if (!$payslip) {
            throw new \Exception('Payslip not found');
        }

        // Get employee's active contract
        $activeContract = $payslip->employee->contracts()->where('status', 'open')->first();
        
        if (!$activeContract) {
            throw new \Exception('No active contract found for employee');
        }

        // Get basic salary and allowances
        $basicSalary = $activeContract->basic_salary ?? 0;
        $contractAllowances = $this->getTotalContractAllowances($activeContract->id);
        $termAllowances = $this->getPayslipTermAllowances($payslipId);

        return $this->calculateEgyptianIncomeTax($basicSalary, $contractAllowances, $termAllowances);
    }

    /**
     * Preview tax calculation for an employee without creating payslip
     * 
     * @param int $employeeId
     * @param float $additionalAllowances
     * @return array
     */
    public function previewEmployeeTax($employeeId, $additionalAllowances = 0)
    {
        $employee = \App\models\hr\Employee::with('contracts')->find($employeeId);
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        // Get employee's active contract
        $activeContract = $employee->contracts()->where('status', 'open')->first();
        
        if (!$activeContract) {
            throw new \Exception('No active contract found for employee');
        }

        // Get basic salary and allowances
        $basicSalary = $activeContract->basic_salary ?? 0;
        $contractAllowances = $this->getTotalContractAllowances($activeContract->id);

        return $this->calculateEgyptianIncomeTax($basicSalary, $contractAllowances, $additionalAllowances);
    }
}
