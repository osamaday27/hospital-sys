<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\hr_payslips;
use App\Models\HR\hr_salary_lines;
use App\Models\HR\hr_salary_rules;
use App\models\hr\hr_loans;
use App\Traits\TaxCalculationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrpayslipscontroller extends Controller
{
    use TaxCalculationTrait;
    /**
     * Display a listing of the payslips.
     */
    public function index()
    {
        return view('hr.payroll.programs.payslips.index');
    }

    /**
     * Show the form for creating a new payslip.
     */
    public function create()
    {
        $employees = Employee::all();
        $salaryRules = hr_salary_rules::orderBy('sequence')->get();
        $automaticRules = hr_salary_rules::where('added_automatically', true)
            ->orderBy('sequence')
            ->get();
        
        // If no automatic rules found, initialize as empty collection
        if ($automaticRules->isEmpty()) {
            $automaticRules = collect([]);
        }
        
        // Ensure salaryRules is not empty
        if ($salaryRules->isEmpty()) {
            $salaryRules = collect([]);
        }
        
        return view('hr.payroll.programs.payslips.create', compact('employees', 'salaryRules', 'automaticRules'));
    }

    /**
     * Store a newly created payslip in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'salary_lines' => 'required|array',
            'salary_lines.*.salary_rule_id' => 'required|exists:hr_salary_rules,id',
            'salary_lines.*.amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check if employee has an active contract
            $employee = Employee::with('activeContract.allowances')->findOrFail($request->employee_id);
            
            if (!$employee->activeContract) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['employee_id' => trans('hr.no_active_contract')]);
            }

            $contract = $employee->activeContract;

            // Validate contract is not expired
            $currentDate = now();
            if ($contract->end_date && $currentDate->gt($contract->end_date)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['employee_id' => trans('hr.contract_expired', ['end_date' => $contract->end_date->format('Y-m-d')])]);
            }

            // Validate salary level is supported (monthly, hours, shifts)
            $allowedSalaryLevels = ['monthly', 'hours', 'shifts'];
            if ($contract->salary_level && !in_array(strtolower($contract->salary_level), $allowedSalaryLevels)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['employee_id' => trans('hr.unsupported_salary_level', ['salary_level' => $contract->salary_level])]);
            }

            // Handle different salary levels
            $salaryLevel = strtolower($contract->salary_level ?? 'monthly');
            
            if ($salaryLevel === 'hours') {
                // Hourly employee: calculate gross then apply deductions
                $workedHours = $this->calculateWorkedHours($request->employee_id, $request->date_from, $request->date_to);
                $hourWage = $contract->hour_amount ?? 0;
                $grossSalary = $workedHours * $hourWage;
                
                // Calculate deductions for hourly employees
                $loanDeduction = $this->calculateLoanInstallment($request->employee_id, $request->date_from, $request->date_to);
                $penaltyDeduction = $this->calculatePenalties($request->employee_id, $request->date_from, $request->date_to);
                
                $totalDeductions = $loanDeduction + $penaltyDeduction;
                $netSalary = $grossSalary - $totalDeductions;
                
                \Log::info("Hourly payslip calculation for employee {$request->employee_id}:");
                \Log::info("- Worked hours: {$workedHours}");
                \Log::info("- Hour wage: {$hourWage}");
                \Log::info("- Gross salary: {$grossSalary}");
                \Log::info("- Loan deduction: {$loanDeduction}");
                \Log::info("- Penalty deduction: {$penaltyDeduction}");
                \Log::info("- Net salary: {$netSalary}");
                \Log::info("- Period: {$request->date_from} to {$request->date_to}");
                
                $payslip = hr_payslips::create([
                    'employee_id' => $request->employee_id,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'status' => 'draft',
                    'total_amount' => $netSalary,
                    'gross_amount' => $grossSalary,
                    'net_amount' => $netSalary
                ]);

                // Create salary lines for hourly employees
                $this->createHourlySalaryLines($payslip->id, $grossSalary, $loanDeduction, $penaltyDeduction, $netSalary);

                DB::commit();
                return redirect()->route('hr_payslips.index')->with('success', trans('hr.payslip_created_successfully'));
            }

            $payslip = hr_payslips::create([
                'employee_id' => $request->employee_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => 'draft',
                'total_amount' => 0
            ]);

            $totalAmount = 0;
            $basicSalaryAmount = 0;
            $allowancesAmount = 0;
            $grossSalaryAmount = 0;
            $totalAdditions = 0;
            $totalDeductions = 0;

            foreach ($request->salary_lines as $line) {
                $salaryRule = hr_salary_rules::find($line['salary_rule_id']);
                $amount = $line['amount'] ?? 0;
                
                // Auto-calculate amounts based on rule type and salary level
                switch ($salaryRule->rule_type) {
                    case 'basic_salary':
                        if ($salaryLevel === 'shifts') {
                            // For shift employees: basic salary = attendance days × shift amount
                            $attendanceDays = $this->calculateAttendanceDays($request->employee_id, $request->date_from, $request->date_to);
                            $amount = $attendanceDays * ($contract->shift_amount ?? 0);
                        } else {
                            // For monthly employees: use contract basic salary
                            $amount = $contract->basic_salary ?? 0;
                        }
                        $basicSalaryAmount = $amount;
                        break;
                        
                    case 'allowances':
                        // Sum of all allowances for this contract
                        $amount = $contract->allowances->sum('amount');
                        $allowancesAmount = $amount;
                        break;
                        
                    case 'gross_salary':
                        // Calculate gross salary as basic salary + allowances
                        $basicSalary = $contract->basic_salary ?? 0;
                        $allowances = $contract->allowances->sum('amount');
                        $amount = $basicSalary + $allowances;
                        $grossSalaryAmount = $amount;
                        break;
                        
                    case 'tax':
                        // Calculate tax using Egyptian tax system
                        $basicSalary = $contract->basic_salary ?? 0;
                        $allowances = $contract->allowances->sum('amount');
                        $taxCalculation = $this->calculateEgyptianIncomeTax($basicSalary, $allowances, 0);
                        $amount = $taxCalculation['monthly_tax'];
                        break;
                        
                    case 'insurance':
                        // Calculate insurance based on hr_ins_conf
                        $basicSalary = $contract->basic_salary ?? 0;
                        $amount = $this->calculateInsurance($basicSalary);
                        break;
                        
                    case 'penalties':
                        // Calculate penalties for the period
                        $amount = $this->calculatePenalties($request->employee_id, $request->date_from, $request->date_to);
                        break;
                        
                    case 'loans':
                        // Calculate loan installment for the period
                        $amount = $this->calculateLoanInstallment($request->employee_id, $request->date_from, $request->date_to);
                        break;
                        
                    case 'absence':
                        // Calculate absence deduction based on attendance records
                        $basicSalary = $contract->basic_salary ?? 0;
                        $amount = $this->calculateAbsence($request->employee_id, $request->date_from, $request->date_to, $basicSalary);
                        break;
                        
                    case 'lateness':
                        // Calculate lateness deduction based on penalty hours
                        $basicSalary = $contract->basic_salary ?? 0;
                        $amount = $this->calculateLateness($request->employee_id, $request->date_from, $request->date_to, $basicSalary);
                        break;
                        
                    case 'net_salary':
                        // This will be calculated after all other lines are processed
                        $amount = 0; // Placeholder, will be calculated later
                        break;
                        
                    default:
                        // For other rule types, use the provided amount
                        $amount = $line['amount'] ?? 0;
                        break;
                }
                
                // Store the salary line
                hr_salary_lines::create([
                    'employee_id' => $request->employee_id,
                    'payslip_id' => $payslip->id,
                    'salary_rule_id' => $line['salary_rule_id'],
                    'salary_rule_type' => $salaryRule->type,
                    'amount' => $amount
                ]);

                // Track additions and deductions separately
                if ($salaryRule->type === 'addition') {
                    $totalAdditions += $amount;
                    $totalAmount += $amount;
                } else {
                    $totalDeductions += $amount;
                    $totalAmount -= $amount;
                }
            }

            // Calculate net salary and update the net_salary line if it exists
            $netSalary = $totalAdditions - $totalDeductions;
            $netSalaryLine = hr_salary_lines::where('payslip_id', $payslip->id)
                ->whereHas('salary_rule', function($query) {
                    $query->where('rule_type', 'net_salary');
                })
                ->first();
            
            if ($netSalaryLine) {
                $netSalaryLine->update(['amount' => $netSalary]);
            }

            $payslip->update(['total_amount' => $netSalary]);

            DB::commit();
            return redirect()->route('hr_payslips.index')->with('success', 'Payslip created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating payslip: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payslip.
     */
    public function show($id)
    {
        $payslip = hr_payslips::findOrFail($id);
        $payslip->load(['employee', 'salary_lines.salary_rule']);
        return view('hr.payroll.programs.payslips.show', compact('payslip'));
    }

    /**
     * Show the form for editing the specified payslip.
     */
    public function edit($id)
    {
        $employees = Employee::all();
        $salaryRules = hr_salary_rules::all();
        $payslip = hr_payslips::findOrFail($id);
        $payslip->load('salary_lines');
        return view('hr.payroll.programs.payslips.edit', compact('payslip', 'employees', 'salaryRules'));
    }

    /**
     * Update the specified payslip in storage.
     */
    public function update(Request $request, hr_payslips $payslip)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'salary_lines' => 'required|array',
            'salary_lines.*.salary_rule_id' => 'required|exists:hr_salary_rules,id',
            'salary_lines.*.amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check if employee has an active contract
            $employee = Employee::with('activeContract.allowances')->findOrFail($request->employee_id);
            
            if (!$employee->activeContract) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['employee_id' => trans('hr.no_active_contract')]);
            }

            $contract = $employee->activeContract;

            // Validate contract is not expired
            $currentDate = now();
            if ($contract->end_date && $currentDate->gt($contract->end_date)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['employee_id' => trans('hr.contract_expired', ['end_date' => $contract->end_date->format('Y-m-d')])]);
            }

            // Validate salary level is supported (monthly, hours, shifts)
            $allowedSalaryLevels = ['monthly', 'hours', 'shifts'];
            if ($contract->salary_level && !in_array(strtolower($contract->salary_level), $allowedSalaryLevels)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['employee_id' => trans('hr.unsupported_salary_level', ['salary_level' => $contract->salary_level])]);
            }

            $payslip->update([
                'employee_id' => $request->employee_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ]);

            // Delete existing salary lines
            $payslip->salary_lines()->delete();

            $totalAmount = 0;
            $basicSalaryAmount = 0;
            $allowancesAmount = 0;
            $grossSalaryAmount = 0;
            $totalAdditions = 0;
            $totalDeductions = 0;

            foreach ($request->salary_lines as $line) {
                $salaryRule = hr_salary_rules::find($line['salary_rule_id']);
                $amount = $line['amount'] ?? 0;
                
                // Auto-calculate amounts based on rule type
                switch ($salaryRule->rule_type) {
                    case 'basic_salary':
                        $amount = $contract->basic_salary ?? 0;
                        $basicSalaryAmount = $amount;
                        break;
                        
                    case 'allowances':
                        // Sum of all allowances for this contract
                        $amount = $contract->allowances->sum('amount');
                        $allowancesAmount = $amount;
                        break;
                        
                    case 'gross_salary':
                        // Calculate gross salary as basic salary + allowances
                        $basicSalary = $contract->basic_salary ?? 0;
                        $allowances = $contract->allowances->sum('amount');
                        $amount = $basicSalary + $allowances;
                        $grossSalaryAmount = $amount;
                        break;
                        
                    case 'tax':
                        // Calculate tax using Egyptian tax system
                        $basicSalary = $contract->basic_salary ?? 0;
                        $allowances = $contract->allowances->sum('amount');
                        $taxCalculation = $this->calculateEgyptianIncomeTax($basicSalary, $allowances, 0);
                        $amount = $taxCalculation['monthly_tax'];
                        break;
                        
                    case 'insurance':
                        // Calculate insurance based on hr_ins_conf
                        $basicSalary = $contract->basic_salary ?? 0;
                        $amount = $this->calculateInsurance($basicSalary);
                        break;
                        
                    case 'penalties':
                        // Calculate penalties for the period
                        $amount = $this->calculatePenalties($request->employee_id, $request->date_from, $request->date_to);
                        break;
                        
                    case 'loans':
                        // Calculate loan installment for the period
                        $amount = $this->calculateLoanInstallment($request->employee_id, $request->date_from, $request->date_to);
                        break;
                        
                    case 'absence':
                        // Calculate absence deduction based on attendance records
                        $basicSalary = $contract->basic_salary ?? 0;
                        $amount = $this->calculateAbsence($request->employee_id, $request->date_from, $request->date_to, $basicSalary);
                        break;
                        
                    case 'lateness':
                        // Calculate lateness deduction based on penalty hours
                        $basicSalary = $contract->basic_salary ?? 0;
                        $amount = $this->calculateLateness($request->employee_id, $request->date_from, $request->date_to, $basicSalary);
                        break;
                        
                    case 'net_salary':
                        // This will be calculated after all other lines are processed
                        $amount = 0; // Placeholder, will be calculated later
                        break;
                        
                    default:
                        // For other rule types, use the provided amount
                        $amount = $line['amount'] ?? 0;
                        break;
                }
                
                // Store the salary line
                hr_salary_lines::create([
                    'employee_id' => $request->employee_id,
                    'payslip_id' => $payslip->id,
                    'salary_rule_id' => $line['salary_rule_id'],
                    'salary_rule_type' => $salaryRule->type,
                    'amount' => $amount
                ]);

                // Track additions and deductions separately
                if ($salaryRule->type === 'addition') {
                    $totalAdditions += $amount;
                    $totalAmount += $amount;
                } else {
                    $totalDeductions += $amount;
                    $totalAmount -= $amount;
                }
            }

            // Calculate net salary and update the net_salary line if it exists
            $netSalary = $totalAdditions - $totalDeductions;
            $netSalaryLine = hr_salary_lines::where('payslip_id', $payslip->id)
                ->whereHas('salary_rule', function($query) {
                    $query->where('rule_type', 'net_salary');
                })
                ->first();
            
            if ($netSalaryLine) {
                $netSalaryLine->update(['amount' => $netSalary]);
            }

            $payslip->update(['total_amount' => $netSalary]);

            DB::commit();
            return redirect()->route('hr_payslips.index')->with('success', 'Payslip updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating payslip: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified payslip from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $payslip = hr_payslips::findOrFail($id);
            $payslip->salary_lines()->delete();
            $payslip->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get payslips data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function get_payslips()
    {
        $payslips = hr_payslips::with('employee')->get();

        return DataTables::of($payslips)
            ->editColumn('created_at', function ($payslip) {
                return Carbon::parse($payslip->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('employee_name', function ($payslip) {
                return $payslip->employee->full_name;
            })
            ->addColumn('period', function ($payslip) {
                return $payslip->date_from->format('Y-m-d') . ' - ' . $payslip->date_to->format('Y-m-d');
            })
            ->addColumn('total_amount', function ($payslip) {
                return number_format($payslip->total_amount, 2);
            })
            ->addColumn('status', function ($payslip) {
                $statusClass = $payslip->status === 'draft' ? 'warning' : ($payslip->status === 'confirmed' ? 'info' : 'success');
                return '<span class="badge bg-' . $statusClass . '">' . __('hr.status_' . $payslip->status) . '</span>';
            })
            ->addColumn('action', function ($payslip) {
                return '
                    <a href="' . route('hr_payslips.show', $payslip->id) . '" 
                       class="btn btn-info btn-sm">' . trans('buttons.show') . '</a>
                    <a href="' . route('hr_payslips.edit', $payslip->id) . '" 
                       class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="deleteFunction(' . $payslip->id . ')" 
                       class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Get employee contract information for payslip creation
     *
     * @param int $employeeId
     * @return \Illuminate\Http\Response
     */
    public function getEmployeeContractInfo($employeeId)
    {
        try {
            $employee = Employee::with('activeContract.allowances')->findOrFail($employeeId);
            
            if (!$employee->activeContract) {
                return response()->json([
                    'success' => false,
                    'message' => trans('hr.no_active_contract'),
                    'has_contract' => false
                ]);
            }

            $contract = $employee->activeContract;

            // Validate contract is not expired
            $currentDate = now();
            if ($contract->end_date && $currentDate->gt($contract->end_date)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('hr.contract_expired', ['end_date' => $contract->end_date->format('Y-m-d')]),
                    'has_contract' => false
                ]);
            }

            // Validate salary level is supported (monthly, hours, shifts)
            $allowedSalaryLevels = ['monthly', 'hours', 'shifts'];
            if ($contract->salary_level && !in_array(strtolower($contract->salary_level), $allowedSalaryLevels)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('hr.unsupported_salary_level', ['salary_level' => $contract->salary_level]),
                    'has_contract' => false
                ]);
            }
            
            // Get date_from and date_to from query params, or use 26th to 25th cycle as default
            $dateFrom = request('date_from');
            $dateTo = request('date_to');
            if (!$dateFrom || !$dateTo) {
                $now = Carbon::now();
                
                // Default payslip period: 26th of previous month to 25th of current month
                if ($now->day >= 26) {
                    // If today is 26th or later, period is 26th of this month to 25th of next month
                    $dateFrom = $now->copy()->day(26)->format('Y-m-d');
                    $dateTo = $now->copy()->addMonth()->day(25)->format('Y-m-d');
                } else {
                    // If today is before 26th, period is 26th of last month to 25th of this month
                    $dateFrom = $now->copy()->subMonth()->day(26)->format('Y-m-d');
                    $dateTo = $now->copy()->day(25)->format('Y-m-d');
                }
            }

            // Get salary level to determine calculation method
            $salaryLevel = strtolower($contract->salary_level ?? 'monthly');
            
            // Calculate based on salary level
            if ($salaryLevel === 'hours') {
                // Hourly employee: only calculate worked hours and net salary
                $workedHours = $this->calculateWorkedHours($employeeId, $dateFrom, $dateTo);
                $hourWage = $contract->hour_amount ?? 0;
                $grossSalary = $workedHours * $hourWage;
                
                // Calculate deductions for hourly employees
                $calculatedLoanInstallment = $this->calculateLoanInstallment($employeeId, $dateFrom, $dateTo);
                $calculatedPenalties = $this->calculatePenalties($employeeId, $dateFrom, $dateTo);
                
                $totalDeductions = $calculatedLoanInstallment + $calculatedPenalties;
                $netSalary = $grossSalary - $totalDeductions;
                
                \Log::info("Hourly contract info for employee {$employeeId}:");
                \Log::info("- Worked hours: {$workedHours}");
                \Log::info("- Hour wage: {$hourWage}");
                \Log::info("- Gross salary: {$grossSalary}");
                \Log::info("- Loan deduction: {$calculatedLoanInstallment}");
                \Log::info("- Penalty deduction: {$calculatedPenalties}");
                \Log::info("- Net salary: {$netSalary}");
                \Log::info("- Period: {$dateFrom} to {$dateTo}");
                
                return response()->json([
                    'success' => true,
                    'has_contract' => true,
                    'salary_level' => 'hours',
                    'contract' => [
                        'id' => $contract->id,
                        'contract_name' => $contract->contract_name,
                        'hour_wage' => $hourWage,
                        'worked_hours' => $workedHours,
                        'gross_salary' => $grossSalary,
                        'calculated_loan_installment' => $calculatedLoanInstallment,
                        'calculated_penalties' => $calculatedPenalties,
                        'net_salary' => $netSalary,
                        'period_from' => $dateFrom,
                        'period_to' => $dateTo,
                    ]
                ]);
                
            } elseif ($salaryLevel === 'shifts') {
                // Shift employee: calculate basic salary from attendance days
                $attendanceDays = $this->calculateAttendanceDays($employeeId, $dateFrom, $dateTo);
                $shiftAmount = $contract->shift_amount ?? 0;
                $basicSalary = $attendanceDays * $shiftAmount;
                $allowances = $contract->allowances->sum('amount');
                $grossSalary = $basicSalary + $allowances;
                
                // Calculate deductions for shift employees
                $taxCalculation = $this->calculateEgyptianIncomeTax($basicSalary, $allowances, 0);
                $calculatedTax = $taxCalculation['monthly_tax'];
                $calculatedInsurance = $this->calculateInsurance($basicSalary);
                $absence = $this->calculateAbsence($employeeId, $dateFrom, $dateTo, $basicSalary);
                $lateness = $this->calculateLateness($employeeId, $dateFrom, $dateTo, $basicSalary);
                $penalties = $this->calculatePenalties($employeeId, $dateFrom, $dateTo);
                $loanInstallment = $this->calculateLoanInstallment($employeeId, $dateFrom, $dateTo);
                
            } else {
                // Monthly employee: standard calculation
                $basicSalary = $contract->basic_salary ?? 0;
                $allowances = $contract->allowances->sum('amount');
                $taxCalculation = $this->calculateEgyptianIncomeTax($basicSalary, $allowances, 0);
                $calculatedTax = $taxCalculation['monthly_tax'];

                // Calculate insurance based on hr_ins_conf
                $calculatedInsurance = $this->calculateInsurance($basicSalary);

                // Calculate absence and lateness for the period
                $absence = $this->calculateAbsence($employeeId, $dateFrom, $dateTo, $basicSalary);
                $lateness = $this->calculateLateness($employeeId, $dateFrom, $dateTo, $basicSalary);
                
                // Calculate penalties for the period
                $penalties = $this->calculatePenalties($employeeId, $dateFrom, $dateTo);
                
                // Calculate loan installment for the period
                $loanInstallment = $this->calculateLoanInstallment($employeeId, $dateFrom, $dateTo);
            }
            
            // Return response for monthly and shift employees
            return response()->json([
                'success' => true,
                'has_contract' => true,
                'salary_level' => $salaryLevel,
                'contract' => [
                    'id' => $contract->id,
                    'contract_name' => $contract->contract_name,
                    'basic_salary' => $basicSalary,
                    'allowances' => $contract->allowances->map(function($allowance) {
                        return [
                            'id' => $allowance->id,
                            'name' => $allowance->name,
                            'amount' => $allowance->amount
                        ];
                    }),
                    'total_allowances' => $allowances,
                    'gross_salary' => $basicSalary + $allowances,
                    'calculated_tax' => $calculatedTax,
                    'calculated_insurance' => $calculatedInsurance,
                    'calculated_absence' => $absence,
                    'calculated_lateness' => $lateness,
                    'calculated_penalties' => $penalties,
                    'calculated_loan_installment' => $loanInstallment,
                    'period_from' => $dateFrom,
                    'period_to' => $dateTo,
                ] + ($salaryLevel === 'shifts' ? [
                    'shift_amount' => $shiftAmount,
                    'attendance_days' => $attendanceDays
                ] : [])
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getEmployeeContractInfo: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Employee ID: ' . $employeeId);
            \Log::error('Request parameters: ' . json_encode(request()->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employee contract information: ' . $e->getMessage(),
                'has_contract' => false,
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Calculate insurance based on hr_ins_conf configuration
     *
     * @param float $basicSalary
     * @return float
     */
    private function calculateInsurance($basicSalary)
    {
        try {
            // Get active insurance configuration
            $insConf = \DB::table('hr_ins_conf')->where('status', 1)->first();
            
            if (!$insConf) {
                return 0;
            }

            // Calculate insurance: x = basic_salary * 0.8, insurance = x * employee_saher
            $x = $basicSalary * 0.8;
            $employeeSaher = $insConf->employee_saher ?? 0.11; // Default to 11% if not found
            $insurance = $x * $employeeSaher;
            
            return round($insurance, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating insurance: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate penalties for the given period
     *
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @return float
     */
    private function calculatePenalties($employeeId, $dateFrom, $dateTo)
    {
        try {
            // Get employee's contract to calculate daily rate for days-based penalties
            $employee = \App\models\hr\Employee::find($employeeId);
            $contract = $employee->activeContract ?? null;
            $dailyRate = 0;
            
            if ($contract) {
                $basicSalary = $contract->basic_salary ?? 0;
                $allowances = $contract->allowances->sum('amount') ?? 0;
                $monthlyGross = $basicSalary + $allowances;
                $dailyRate = $monthlyGross / 30; // Assuming 30 days per month
            }

            // Get penalties where the penalty date is within the payslip period
            $penalties = \DB::table('hr_penalties')
                ->where('employee_id', $employeeId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->whereNull('deleted_at') // Handle soft deletes
                ->get();

            $totalPenalty = 0;
            
            foreach ($penalties as $penalty) {
                if ($penalty->penalty_type === 'amount') {
                    // Direct amount penalty
                    $totalPenalty += $penalty->amount ?? 0;
                } else if ($penalty->penalty_type === 'days') {
                    // Days-based penalty: deduct daily rate × number of days
                    $days = $penalty->no_days ?? 0;
                    $totalPenalty += $dailyRate * $days;
                }
            }

            \Log::info("Penalties calculated for employee {$employeeId}: {$totalPenalty} (Daily rate: {$dailyRate})");
            return round($totalPenalty, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating penalties: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate loan installment for the given period
     *
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @return float
     */
    private function calculateLoanInstallment($employeeId, $dateFrom, $dateTo)
    {
        try {
            \Log::info("Calculating loan installment for employee {$employeeId} from {$dateFrom} to {$dateTo}");
            
            // Get approved loans with remaining installments for this employee
            $activeLoans = hr_loans::where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->where('remaining_installments', '>', 0)
                ->get();

            \Log::info("Found " . $activeLoans->count() . " active loans for employee {$employeeId}");

            $totalInstallment = 0;
            
            foreach ($activeLoans as $loan) {
                \Log::info("Processing loan ID: {$loan->id}");
                \Log::info("- Loan type: {$loan->loan_type}");
                \Log::info("- Loan date: {$loan->loan_date}");
                \Log::info("- Amount: {$loan->amount}");
                \Log::info("- Installment value: {$loan->installment_value}");
                \Log::info("- Remaining installments: {$loan->remaining_installments}");
                
                // Check if loan should be deducted in this payslip period
                $loanDate = Carbon::parse($loan->loan_date);
                $periodStart = Carbon::parse($dateFrom);
                $periodEnd = Carbon::parse($dateTo);
                
                // For instant loans: deduct if loan date is within or before the payslip period
                if ($loan->loan_type === 'instant') {
                    if ($loanDate->lte($periodEnd) && $loan->remaining_installments > 0) {
                        $totalInstallment += $loan->installment_value;
                        \Log::info("- Added instant loan installment: {$loan->installment_value}");
                    }
                } else {
                    // For installment loans: deduct if loan date is within or before the payslip period
                    // and there are remaining installments
                    if ($loanDate->lte($periodEnd) && $loan->remaining_installments > 0) {
                        $totalInstallment += $loan->installment_value;
                        \Log::info("- Added installment loan deduction: {$loan->installment_value}");
                    }
                }
            }

            \Log::info("Total loan installment calculated for employee {$employeeId}: {$totalInstallment}");
            return round($totalInstallment, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating loan installment: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return 0;
        }
    }

    // ⚠️ OLD INCORRECT TAX CALCULATION REMOVED
    // Now using TaxCalculationTrait with proper Egyptian tax law implementation

    /**
     * Calculate absence deduction based on attendance records
     *
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param float $basicSalary
     * @return float
     */
    private function calculateAbsence($employeeId, $dateFrom, $dateTo, $basicSalary)
    {
        // Get employee with schedule
        $employee = Employee::with('schedule.days')->find($employeeId);
        
        if (!$employee || !$employee->schedule) {
            return 0;
        }

        // Get working days from schedule (days with start_time and end_time)
        $workingDays = $employee->schedule->days->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->pluck('day_of_week')
            ->toArray();

        if (empty($workingDays)) {
            return 0;
        }

        // Get vacation records for the period
        $vacationDays = \App\models\hr\hr_vacations::where('employee_id', $employeeId)
            ->where('approval_status', 'approved')
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('from', [$dateFrom, $dateTo])
                      ->orWhereBetween('to', [$dateFrom, $dateTo])
                      ->orWhere(function($q) use ($dateFrom, $dateTo) {
                          $q->where('from', '<=', $dateFrom)
                            ->where('to', '>=', $dateTo);
                      });
            })
            ->get();

        // Create a set of vacation dates
        $vacationDates = [];
        foreach ($vacationDays as $vacation) {
            $start = Carbon::parse($vacation->from);
            $end = Carbon::parse($vacation->to);
            
            while ($start <= $end) {
                $vacationDates[] = $start->format('Y-m-d');
                $start->addDay();
            }
        }

        // Calculate absence days
        $absenceDays = 0;
        $currentDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek + 1; // Convert to 1-7 format
            $currentDateStr = $currentDate->format('Y-m-d');
            
            // Check if this is a working day
            if (in_array($dayOfWeek, $workingDays)) {
                // Skip if this day has a vacation
                if (in_array($currentDateStr, $vacationDates)) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Check if there's no attendance record for this date
                $hasAttendance = \App\models\hr\hr_attendance::where('employee_id', $employeeId)
                    ->whereDate('check_in', $currentDateStr)
                    ->exists();
                
                if (!$hasAttendance) {
                    $absenceDays++;
                }
            }
            
            $currentDate->addDay();
        }

        // Calculate absence amount: (absence_days * basic_salary / 30.0)
        return $absenceDays * ($basicSalary / 30.0);
    }

    /**
     * Calculate lateness deduction based on penalty hours
     *
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param float $basicSalary
     * @return float
     */
    private function calculateLateness($employeeId, $dateFrom, $dateTo, $basicSalary)
    {
        // Get vacation records for the period
        $vacationDays = \App\models\hr\hr_vacations::where('employee_id', $employeeId)
            ->where('approval_status', 'approved')
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('from', [$dateFrom, $dateTo])
                      ->orWhereBetween('to', [$dateFrom, $dateTo])
                      ->orWhere(function($q) use ($dateFrom, $dateTo) {
                          $q->where('from', '<=', $dateFrom)
                            ->where('to', '>=', $dateTo);
                      });
            })
            ->get();

        // Create a set of vacation dates
        $vacationDates = [];
        foreach ($vacationDays as $vacation) {
            $start = Carbon::parse($vacation->from);
            $end = Carbon::parse($vacation->to);
            
            while ($start <= $end) {
                $vacationDates[] = $start->format('Y-m-d');
                $start->addDay();
            }
        }

        // Get total penalty hours for the period, excluding vacation days
        $totalPenaltyHours = \App\models\hr\hr_attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereNotIn(DB::raw('DATE(check_in)'), $vacationDates)
            ->sum('penalty');

        if ($totalPenaltyHours <= 0) {
            return 0;
        }

        // Calculate lateness amount: ((total_penalty_hours / 8) * (basic_salary / 30.0))
        return ($totalPenaltyHours / 8) * ($basicSalary / 30.0);
    }

    /**
     * Calculate total worked hours for hourly employees in the given period
     *
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @return float
     */
    private function calculateWorkedHours($employeeId, $dateFrom, $dateTo)
    {
        try {
            \Log::info("Calculating worked hours for employee {$employeeId} from {$dateFrom} to {$dateTo}");
            
            // Filter by check_in date column only (date column doesn't exist)
            $attendanceRecords = \App\models\hr\hr_attendance::where('employee_id', $employeeId)
                ->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->get(['check_in', 'worked_hours']);
            
            \Log::info("Found " . $attendanceRecords->count() . " attendance records");
            \Log::info("Date range: {$dateFrom} to {$dateTo}");
            
            foreach ($attendanceRecords as $record) {
                \Log::info("Record: check_in={$record->check_in}, worked_hours={$record->worked_hours}");
            }
            
            $totalHours = $attendanceRecords->sum('worked_hours');
            
            \Log::info("Total worked hours calculated: {$totalHours}");

            return round($totalHours ?? 0, 2);
        } catch (\Exception $e) {
            \Log::error('Error calculating worked hours: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate total attendance days for shift employees in the given period
     *
     * @param int $employeeId
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     */
    private function calculateAttendanceDays($employeeId, $dateFrom, $dateTo)
    {
        try {
            $attendanceDays = \App\models\hr\hr_attendance::where('employee_id', $employeeId)
                ->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count();

            return $attendanceDays;
        } catch (\Exception $e) {
            \Log::error('Error calculating attendance days: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Test method to debug hourly payslip calculation
     */
    public function testHourlyCalculation($employeeId, $dateFrom, $dateTo)
    {
        try {
            $employee = Employee::with('activeContract')->findOrFail($employeeId);
            $contract = $employee->activeContract;
            
            \Log::info("=== HOURLY PAYSLIP DEBUG TEST ===");
            \Log::info("Employee ID: {$employeeId}");
            \Log::info("Employee Name: {$employee->full_name}");
            \Log::info("Contract Salary Level: {$contract->salary_level}");
            \Log::info("Hour Amount: {$contract->hour_amount}");
            \Log::info("Date Range: {$dateFrom} to {$dateTo}");
            
            // Check attendance records
            $attendanceRecords = \App\models\hr\hr_attendance::where('employee_id', $employeeId)
                ->whereBetween('check_in', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->get();
            
            \Log::info("Attendance Records Found: " . $attendanceRecords->count());
            
            foreach ($attendanceRecords as $record) {
                \Log::info("- Date: {$record->check_in}, Worked Hours: {$record->worked_hours}");
            }
            
            $totalHours = $attendanceRecords->sum('worked_hours');
            $hourWage = $contract->hour_amount ?? 0;
            $netSalary = $totalHours * $hourWage;
            
            \Log::info("CALCULATION RESULTS:");
            \Log::info("- Total Hours: {$totalHours}");
            \Log::info("- Hour Wage: {$hourWage}");
            \Log::info("- Net Salary: {$netSalary}");
            \Log::info("=== END DEBUG TEST ===");
            
            return response()->json([
                'employee_id' => $employeeId,
                'employee_name' => $employee->full_name,
                'salary_level' => $contract->salary_level,
                'hour_wage' => $hourWage,
                'attendance_count' => $attendanceRecords->count(),
                'total_hours' => $totalHours,
                'net_salary' => $netSalary,
                'attendance_records' => $attendanceRecords->map(function($record) {
                    return [
                        'date' => $record->check_in,
                        'worked_hours' => $record->worked_hours
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in hourly calculation test: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create salary lines for hourly employees
     */
    private function createHourlySalaryLines($payslipId, $grossSalary, $loanDeduction, $penaltyDeduction, $netSalary)
    {
        try {
            // Gross salary line (using basic_salary rule for simplicity)
            $basicSalaryRule = hr_salary_rules::where('rule_type', 'basic_salary')->first();
            if ($basicSalaryRule && $grossSalary > 0) {
                hr_salary_lines::create([
                    'payslip_id' => $payslipId,
                    'salary_rule_id' => $basicSalaryRule->id,
                    'amount' => $grossSalary,
                ]);
            }
            
            // Loan deduction line
            $loansRule = hr_salary_rules::where('rule_type', 'loans')->first();
            if ($loansRule && $loanDeduction > 0) {
                hr_salary_lines::create([
                    'payslip_id' => $payslipId,
                    'salary_rule_id' => $loansRule->id,
                    'amount' => $loanDeduction,
                ]);
            }
            
            // Penalty deduction line
            $penaltiesRule = hr_salary_rules::where('rule_type', 'penalties')->first();
            if ($penaltiesRule && $penaltyDeduction > 0) {
                hr_salary_lines::create([
                    'payslip_id' => $payslipId,
                    'salary_rule_id' => $penaltiesRule->id,
                    'amount' => $penaltyDeduction,
                ]);
            }
            
            // Net salary line
            $netSalaryRule = hr_salary_rules::where('rule_type', 'net_salary')->first();
            if ($netSalaryRule) {
                hr_salary_lines::create([
                    'payslip_id' => $payslipId,
                    'salary_rule_id' => $netSalaryRule->id,
                    'amount' => $netSalary,
                ]);
            }
            
            \Log::info("Created hourly salary lines for payslip {$payslipId}");
            
        } catch (\Exception $e) {
            \Log::error('Error creating hourly salary lines: ' . $e->getMessage());
        }
    }

    // ⚠️ OLD INCORRECT TAX SEGMENTS CALCULATION REMOVED
    // Now using TaxCalculationTrait with proper Egyptian progressive tax brackets
} 