<?php

namespace App\Http\Controllers\hr;

use App\Models\hr\hr_salary_rules;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrsalaryrulescontroller extends Controller
{
    public function index()
    {
        return view('hr.payroll.settings.salary_rules.index');
    }

    public function create()
    {
        return view('hr.payroll.settings.salary_rules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:addition,deduction',
            'rule_type' => 'required|in:absence,lateness,penalties,loans,incentive,overtime,free,manual,basic_salary,allowances,tax,insurance,gross_salary,net_salary',
            'added_automatically' => 'boolean',
            'sequence' => 'required_if:added_automatically,1|integer|min:0',
            'is_active' => 'boolean'
        ]);

        // Validate rule type based on type
        $validRuleTypes = hr_salary_rules::getRuleTypes($request->type);
        if (!in_array($request->rule_type, $validRuleTypes)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['rule_type' => 'Invalid rule type for the selected type.']);
        }

        // Validate unique rule type
        if ($request->rule_type !== 'manual' && !hr_salary_rules::validateUniqueRuleType($request->type, $request->rule_type)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['rule_type' => 'A rule with this type already exists.']);
        }

        // Set sequence to 0 if not added automatically
        if (!$request->added_automatically) {
            $request->merge(['sequence' => 0]);
        }

        hr_salary_rules::create($request->all());

        return redirect()->route('hr_salary_rules.index')
            ->with('success', 'Salary rule created successfully!');
    }

    public function edit($id)
    {
        $salaryRule = hr_salary_rules::findOrFail($id);
        return view('hr.payroll.settings.salary_rules.edit', compact('salaryRule'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:addition,deduction',
            'rule_type' => 'required|in:absence,lateness,penalties,loans,incentive,overtime,free,manual,basic_salary,allowances,tax,insurance,gross_salary,net_salary',
            'added_automatically' => 'boolean',
            'sequence' => 'required_if:added_automatically,1|integer|min:0',
            'is_active' => 'boolean'
        ]);

        // Validate rule type based on type
        $validRuleTypes = hr_salary_rules::getRuleTypes($request->type);
        if (!in_array($request->rule_type, $validRuleTypes)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['rule_type' => 'Invalid rule type for the selected type.']);
        }

        // Validate unique rule type
        if ($request->rule_type !== 'manual' && !hr_salary_rules::validateUniqueRuleType($request->type, $request->rule_type, $id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['rule_type' => 'A rule with this type already exists.']);
        }

        // Set sequence to 0 if not added automatically
        if (!$request->added_automatically) {
            $request->merge(['sequence' => 0]);
        }

        $salaryRule = hr_salary_rules::findOrFail($id);
        $salaryRule->update($request->all());

        return redirect()->route('hr_salary_rules.index')
            ->with('success', 'Salary rule updated successfully!');
    }

    public function destroy($id)
    {
        $salaryRule = hr_salary_rules::findOrFail($id);
        $salaryRule->delete();

        return redirect()->route('hr_salary_rules.index')
            ->with('success', 'Salary rule deleted successfully!');
    }

    public function get_salary_rules()
    {
        $salaryRules = hr_salary_rules::query();

        return DataTables::of($salaryRules)
            ->editColumn('type', function ($salaryRule) {
                return __('hr.' . $salaryRule->type);
            })
            ->editColumn('rule_type', function ($salaryRule) {
                return __('hr.' . $salaryRule->rule_type);
            })
            ->editColumn('added_automatically', function ($salaryRule) {
                return $salaryRule->added_automatically ? __('hr.yes') : __('hr.no');
            })
            ->editColumn('sequence', function ($salaryRule) {
                return $salaryRule->added_automatically ? $salaryRule->sequence : '-';
            })
            ->editColumn('is_active', function ($salaryRule) {
                return $salaryRule->is_active ? __('hr.active') : __('hr.inactive');
            })
            ->editColumn('created_at', function ($salaryRule) {
                return Carbon::parse($salaryRule->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($salaryRule) {
                return '
                    <a href="'.route('hr_salary_rules.edit', $salaryRule->id).'" 
                       class="btn btn-warning btn-sm">'.trans('buttons.edit').'</a>
                    
                    <a href="javascript:void(0)" onclick="deleteFunction('.$salaryRule->id.')" 
                       class="btn btn-danger btn-sm delete-btn">'.trans('buttons.delete').'</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 