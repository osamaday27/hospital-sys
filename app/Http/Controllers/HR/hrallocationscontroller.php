<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_vacation_types;
use App\models\hr\hr_allocations;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;


class hrallocationscontroller extends Controller 
{
    use file_storage;

    public function index()
    {
        return view('hr.settings.allocations.index');
    }

    public function create()
    {
        $vacation_types = hr_vacation_types::where('has_allocation', true)->get();
        $employees = Employee::all();
        return view('hr.settings.allocations.create', compact('vacation_types', 'employees'));
    }

    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'allocation_name' => 'required|unique:hr_allocations,allocation_name',
            'vacation_type' => 'required|exists:hr_vacation_types,id',
            'mode' => 'required|in:by_employee,by_department,by_hospital',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'employees' => 'nullable|array', 
            'employees.*' => 'exists:employees,id', 
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Create hr Allocation
        $allocation = hr_allocations::create([
            'allocation_name' => $request->allocation_name,
            'vacation_type' => $request->vacation_type,
            'mode' => $request->mode,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);
    
        // Attach employees based on mode
        if ($request->mode === 'by_hospital') {
            // Assign allocation to all employees
            $employees = Employee::pluck('id')->toArray();
        } elseif ($request->mode === 'by_employee' && !empty($request->employees)) {
            $employees = $request->employees;
        } else {
            $employees = []; // No employees attached for "by_department" mode
        }
    
        // Attach employees to allocation
        $allocation->employees()->sync($employees);
        
        return redirect()->route('hr_allocations.index')->with('success', 'Allocation created successfully.');
    }


    public function edit($id)
    {
        $allocation = hr_allocations::with('employees')->findOrFail($id);
        $vacation_types = hr_vacation_types::all(); 
        $employees = Employee::all(); 
    
        // Get the IDs of employees already allocated to this allocation
        $allocatedEmployees = $allocation->employees->pluck('id')->toArray();
    
        return view('hr.settings.allocations.edit', compact('allocation', 'vacation_types', 'employees', 'allocatedEmployees'));
    }

    public function destroy($id)
    {   
        $allocation = hr_allocations::findOrFail($id);
        $allocation->delete();

        return redirect()->route('hr_allocations.index')->with('success', 'Allocation deleted successfully!');
    }


    public function update(Request $request, $id)
    {
        // Find the allocation
        $allocation = hr_allocations::findOrFail($id);
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'allocation_name' => 'required|unique:hr_allocations,allocation_name,' . $id,
            'vacation_type' => 'required|exists:hr_vacation_types,id',
            'mode' => 'required|in:by_employee,by_department,by_hospital',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'employees' => 'nullable|array',
            'employees.*' => 'exists:employees,id',
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Update hr Allocation
        $allocation->update([
            'allocation_name' => $request->allocation_name,
            'vacation_type' => $request->vacation_type,
            'mode' => $request->mode,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);
    
        // Attach employees based on mode
        if ($request->mode === 'by_hospital') {
            // Assign allocation to all employees
            $employees = Employee::pluck('id')->toArray();
        } elseif ($request->mode === 'by_employee' && !empty($request->employees)) {
            $employees = $request->employees;
        } else {
            $employees = []; // No employees attached for "by_department" mode
        }
    
        // Sync employees to allocation
        $allocation->employees()->sync($employees);
    
        return redirect()->route('hr_allocations.index')->with('success', 'Allocation updated successfully.');
    }


    public function get_allocations()
    {
        $allocations = hr_allocations::with('vacation_type_name');

        return DataTables::of($allocations)

            ->addColumn('vacation_type', function ($allocation) {
                return $allocation->vacation_type_name ? $allocation->vacation_type_name->name : 'N/A';
            })

            ->editColumn('created_at', function ($allocations) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($allocations->created_at)->format('d/m/Y h:i A');
            })

            ->addColumn('action', function ($allocations) {
                $encryptedId = Crypt::encryptString($allocations->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $allocations->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('hr_allocations.edit', ['id' => $allocations->id]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['vacation_type',  'action'])
            ->make(true);
    }

}