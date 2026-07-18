<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_contracts;
use App\models\hr\Employee;
use App\models\hr\Department;
use App\models\hr\jobs;
use App\models\hr\hr_ins_conf;
use App\models\hr\hr_tax_conf;
use App\models\HR\hr_allowances_types;
use App\models\HR\hr_allowances;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class hrcontractscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('hr.settings.contracts.index');
    }

    public function create()
    {
        $employees = Employee::all();
        $jobs = jobs::all();
        $departments = Department::all();
        $allowance_types = hr_allowances_types::all();
        return view('hr.settings.contracts.create', compact('employees', 'departments', 'jobs', 'allowance_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contract_name' => 'required|string|max:255|unique:hr_contracts,contract_name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:hr_department,id',
            'job_id' => 'required|exists:hr_jobs,id',
            'salary_level' => 'required|in:hours,shifts,monthly',
            'salary_structure_type' => 'required|in:operations,marketing,warehouse,physicians,admins',
            'status' => 'required',
            'hour_amount' => 'nullable|numeric',
            'shift_amount' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric'
        ]);

        // Enforce required amounts based on salary level
        $salaryLevel = $request->input('salary_level');
        if ($salaryLevel === 'hours') {
            $request->validate(['hour_amount' => 'required|numeric|min:0']);
        } elseif ($salaryLevel === 'shifts') {
            $request->validate(['shift_amount' => 'required|numeric|min:0']);
        } elseif ($salaryLevel === 'monthly') {
            $request->validate(['basic_salary' => 'required|numeric|min:0']);
        }

        $existingOpenContract = hr_contracts::where('employee_id', $request->input('employee_id'))
            ->where('status', 'open')
            ->exists();

        if ($existingOpenContract && $request->input('status') == 'open') {
            return redirect()->back()->withErrors(['status' => 'This employee already has an open contract.']);
        }

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $today = date('Y-m-d');
        $status = 'open';
        if ($today < $start_date) {
            $status = 'closed';
        } elseif ($today > $end_date) {
            $status = 'expired';
        }

        // Normalize amounts to avoid NULLs for unrelated fields
        $hourAmount = $salaryLevel === 'hours' ? (float) ($request->input('hour_amount') ?? 0) : 0;
        $shiftAmount = $salaryLevel === 'shifts' ? (float) ($request->input('shift_amount') ?? 0) : 0;
        $basicSalary = $salaryLevel === 'monthly' ? (float) ($request->input('basic_salary') ?? 0) : 0;

        $contract = hr_contracts::create([
            'contract_name' => $request->input('contract_name'),
            'employee_id' => $request->input('employee_id'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'department_id' => $request->input('department_id'),
            'job_id' => $request->input('job_id'),
            'salary_structure_type' => $request->input('salary_structure_type'),
            'status' => $status,
            'salary_level' => $request->input('salary_level'),
            'hour_amount' => $hourAmount,
            'shift_amount' => $shiftAmount,
            'basic_salary' => $basicSalary,
        ]);

        // Update employee contract_open_date and contract_end_date
        $employee = Employee::find($request->input('employee_id'));
        if ($employee) {
            $employee->contract_open_date = $start_date;
            $employee->contract_end_date = $end_date;
            $employee->save();
        }

        // Handle allowances if provided
        if ($request->has('allowances')) {
            foreach ($request->input('allowances') as $allowanceData) {
                hr_allowances::create([
                    'name' => $allowanceData['name'],
                    'allowance_id' => $allowanceData['allowance_id'],
                    'amount' => $allowanceData['amount'],
                    'contract_id' => $contract->id,
                ]);
            }
        }

        return redirect()->route('hr_contracts.index')->with('success', 'Contract created successfully!');
    }

    public function show($id)
    {
        $contract = hr_contracts::with('employee', 'department', 'job')->findOrFail($id);
        return view('hr.settings.contracts.show', compact('contract'));
    }

    public function edit($id)
    {
        $contract = hr_contracts::findOrFail($id);
        $employees = Employee::all();
        $jobs = jobs::all();
        $departments = Department::all();
        $allowance_types = hr_allowances_types::all();
        return view('hr.settings.contracts.edit', compact('contract', 'employees', 'departments', 'jobs', 'allowance_types'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'contract_name' => 'required|string|max:255|unique:hr_contracts,contract_name,' . $id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:hr_department,id',
            'job_id' => 'required|exists:hr_jobs,id',
            'salary_level' => 'required|in:hours,shifts,monthly',
            'salary_structure_type' => 'required|in:operations,marketing,warehouse,physicians,admins',
            'status' => 'required',
            'hour_amount' => 'nullable|numeric',
            'shift_amount' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric'
        ]);

        // Enforce required amounts based on salary level
        $salaryLevel = $request->input('salary_level');
        if ($salaryLevel === 'hours') {
            $request->validate(['hour_amount' => 'required|numeric|min:0']);
        } elseif ($salaryLevel === 'shifts') {
            $request->validate(['shift_amount' => 'required|numeric|min:0']);
        } elseif ($salaryLevel === 'monthly') {
            $request->validate(['basic_salary' => 'required|numeric|min:0']);
        }

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $today = date('Y-m-d');
        $status = 'open';
        if ($today < $start_date) {
            $status = 'closed';
        } elseif ($today > $end_date) {
            $status = 'expired';
        }

        $contract = hr_contracts::findOrFail($id);

        // Normalize amounts to avoid NULLs for unrelated fields
        $hourAmount = $salaryLevel === 'hours' ? (float) ($request->input('hour_amount') ?? 0) : 0;
        $shiftAmount = $salaryLevel === 'shifts' ? (float) ($request->input('shift_amount') ?? 0) : 0;
        $basicSalary = $salaryLevel === 'monthly' ? (float) ($request->input('basic_salary') ?? 0) : 0;

        $contract->update([
            'contract_name' => $request->input('contract_name'),
            'employee_id' => $request->input('employee_id'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'department_id' => $request->input('department_id'),
            'job_id' => $request->input('job_id'),
            'salary_structure_type' => $request->input('salary_structure_type'),
            'status' => $status,
            'salary_level' => $request->input('salary_level'),
            'hour_amount' => $hourAmount,
            'shift_amount' => $shiftAmount,
            'basic_salary' => $basicSalary,
        ]);

        // Update employee contract_open_date and contract_end_date
        $employee = Employee::find($request->input('employee_id'));
        if ($employee) {
            $employee->contract_open_date = $start_date;
            $employee->contract_end_date = $end_date;
            $employee->save();
        }

        // Handle allowances if provided
        if ($request->has('allowances')) {
            // Delete existing allowances for this contract
            hr_allowances::where('contract_id', $contract->id)->delete();
            
            // Create new allowances
            foreach ($request->input('allowances') as $allowanceData) {
                hr_allowances::create([
                    'name' => $allowanceData['name'],
                    'allowance_id' => $allowanceData['allowance_id'],
                    'amount' => $allowanceData['amount'],
                    'contract_id' => $contract->id,
                ]);
            }
        }

        return redirect()->route('hr_contracts.index')->with('success', 'Contract updated successfully!');
    }

    public function destroy($id)
    {
        $contract = hr_contracts::findOrFail($id);
        $contract->delete();

        return redirect()->route('hr_contracts.index')->with('success', 'Contract deleted successfully!');
    }

    public function getDepartmentJobByEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $department = $employee->emp_dep;
        $job = $employee->emp_pos;

        return response()->json([
            'department_id' => $department ? $department->id : null,
            'job_id' => $job ? $job->id : null
        ]);
    }

    public function get_contracts()
    {
        $contracts = hr_contracts::with('employee', 'job', 'department', 'allowances');

        return DataTables::of($contracts)
            ->addColumn('status', function ($contracts) {
                switch ($contracts->status) {
                    case 'open':
                        return '<span class="badge bg-success">' . trans('hr.contract_open') . '</span>';
                    case 'closed':
                        return '<span class="badge bg-warning">' . trans('hr.contract_closed') . '</span>';
                    case 'expired':
                        return '<span class="badge bg-danger">' . trans('hr.contract_expired') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('created_at', function ($contracts) {
                return Carbon::parse($contracts->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('employee', function ($contracts) {
                return $contracts->employee->name_ar;
            })
            ->addColumn('job', function ($contracts) {
                return $contracts->job->name_ar;
            })
            ->addColumn('department', function ($contracts) {
                return $contracts->department->name_ar;
            })
            ->addColumn('allowances', function ($contracts) {
                if ($contracts->allowances->count() > 0) {
                    $allowancesList = $contracts->allowances->map(function($allowance) {
                        return $allowance->name . ' (' . number_format($allowance->amount, 2) . ')';
                    })->join('<br>');
                    return $allowancesList;
                }
                return 'No allowances';
            })
            ->addColumn('action', function ($contracts) {
                $encryptedId = Crypt::encryptString($contracts->id);
                $deleteFunction = 'deleteFunction(' . $contracts->id . ')';

                return '
                    <a href="' . route('hr_contracts.edit', ['id' => $contracts->id]) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="' . $deleteFunction . '" class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['status', 'employee', 'job', 'department', 'allowances', 'action'])
            ->make(true);
    }

    // ======================== Calculations ========================

    private function calculateShohdaa($basic_salary)
    {
        return $basic_salary * 0.0005;
    }

    private function calculateSocialInsurance($basic_salary)
    {
        $ins_conf = hr_ins_conf::where('status', true)->first();
        if (!$ins_conf) {
            return 0;
        }

        $margin = $ins_conf->social_insurance_margin ?? 0;
        return ($basic_salary < $margin) ? $basic_salary : $margin;
    }

    private function calculateTax($basic_salary, $social_insurance)
    {
        $tax_conf = hr_tax_conf::where('status', true)->first();
        $ins_conf = hr_ins_conf::where('status', true)->first();

        if (!$tax_conf || !$ins_conf) {
            return 0;
        }

        $emp_share = $ins_conf->emp_share ?? 0;
        $taxable_income = (($basic_salary - ($social_insurance * ($emp_share / 100))) * 12) - $tax_conf->exemption_margin;
        $taxable_income = max(0, $taxable_income);

        return $this->calculateTaxValue($taxable_income, $tax_conf) / 12;
    }

    private function calculateTaxValue($tax_pool, $tax_conf)
    {
        if ($tax_pool <= $tax_conf->segment_1) {
            return 0;
        } elseif ($tax_pool <= $tax_conf->segment_2) {
            return ($tax_pool - 40000) * 0.1;
        } elseif ($tax_pool <= $tax_conf->segment_3) {
            return (55000 - 40000) * 0.1 + ($tax_pool - 55000) * 0.15;
        } elseif ($tax_pool <= $tax_conf->segment_4) {
            return (55000 - 40000) * 0.1 + (70000 - 55000) * 0.15 + ($tax_pool - 70000) * 0.2;
        } elseif ($tax_pool <= $tax_conf->segment_5) {
            return (55000 - 40000) * 0.1 + (70000 - 55000) * 0.15 + (200000 - 70000) * 0.2 + ($tax_pool - 200000) * 0.225;
        } elseif ($tax_pool <= $tax_conf->segment_6) {
            return (55000 - 40000) * 0.1 + (70000 - 55000) * 0.15 + (200000 - 70000) * 0.2 + (400000 - 200000) * 0.225 + ($tax_pool - 400000) * 0.25;
        } elseif ($tax_pool <= $tax_conf->segment_7) {
            return 55000 * 0.1 + (70000 - 55000) * 0.15 + (200000 - 70000) * 0.2 + (400000 - 200000) * 0.225 + ($tax_pool - 400000) * 0.25;
        } elseif ($tax_pool <= $tax_conf->segment_8) {
            return 70000 * 0.15 + (200000 - 70000) * 0.2 + (400000 - 200000) * 0.225 + ($tax_pool - 400000) * 0.25;
        } elseif ($tax_pool <= $tax_conf->segment_9) {
            return 200000 * 0.2 + (400000 - 200000) * 0.225 + ($tax_pool - 400000) * 0.25;
        } elseif ($tax_pool <= $tax_conf->segment_10) {
            return 400000 * 0.225 + ($tax_pool - 400000) * 0.25;
        } else {
            return 1200000 * 0.25 + ($tax_pool - 1200000) * 0.275;
        }
    }

    private function calculateNetSalary($basic_salary, $social_insurance, $shohdaa, $tax)
    {
        $ins_conf = hr_ins_conf::where('status', true)->first();
        if (!$ins_conf) {
            return 0;
        }

        $emp_share = $ins_conf->emp_share ?? 0;
        $medical_insurance = 0; // You can replace it if you have medical insurance value

        $total_deductions = ($social_insurance * ($emp_share / 100)) + $medical_insurance + $tax + $shohdaa;
        return round($basic_salary - $total_deductions, 2);
    }

    public function calculateSalaryComponents($basic_salary)
    {
        $shohdaa = $this->calculateShohdaa($basic_salary);
        $social_insurance = $this->calculateSocialInsurance($basic_salary);
        $tax = $this->calculateTax($basic_salary, $social_insurance);
        $net_salary = $this->calculateNetSalary($basic_salary, $social_insurance, $shohdaa, $tax);

        return response()->json([
            'shohdaa' => round($shohdaa, 2),
            'social_insurance' => round($social_insurance, 2),
            'tax' => round($tax, 2),
            'net_salary' => round($net_salary, 2),
        ]);
    }
}
