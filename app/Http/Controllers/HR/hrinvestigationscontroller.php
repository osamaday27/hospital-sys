<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\hr_investigations;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrinvestigationscontroller extends Controller
{
    public function index()
    {
        return view('hr.employees.programs.investigations.index');
    }

    public function create()
    {
        $employees = Employee::all();
        return view('hr.employees.programs.investigations.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'investigation_address' => 'required|string|max:255',
            'investigation_type' => 'required|string|max:255',
            'investigation_details' => 'required|string',
            'action_taken' => 'required|string'
        ]);

        hr_investigations::create($request->all());

        return redirect()->route('hr_investigations.index')
            ->with('success', 'Investigation record created successfully.');
    }

    public function show($id)
    {
        $investigation = hr_investigations::with('employee')->findOrFail($id);
        return view('hr.employees.programs.investigations.show', compact('investigation'));
    }

    public function edit($id)
    {
        $investigation = hr_investigations::findOrFail($id);
        $employees = Employee::all();
        return view('hr.employees.programs.investigations.edit', compact('investigation', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'investigation_address' => 'required|string|max:255',
            'investigation_type' => 'required|string|max:255',
            'investigation_details' => 'required|string',
            'action_taken' => 'required|string'
        ]);

        $investigation = hr_investigations::findOrFail($id);
        $investigation->update($request->all());

        return redirect()->route('hr_investigations.index')
            ->with('success', 'Investigation record updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $investigation = hr_investigations::findOrFail($id);
            $investigation->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function get_investigations()
    {
        $investigations = hr_investigations::with('employee')->get();

        return DataTables::of($investigations)
            ->editColumn('created_at', function ($investigation) {
                return Carbon::parse($investigation->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('employee_name', function ($investigation) {
                return $investigation->employee->full_name;
            })
            ->addColumn('action', function ($investigation) {
                return '
                    <a href="' . route('hr_investigations.show', $investigation->id) . '" 
                       class="btn btn-info btn-sm">' . trans('buttons.show') . '</a>
                    <a href="' . route('hr_investigations.edit', $investigation->id) . '" 
                       class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="deleteFunction(' . $investigation->id . ')" 
                       class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 