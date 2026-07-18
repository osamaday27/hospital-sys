<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_penalties;
use App\models\hr\hr_violations;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrpenaltiescontroller extends Controller
{
    public function index()
    {
        return view('hr.payroll.programs.penalties.index');
    }

    public function create()
    {
        $employees = Employee::all();
        $violations = hr_violations::all();
        return view('hr.payroll.programs.penalties.create', compact('employees', 'violations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'violation_id' => 'required|exists:hr_violations,id',
            'date' => 'required|date',
            'reason' => 'required|string',
        ]);

        hr_penalties::create($request->all());

        return redirect()->route('hr_penalties.index')
            ->with('success', 'Penalty created successfully!');
    }

    public function edit($id)
    {
        $penalty = hr_penalties::findOrFail($id);
        $employees = Employee::all();
        $violations = hr_violations::all();
        return view('hr.payroll.programs.penalties.edit', compact('penalty', 'employees', 'violations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'violation_id' => 'required|exists:hr_violations,id',
            'date' => 'required|date',
            'reason' => 'required|string',
        ]);

        $penalty = hr_penalties::findOrFail($id);
        $penalty->update($request->all());

        return redirect()->route('hr_penalties.index')
            ->with('success', 'Penalty updated successfully!');
    }

    public function destroy($id)
    {
        $penalty = hr_penalties::findOrFail($id);
        $penalty->delete();

        return redirect()->route('hr_penalties.index')
            ->with('success', 'Penalty deleted successfully!');
    }

    public function get_penalties()
    {
        $penalties = hr_penalties::with(['employee', 'violation']);

        return DataTables::of($penalties)
            ->addColumn('employee_name', function ($penalty) {
                return $penalty->employee->full_name ?? 'N/A';
            })
            ->addColumn('violation_name', function ($penalty) {
                return $penalty->violation->violation_name_ar ?? 'N/A';
            })
            ->editColumn('date', function ($penalty) {
                return Carbon::parse($penalty->date)->format('d/m/Y');
            })
            ->editColumn('created_at', function ($penalty) {
                return Carbon::parse($penalty->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($penalty) {
                return '
                    <a href="'.route('hr_penalties.edit', $penalty->id).'" 
                       class="btn btn-warning btn-sm">'.trans('buttons.edit').'</a>
                    
                    <a href="javascript:void(0)" onclick="deleteFunction('.$penalty->id.')" 
                       class="btn btn-danger btn-sm delete-btn">'.trans('buttons.delete').'</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}