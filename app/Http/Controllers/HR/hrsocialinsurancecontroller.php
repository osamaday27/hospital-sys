<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\HR\hr_social_insurance;
use App\Models\HR\Employee;
use App\Traits\web\file_storage;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class hrsocialinsurancecontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('hr.payroll.settings.social_insurance.index');
    }

    public function create()
    {
        $employees = Employee::with(['emp_dep', 'emp_pos'])->get();
        return view('hr.payroll.settings.social_insurance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'insurance_status' => 'required|boolean',
            'subscription_date' => 'required|date',
            'insurance_office' => 'required|string|max:255',
            'insurance_number' => 'required|string|max:255|unique:hr_social_insurance,insurance_number',
            'health_status' => 'required|boolean',
            'insurance_company' => 'required|string|max:255',
            'main_salary' => 'required|numeric|min:0',
            'shared_salary' => 'required|numeric|min:0',
        ]);

        hr_social_insurance::create($request->all());

        return redirect()->route('hr_social_insurance.index')
            ->with('success', 'Social insurance record created successfully!');
    }

    public function edit($id)
    {
        $insurance = hr_social_insurance::with(['employee.emp_dep', 'employee.emp_pos'])->findOrFail($id);
        return view('hr.payroll.settings.social_insurance.edit', compact('insurance'));
    }

    public function update(Request $request, $id)
    {
        $insurance = hr_social_insurance::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'insurance_status' => 'required|boolean',
            'subscription_date' => 'required|date',
            'insurance_office' => 'required|string|max:255',
            'insurance_number' => 'required|string|max:255|unique:hr_social_insurance,insurance_number,' . $id,
            'health_status' => 'required|boolean',
            'insurance_company' => 'required|string|max:255',
            'main_salary' => 'required|numeric|min:0',
            'shared_salary' => 'required|numeric|min:0',
        ]);

        $insurance->update($request->all());

        return redirect()->route('hr_social_insurance.index')
            ->with('success', 'Social insurance record updated successfully!');
    }

    public function get_social_insurance()
    {
        $insurance = hr_social_insurance::with(['employee.emp_dep', 'employee.emp_pos']);

        return DataTables::of($insurance)
            ->addColumn('employee_code', function ($insurance) {
                return $insurance->employee->employee_code ?? 'N/A';
            })
            ->addColumn('job_position', function ($insurance) {
                return $insurance->employee->emp_pos->name_ar ?? 'N/A';
            })
            ->addColumn('department', function ($insurance) {
                return $insurance->employee->emp_dep->name_ar ?? 'N/A';
            })
            ->addColumn('hiring_date', function ($insurance) {
                return $insurance->employee->hiring_date ? Carbon::parse($insurance->employee->hiring_date)->format('d/m/Y') : 'N/A';
            })
            ->addColumn('has_valid_contract', function ($insurance) {
                return $insurance->employee->has_valid_contract ? 'Yes' : 'No';
            })
            ->addColumn('marital_status', function ($insurance) {
                return $insurance->employee->marital_status ?? 'N/A';
            })
            ->addColumn('gender', function ($insurance) {
                return $insurance->employee->gender ?? 'N/A';
            })
            ->addColumn('phone', function ($insurance) {
                return $insurance->employee->phone ?? 'N/A';
            })
            ->addColumn('full_address', function ($insurance) {
                return $insurance->employee->full_address ?? 'N/A';
            })
            ->addColumn('identification_number', function ($insurance) {
                return $insurance->employee->identification_number ?? 'N/A';
            })
            ->addColumn('insurance_status', function ($insurance) {
                return $insurance->insurance_status ? 
                    '<span class="badge bg-success">' . trans('status.active') . '</span>' : 
                    '<span class="badge bg-danger">' . trans('status.inactive') . '</span>';
            })
            ->addColumn('health_status', function ($insurance) {
                return $insurance->health_status ? 
                    '<span class="badge bg-success">' . trans('status.healthy') . '</span>' : 
                    '<span class="badge bg-danger">' . trans('status.unhealthy') . '</span>';
            })
            ->editColumn('subscription_date', function ($insurance) {
                return Carbon::parse($insurance->subscription_date)->format('d/m/Y');
            })
            ->editColumn('created_at', function ($insurance) {
                return Carbon::parse($insurance->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($insurance) {
                $encryptedId = Crypt::encryptString($insurance->id);
                $deleteFunction = 'deleteFunction(' . $insurance->id . ')';

                return '
                    <a href="' . route('hr_social_insurance.edit', ['id' => $insurance->id]) . '" 
                        class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="' . $deleteFunction . '" 
                        class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['insurance_status', 'health_status', 'action'])
            ->make(true);
    }
} 