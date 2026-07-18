<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\models\hr\section; 
use Illuminate\Http\Request;
use App\models\hr\Employee; 
use App\models\hr\Department; 
use App\Traits\web\file_storage;


class sectionscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        $sections = section::all();
        $departments = section::with('department_name')->get();
        $employees = section::with('manager')->get();
        return view('hr.settings.sections.index', compact('sections', 'departments', 'employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $employees = Employee::all();
        return view('hr.settings.sections.create', compact('departments', 'employees'));
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
            'name_ne' => 'required|string|max:255|unique:hr_sections,name_ne',
            'name_ar' => 'required|string|max:255|unique:hr_sections,name_ar',
            // Additional validation for file uploads can be added if necessary
        ]);
    

        section::create([
            'name_ne' => $request->input('name_ne'),
            'name_ar' => $request->input('name_ar'),
            'manager_id' => $request->input('manager_id'),
            'department' => $request->input('department'),
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_section.index')->with('success', 'Section created successfully!');
    }


    public function edit($id)
    {
        $section_edit = section::findOrFail($id);
        $departments = Department::all();
        $employees = Employee::all();
        return view('hr.settings.sections.edit', compact('section_edit', 'departments', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $section = section::findOrFail($id);
    
        // Validate required fields
        $request->validate([
            'name_ne' => 'required|string|max:255|unique:hr_sections,name_ne,'  . $id,
            'name_ar' => 'required|string|max:255|unique:hr_sections,name_ar,'  . $id,
        ]);
    
    
        // Update section record
        $section->update([
            'name_ne' => $request->input('name_ne'),
            'name_ar' => $request->input('name_ar'),
            'manager_id' => $request->input('manager_id'),
            'department' => $request->input('department'),
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_section.index')->with('success', 'Section updated successfully!');
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sections = section::findOrFail($id);
        $sections->delete();

        return redirect()->route('hr_section.index')->with('success', 'Section deleted successfully!');
    }

}