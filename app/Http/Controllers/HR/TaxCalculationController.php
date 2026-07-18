<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Traits\TaxCalculationTrait;
use App\models\hr\hr_tax_conf;
use App\models\hr\Employee;
use App\Models\HR\hr_payslips;
use Illuminate\Http\Request;

class TaxCalculationController extends Controller
{
    use TaxCalculationTrait;

    /**
     * Preview tax calculation for an employee
     */
    public function previewEmployeeTax(Request $request, $employeeId)
    {
        $request->validate([
            'additional_allowances' => 'nullable|numeric|min:0'
        ]);

        try {
            $additionalAllowances = $request->input('additional_allowances', 0);
            $taxCalculation = $this->previewEmployeeTax($employeeId, $additionalAllowances);

            return response()->json([
                'success' => true,
                'data' => $taxCalculation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Test tax calculation with sample data (matches user's example)
     */
    public function testTaxCalculation()
    {
        try {
            // Test case from user's example: basic_salary + allowances = 40,100
            // This should result in annual tax of approximately 89,750 EGP
            $monthlyBasicSalary = 35000; // 35,000 EGP
            $monthlyAllowances = 5100;   // 5,100 EGP (total monthly = 40,100)
            $termAllowances = 0;

            $result = $this->calculateEgyptianIncomeTax($monthlyBasicSalary, $monthlyAllowances, $termAllowances);

            return response()->json([
                'success' => true,
                'test_case' => [
                    'monthly_basic_salary' => $monthlyBasicSalary,
                    'monthly_allowances' => $monthlyAllowances,
                    'monthly_total' => $monthlyBasicSalary + $monthlyAllowances,
                    'annual_total' => ($monthlyBasicSalary + $monthlyAllowances) * 12
                ],
                'tax_calculation' => $result,
                'verification' => [
                    'expected_annual_tax' => 89750, // From user's example
                    'calculated_annual_tax' => $result['annual_tax'],
                    'difference' => abs(89750 - $result['annual_tax']),
                    'matches_expected' => abs(89750 - $result['annual_tax']) < 100 // Allow small rounding differences
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active tax configuration
     */
    public function getTaxConfiguration()
    {
        try {
            $taxConfig = hr_tax_conf::where('status', true)->first();
            
            if (!$taxConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active tax configuration found'
                ], 404);
            }

            // Format tax brackets for display
            $brackets = [
                ['range' => '0 - ' . number_format($taxConfig->segment_1), 'rate' => '0%'],
                ['range' => number_format($taxConfig->segment_1 + 1) . ' - ' . number_format($taxConfig->segment_2), 'rate' => '10%'],
                ['range' => number_format($taxConfig->segment_2 + 1) . ' - ' . number_format($taxConfig->segment_3), 'rate' => '15%'],
                ['range' => number_format($taxConfig->segment_3 + 1) . ' - ' . number_format($taxConfig->segment_4), 'rate' => '20%'],
                ['range' => number_format($taxConfig->segment_4 + 1) . ' - ' . number_format($taxConfig->segment_5), 'rate' => '22.5%'],
                ['range' => number_format($taxConfig->segment_5 + 1) . ' - ∞', 'rate' => '25%']
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'exemption_margin' => $taxConfig->exemption_margin,
                    'tax_brackets' => $brackets,
                    'config_name' => $taxConfig->name,
                    'status' => $taxConfig->status ? 'Active' : 'Inactive'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate tax for all employees (bulk calculation)
     */
    public function bulkTaxCalculation(Request $request)
    {
        $request->validate([
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        try {
            $employeeIds = $request->input('employee_ids');
            
            // If no specific employees provided, calculate for all active employees
            if (!$employeeIds) {
                $employees = Employee::where('employee_status', 'in_service')->get();
            } else {
                $employees = Employee::whereIn('id', $employeeIds)->get();
            }

            $results = [];
            $errors = [];

            foreach ($employees as $employee) {
                try {
                    $taxCalculation = $this->previewEmployeeTax($employee->id);
                    $results[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->full_name,
                        'monthly_tax' => $taxCalculation['monthly_tax'],
                        'annual_tax' => $taxCalculation['annual_tax'],
                        'effective_rate' => $taxCalculation['effective_tax_rate'] . '%'
                    ];
                } catch (\Exception $e) {
                    $errors[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->full_name,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_employees' => count($employees),
                    'successful_calculations' => count($results),
                    'failed_calculations' => count($errors),
                    'results' => $results,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate tax for existing payslips
     */
    public function recalculatePayslipTaxes(Request $request)
    {
        $request->validate([
            'payslip_ids' => 'nullable|array',
            'payslip_ids.*' => 'exists:hr_payslips,id'
        ]);

        try {
            $payslipIds = $request->input('payslip_ids');
            
            if (!$payslipIds) {
                // Recalculate for all payslips that don't have tax calculated
                $payslips = hr_payslips::whereNull('tax_amount')->orWhere('tax_amount', 0)->get();
            } else {
                $payslips = hr_payslips::whereIn('id', $payslipIds)->get();
            }

            $results = [];
            $errors = [];

            foreach ($payslips as $payslip) {
                try {
                    $taxCalculation = $payslip->calculateTax();
                    if ($taxCalculation) {
                        $results[] = [
                            'payslip_id' => $payslip->id,
                            'employee_name' => $payslip->employee->full_name ?? 'Unknown',
                            'period' => $payslip->date_from->format('Y-m') . ' to ' . $payslip->date_to->format('Y-m'),
                            'tax_amount' => $taxCalculation['monthly_tax'],
                            'net_amount' => $payslip->net_amount
                        ];
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'payslip_id' => $payslip->id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_payslips' => count($payslips),
                    'successful_calculations' => count($results),
                    'failed_calculations' => count($errors),
                    'results' => $results,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
