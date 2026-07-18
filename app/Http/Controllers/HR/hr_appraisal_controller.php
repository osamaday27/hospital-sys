<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\hr\hr_appraisal;
use App\models\hr\hr_appraisal_conf;
use App\models\hr\hr_appraisal_terms_conf;
use App\models\hr\hr_appraisal_details;
use App\models\hr\Employee;
use App\models\hr\jobs;
use App\models\hr\Department;
use Yajra\DataTables\Facades\DataTables;

class hr_appraisal_controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hr.appraisal.programs.appraisal_form.index');
    }

    /**
     * Get appraisal data for DataTables
     */
    public function getData()
    {
        try {
            $appraisals = hr_appraisal::with(['employee', 'job', 'department']);

            return DataTables::of($appraisals)
            ->addColumn('employee_name', function ($appraisal) {
                return $appraisal->employee->full_name ?? '';
            })
            ->addColumn('job_name', function ($appraisal) {
                return $appraisal->job->name_en ?? '';
            })
            ->addColumn('department_name', function ($appraisal) {
                return $appraisal->department->name_ne ?? '';
            })
            ->addColumn('total_achieved', function ($appraisal) {
                return $appraisal->total_achieved_percentage . '%';
            })
            ->addColumn('action', function ($appraisal) {
                $actions = '<div class="d-flex gap-2">';
                $actions .= '<a href="' . route('hr_appraisal.show', $appraisal->id) . '" class="btn btn-sm btn-info"><i class="ri-eye-line"></i></a>';
                $actions .= '<a href="' . route('hr_appraisal.edit', $appraisal->id) . '" class="btn btn-sm btn-warning"><i class="ri-edit-line"></i></a>';
                $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteFunction(' . $appraisal->id . ')"><i class="ri-delete-bin-line"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
        } catch (\Exception $e) {
            \Log::error('Appraisal DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while loading the data'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::select('id', 'full_name', 'employee_code')->get();
        return view('hr.appraisal.programs.appraisal_form.create', compact('employees'));
    }

    /**
     * Get employee data for AJAX request
     */
    public function getEmployeeData(Request $request)
    {
        $employee = Employee::with(['emp_pos', 'emp_dep'])->find($request->employee_id);
        
        if (!$employee) {
            return response()->json(['error' => __('hr.employee_not_found')]);
        }

        // Check if appraisal configuration exists for this job
        $appraisalConf = hr_appraisal_conf::where('job_id', $employee->job_position)->first();
        
        if (!$appraisalConf) {
            return response()->json(['error' => __('hr.no_appraisal_config_found')]);
        }

        // Get terms for this configuration
        $terms = $appraisalConf->terms()->get()->groupBy('category');

        return response()->json([
            'employee_code' => $employee->employee_code,
            'job_id' => $employee->job_position,
            'job_name' => $employee->emp_pos->name_en ?? '',
            'department' => $employee->department,
            'department_name' => $employee->emp_dep->name_ne ?? '',
            'hiring_date' => $employee->hiring_date,
            'terms' => $terms
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'appraisal_interval' => 'required|string',
            'terms' => 'required|array',
            'terms.*.term_id' => 'required|exists:hr_appraisal_terms_conf,id',
            'terms.*.achieved_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Get employee data
        $employee = Employee::find($request->employee_id);
        
        // Create appraisal
        $appraisal = hr_appraisal::create([
            'employee_id' => $request->employee_id,
            'employee_code' => $employee->employee_code,
            'job_id' => $employee->job_position,
            'department' => $employee->department,
            'hiring_date' => $employee->hiring_date,
            'appraisal_interval' => $request->appraisal_interval,
        ]);

        // Create appraisal details
        foreach ($request->terms as $termData) {
            $term = hr_appraisal_terms_conf::find($termData['term_id']);
            $difference = $termData['achieved_percentage'] - $term->percentage;
            
            hr_appraisal_details::create([
                'appraisal_id' => $appraisal->id,
                'term_id' => $termData['term_id'],
                'achieved_percentage' => $termData['achieved_percentage'],
                'difference' => $difference,
            ]);
        }

        return redirect()->route('hr_appraisal.index')
            ->with('success', __('hr.appraisal_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $appraisal = hr_appraisal::with(['employee', 'job', 'department', 'details.term'])->findOrFail($id);
        return view('hr.appraisal.programs.appraisal_form.show', compact('appraisal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $appraisal = hr_appraisal::with(['details.term'])->findOrFail($id);
        $employees = Employee::select('id', 'full_name', 'employee_code')->get();
        
        // Get terms grouped by category
        $terms = $appraisal->details->groupBy('term.category');
        
        return view('hr.appraisal.programs.appraisal_form.edit', compact('appraisal', 'employees', 'terms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'appraisal_interval' => 'required|string',
            'terms' => 'required|array',
            'terms.*.term_id' => 'required|exists:hr_appraisal_terms_conf,id',
            'terms.*.achieved_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $appraisal = hr_appraisal::findOrFail($id);
        
        // Update appraisal
        $appraisal->update([
            'appraisal_interval' => $request->appraisal_interval,
        ]);

        // Delete existing details
        $appraisal->details()->delete();

        // Create new appraisal details
        foreach ($request->terms as $termData) {
            $term = hr_appraisal_terms_conf::find($termData['term_id']);
            $difference = $termData['achieved_percentage'] - $term->percentage;
            
            hr_appraisal_details::create([
                'appraisal_id' => $appraisal->id,
                'term_id' => $termData['term_id'],
                'achieved_percentage' => $termData['achieved_percentage'],
                'difference' => $difference,
            ]);
        }

        return redirect()->route('hr_appraisal.index')
            ->with('success', __('hr.appraisal_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $appraisal = hr_appraisal::findOrFail($id);
            $appraisal->delete();

            return response()->json([
                'success' => true,
                'message' => __('hr.appraisal_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('Appraisal Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting appraisal: ' . $e->getMessage()
            ], 500);
        }
    }
} 