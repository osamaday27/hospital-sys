<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HR\Employee;
use App\models\hr\hr_tax_conf;
use App\models\hr\hr_ins_conf;

class TestPayslipLogic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payslip-logic {employee_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the payslip logic including tax calculation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing payslip logic...');
        
        // Test tax calculation
        $controller = new \App\Http\Controllers\HR\hrpayslipscontroller();
        $reflection = new \ReflectionClass($controller);
        $calculateTaxMethod = $reflection->getMethod('calculateTax');
        $calculateTaxMethod->setAccessible(true);
        
        $grossSalaries = [5000, 10000, 15000, 20000, 25000, 30000];
        
        foreach ($grossSalaries as $grossSalary) {
            $tax = $calculateTaxMethod->invoke($controller, $grossSalary);
            $this->line("Gross Salary: " . number_format($grossSalary, 2) . " -> Tax: " . number_format($tax, 2));
        }
        
        // Test absence and lateness calculations
        $this->info("\nTesting absence and lateness calculations...");
        
        // Get an employee with a contract
        $employee = \App\Models\HR\Employee::with('activeContract')->whereHas('activeContract')->first();
        
        if ($employee) {
            $this->info("Testing for employee: " . $employee->full_name);
            $this->info("Contract: " . $employee->activeContract->contract_name);
            $this->info("Basic Salary: " . number_format($employee->activeContract->basic_salary, 2));
            
            // Test absence calculation
            $calculateAbsenceMethod = $reflection->getMethod('calculateAbsence');
            $calculateAbsenceMethod->setAccessible(true);
            
            $dateFrom = '2024-01-01';
            $dateTo = '2024-01-31';
            $basicSalary = $employee->activeContract->basic_salary;
            
            // Debug schedule information
            if ($employee->schedule) {
                $this->info("Schedule: " . $employee->schedule->name);
                $workingDays = $employee->schedule->days->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->pluck('day_of_week')
                    ->toArray();
                $this->info("Working days: " . implode(', ', $workingDays));
            } else {
                $this->info("No schedule assigned to employee");
            }
            
            $absenceAmount = $calculateAbsenceMethod->invoke($controller, $employee->id, $dateFrom, $dateTo, $basicSalary);
            $this->info("Absence calculation for Jan 2024: " . number_format($absenceAmount, 2));
            
            // Calculate expected absence days
            $expectedAbsenceDays = $absenceAmount / ($basicSalary / 30.0);
            $this->info("Expected absence days: " . number_format($expectedAbsenceDays, 2));
            
            // Count working days in the period
            if ($employee->schedule) {
                $workingDays = $employee->schedule->days->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->pluck('day_of_week')
                    ->toArray();
                
                $workingDaysInPeriod = 0;
                $currentDate = \Carbon\Carbon::parse($dateFrom);
                $endDate = \Carbon\Carbon::parse($dateTo);
                
                while ($currentDate <= $endDate) {
                    $dayOfWeek = $currentDate->dayOfWeek + 1;
                    if (in_array($dayOfWeek, $workingDays)) {
                        $workingDaysInPeriod++;
                    }
                    $currentDate->addDay();
                }
                
                $this->info("Working days in period: " . $workingDaysInPeriod);
                $this->info("Total days in period: " . \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1);
            }
            
            // Test lateness calculation
            $calculateLatenessMethod = $reflection->getMethod('calculateLateness');
            $calculateLatenessMethod->setAccessible(true);
            
            $latenessAmount = $calculateLatenessMethod->invoke($controller, $employee->id, $dateFrom, $dateTo, $basicSalary);
            $this->info("Lateness calculation for Jan 2024: " . number_format($latenessAmount, 2));
            
            // Show attendance summary
            $attendanceCount = \App\models\hr\hr_attendance::where('employee_id', $employee->id)
                ->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count();
            
            $totalPenalty = \App\models\hr\hr_attendance::where('employee_id', $employee->id)
                ->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->sum('penalty');
            
            $this->info("Attendance records in period: " . $attendanceCount);
            $this->info("Total penalty hours: " . number_format($totalPenalty, 2));
            
            // Check for vacation records
            $vacationCount = \App\models\hr\hr_vacations::where('employee_id', $employee->id)
                ->where('approval_status', 'approved')
                ->where(function($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('from', [$dateFrom, $dateTo])
                          ->orWhereBetween('to', [$dateFrom, $dateTo])
                          ->orWhere(function($q) use ($dateFrom, $dateTo) {
                              $q->where('from', '<=', $dateFrom)
                                ->where('to', '>=', $dateTo);
                          });
                })
                ->count();
            
            $this->info("Approved vacation records in period: " . $vacationCount);
            
            // Test net salary calculation
            $this->info("\nTesting net salary calculation...");
            $basicSalary = $employee->activeContract->basic_salary;
            $allowances = $employee->activeContract->allowances->sum('amount');
            $grossSalary = $basicSalary + $allowances;
            $tax = $this->calculateTax($grossSalary);
            
            $totalAdditions = $basicSalary + $allowances;
            $totalDeductions = $tax + $absenceAmount + $latenessAmount;
            $netSalary = $totalAdditions - $totalDeductions;
            
            $this->info("Basic Salary: " . number_format($basicSalary, 2));
            $this->info("Allowances: " . number_format($allowances, 2));
            $this->info("Gross Salary: " . number_format($grossSalary, 2));
            $this->info("Tax: " . number_format($tax, 2));
            $this->info("Absence: " . number_format($absenceAmount, 2));
            $this->info("Lateness: " . number_format($latenessAmount, 2));
            $this->info("Total Additions: " . number_format($totalAdditions, 2));
            $this->info("Total Deductions: " . number_format($totalDeductions, 2));
            $this->info("Net Salary: " . number_format($netSalary, 2));
            
        } else {
            $this->error("No employee with contract found for testing.");
        }
        
        return 0;
    }

    private function testEmployeePayslip($employee)
    {
        $this->info("Testing payslip logic for employee: {$employee->full_name}");
        
        if (!$employee->activeContract) {
            $this->error("Employee does not have an active contract.");
            return;
        }

        $contract = $employee->activeContract;
        $basicSalary = $contract->basic_salary ?? 0;
        $allowances = $contract->allowances->sum('amount');
        $grossSalary = $basicSalary + $allowances;

        $this->info("Contract: {$contract->contract_name}");
        $this->info("Basic Salary: " . number_format($basicSalary, 2));
        $this->info("Total Allowances: " . number_format($allowances, 2));
        $this->info("Gross Salary: " . number_format($grossSalary, 2));

        // Test tax calculation
        $taxAmount = $this->calculateTax($grossSalary);
        $this->info("Calculated Tax: " . number_format($taxAmount, 2));
    }

    private function testTaxCalculation()
    {
        $this->info("Testing tax calculation logic...");
        
        // Test different gross salary amounts
        $testSalaries = [5000, 10000, 15000, 20000, 25000, 30000];
        
        foreach ($testSalaries as $salary) {
            $taxAmount = $this->calculateTax($salary);
            $this->info("Gross Salary: " . number_format($salary, 2) . " -> Tax: " . number_format($taxAmount, 2));
        }
    }

    private function calculateTax($grossSalary)
    {
        // Get active tax configuration
        $taxConf = hr_tax_conf::where('status', true)->first();
        
        if (!$taxConf) {
            return 0;
        }

        // Step 1: Calculate taxable income (gross salary / 1.3 * 12)
        $taxableIncome = ($grossSalary / 1.3) * 12;
        
        // Step 2: Subtract exemption margin
        $taxableIncome = $taxableIncome - $taxConf->exemption_margin;
        
        // If taxable income is negative or zero, no tax
        if ($taxableIncome <= 0) {
            return 0;
        }

        // Step 3: Calculate tax based on segments
        $taxAmount = $this->calculateTaxBySegments($taxableIncome, $taxConf);
        
        // Step 4: Return monthly tax (divide by 12)
        return $taxAmount / 12;
    }

    private function calculateTaxBySegments($taxableIncome, $taxConf)
    {
        $taxAmount = 0;

        // Define tax rates for each segment
        $taxRates = [
            1 => 0.00,    // 0% (0 -> 40000)
            2 => 0.10,    // 10% (40000 -> 55000)
            3 => 0.15,    // 15% (55000 -> 70000)
            4 => 0.20,    // 20% (70000 -> 200000)
            5 => 0.225,   // 22.5% (200000 -> 400000)
            6 => 0.25,    // 25% (400000 -> 1200000)
            7 => 0.275,   // 27.5% (> 1200000)
        ];

        // Define segment limits
        $segmentLimits = [
            1 => $taxConf->segment_1,   // 40000
            2 => $taxConf->segment_2,   // 55000
            3 => $taxConf->segment_3,   // 70000
            4 => $taxConf->segment_4,   // 200000
            5 => $taxConf->segment_5,   // 400000
            6 => $taxConf->segment_10,  // 1200000 (using segment_10 as the limit)
        ];

        // Calculate tax for each segment
        $previousLimit = 0;
        
        for ($i = 1; $i <= 6; $i++) {
            if ($taxableIncome <= $previousLimit) {
                break;
            }

            $currentLimit = $segmentLimits[$i];
            $segmentIncome = min($taxableIncome, $currentLimit) - $previousLimit;
            
            if ($segmentIncome > 0) {
                $taxAmount += $segmentIncome * $taxRates[$i];
            }
            
            $previousLimit = $currentLimit;
        }

        // Handle income above the highest segment (27.5% rate)
        if ($taxableIncome > $segmentLimits[6]) {
            $excessIncome = $taxableIncome - $segmentLimits[6];
            $taxAmount += $excessIncome * $taxRates[7];
        }

        return $taxAmount;
    }
} 