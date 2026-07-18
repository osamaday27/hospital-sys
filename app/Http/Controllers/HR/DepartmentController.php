<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\hr\Department; 
use App\models\hr\Employee; 
use App\Traits\web\file_storage;


class DepartmentController extends Controller
{
    use file_storage;

    public function index()
    {
        $hr_department = Department::with('parent')->get();
        $employees = Department::with('manager')->get();
        return view('hr.settings.departments.index', compact('hr_department', 'employees'));
    }

    public function create()
    {
        $hr_department = Department::all();
        $employees = Employee::all();
        return view('hr.settings.departments.create', compact('hr_department', 'employees'));
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name_ne' => 'required|string|max:255|unique:hr_department,name_ne',
            'name_ar' => 'required|string|max:255|unique:hr_department,name_ar',
            // Additional validation for file uploads can be added if necessary
        ]);
    

        Department::create([
            'name_ne' => $request->input('name_ne'),
            'name_ar' => $request->input('name_ar'),
            'parent_department' => $request->input('parent_department'),
            'manager_id' => $request->input('manager_id'),
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_department.index')->with('success', 'Department created successfully!');
    }

    public function edit($id)
    {
        $department_edit = Department::findOrFail($id);
        $hr_department = Department::all();
        $employees = Employee::all();
        return view('hr.settings.departments.edit', compact('department_edit', 'hr_department', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
    
        // Validate required fields
        $request->validate([
            'name_ne' => 'required|string|max:255|unique:hr_department,name_ne,'  . $id,
            'name_ar' => 'required|string|max:255|unique:hr_department,name_ar,'  . $id,
        ]);
    
    
        // Update employee record
        $department->update([
            'name_ne' => $request->input('name_ne'),
            'name_ar' => $request->input('name_ar'),
            'parent_department' => $request->input('parent_department'),
            'manager_id' => $request->input('manager_id'),
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_department.index')->with('success', 'Department updated successfully!');
    }


    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $departments = Department::findOrFail($id);
        $departments->delete();

        return redirect()->route('hr_department.index')->with('success', 'Department deleted successfully!');
    }

}
