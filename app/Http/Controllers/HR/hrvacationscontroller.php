<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_vacations;
use App\models\hr\Employee;
use App\models\hr\hr_vacation_types;
use App\services\vacationsbalanceservice;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 


class hrvacationscontroller extends Controller
{
    public function index()
    {
        return view('hr.vacations.programs.vacations.index');
    }

    public function create()
    {
        $employees = Employee::all();
        $vacation_types = hr_vacation_types::all();
        return view('hr.vacations.programs.vacations.create', compact('employees', 'vacation_types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:hr_vacation_types,id',
            'take_with' => 'required|in:days,hours',
            'reason' => 'nullable|string',
        ]);
    
        // Add conditional validation based on take_with
        if ($request->take_with === 'days') {
            $validator->addRules([
                'request_type' => 'required|in:full_day,half_day',
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
            ]);
        } 
        else if ($request->take_with === 'hours') {
            $validator->addRules([
                'hours_date' => 'required|date',
                'from_time' => 'required|date_format:H:i',
                'to_time' => 'required|date_format:H:i|after:from_time',
            ]);
        }
    
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $vacationType = hr_vacation_types::findOrFail($request->input('vacation_type_id'));
    
        if ($request->input('take_with') !== $vacationType->take_vacation_in) {
            return redirect()->back()->withErrors(['take_with' => 'Invalid vacation type selection.'])->withInput();
        }
    
        $daysTaken = 0;
        $from = null;
        $to = null;
    
        switch ($request->input('take_with')) {
            case 'days':
                $from = $request->input('from');
                $to = $request->input('to');
                $daysTaken = Carbon::parse($to)->diffInDays(Carbon::parse($from)) + 1;
                
                // If it's a half day request, adjust the days taken
                if ($request->input('request_type') === 'half_day') {
                    $daysTaken = $daysTaken * 0.5;
                }
                break;
    
            case 'hours':
                $from = $request->input('hours_date') . ' ' . $request->input('from_time');
                $to = $request->input('hours_date') . ' ' . $request->input('to_time');
                $hoursTaken = Carbon::parse($to)->diffInHours(Carbon::parse($from));
                $daysTaken = $hoursTaken; // Store actual hours instead of converting to days
                break;
        }
    
        $vacation = hr_vacations::create([
            'employee_id' => $request->input('employee_id'),
            'vacation_type_id' => $request->input('vacation_type_id'),
            'take_with' => $request->input('take_with'),
            'request_type' => $request->input('request_type'),
            'from' => $from,
            'to' => $to,
            'date' => $request->input('take_with') !== 'days' ? $request->input('date') : null,
            'from_time' => $request->input('take_with') === 'hours' ? $request->input('from_time') : null,
            'to_time' => $request->input('take_with') === 'hours' ? $request->input('to_time') : null,
            'days_taken' => $daysTaken,
            'reason' => $request->input('reason'),
            'approval_status' => 'new',
        ]);
    
        if ($vacation->vacation_type->has_allocation) {
            $balanceService = new vacationsbalanceservice();
            $balanceService->updateEmployeeBalance($vacation);
        }
    
        return redirect()->route('hr_vacations.index')->with('success', 'Vacation created successfully.');
    }

    public function edit($id)
    {
        $vacation = hr_vacations::findOrFail($id);
    
        $employees = Employee::all(); 
        $vacationTypes = hr_vacation_types::all(); 
    
        // Pass data to the edit view
        return view('hr.vacations.programs.vacations.edit', compact('vacation', 'employees', 'vacationTypes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:hr_vacation_types,id',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'reason' => 'nullable|string',
        ]);
    
        // Find the vacation record by ID
        $vacation = hr_vacations::findOrFail($id);
    
        // Update the vacation record
        $vacation->update([
            'employee_id' => $request->input('employee_id'),
            'vacation_type_id' => $request->input('vacation_type_id'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'reason' => $request->input('reason'),
        ]);
    
        return redirect()->route('hr_vacations.index')->with('success', 'Vacation updated successfully.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
    
        try {
            $vacation = hr_vacations::findOrFail($id);
            $daysTaken = $vacation->days_taken;
            
            // Restore balance before deleting if vacation type has allocation
            if ($vacation->vacationType->has_allocation) {
                $balanceService = new vacationsbalanceservice();
                
                // Get current allocation
                $allocation = DB::table('hr_allocations')
                    ->join('employee_hr_allocation', 'hr_allocations.id', '=', 'employee_hr_allocation.hr_allocation_id')
                    ->where('employee_hr_allocation.employee_id', $vacation->employee_id)
                    ->where('hr_allocations.vacation_type', $vacation->vacation_type_id)
                    ->first();
                
                if ($allocation) {
                    // Calculate new balance (current + days taken)
                    $newBalance = $allocation->remaining_balance + $daysTaken;
                    
                    // Update the balance
                    DB::table('employee_hr_allocation')
                        ->where('employee_id', $vacation->employee_id)
                        ->where('hr_allocation_id', $allocation->id)
                        ->update(['remaining_balance' => $newBalance]);
                }
            }
    
            $vacation->delete();
    
            DB::commit();
    
            return redirect()->route('hr_vacations.index')
                ->with('success', 'Vacation deleted and balance restored successfully!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete vacation: ' . $e->getMessage());
        }
    }

    public function getRemainingBalance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:hr_vacation_types,id',
        ]);
    
        $employeeId = $request->input('employee_id');
        $vacationTypeId = $request->input('vacation_type_id');
    
        try {
            // Get allocation
            $allocation = DB::table('hr_allocations')
                ->join('employee_hr_allocation', 'hr_allocations.id', '=', 'employee_hr_allocation.hr_allocation_id')
                ->where('employee_hr_allocation.employee_id', $employeeId)
                ->where('hr_allocations.vacation_type', $vacationTypeId)
                ->first();
    
            if (!$allocation) {
                return response()->json(['remaining_balance' => 0]);
            }
    
            // Calculate used days
            $usedDays = hr_vacations::where('employee_id', $employeeId)
                ->where('vacation_type_id', $vacationTypeId)
                ->sum('days_taken');
    
            // Calculate remaining balance
            $remainingBalance = max(0, $allocation->amount - $usedDays);
    
            return response()->json([
                'remaining_balance' => $remainingBalance,
                'allocation' => $allocation->amount,
                'used_days' => $usedDays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error calculating balance',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function get_vacations()
    {
        $vacations = hr_vacations::with('employee', 'vacation_type');

        return DataTables::of($vacations)
            ->addColumn('employee', function ($vacation) {
                return $vacation->employee->full_name;
            })
            ->addColumn('employee_code', function ($vacation) {
                return $vacation->employee->employee_code;
            })
            ->addColumn('job_position', function ($vacation) {
                return $vacation->employee->emp_pos->name_ar ?? '';
            })
            ->addColumn('section', function ($vacation) {
                return $vacation->employee->emp_sec->name_ar ?? '';
            })
            ->addColumn('vacation_type', function ($vacation) {
                return $vacation->vacation_type->name;
            })
            ->addColumn('requested_amount', function ($vacation) {
                $amount = $vacation->days_taken;
                $unit = $vacation->take_with === 'days' ? 'days' : 'hours';
                
                if ($vacation->take_with === 'days') {
                    if ($vacation->request_type === 'half_day') {
                        return number_format($amount, 1) . ' ' . $unit;
                    }
                    return number_format($amount, 0) . ' ' . $unit;
                } else {
                    // For hours, show the actual hours taken
                    return number_format($amount, 0) . ' ' . $unit;
                }
            })
            ->addColumn('from', function ($vacation) {
                if ($vacation->take_with === 'days') {
                    return Carbon::parse($vacation->from)->format('Y-m-d');
                }
                return Carbon::parse($vacation->from)->format('Y-m-d H:i');
            })
            ->addColumn('to', function ($vacation) {
                if ($vacation->take_with === 'days') {
                    return Carbon::parse($vacation->to)->format('Y-m-d');
                }
                return Carbon::parse($vacation->to)->format('Y-m-d H:i');
            })
            ->addColumn('approval_status', function ($vacation) {
                $approval_status = $vacation->approval_status;
                switch ($approval_status) {
                    case 'new':
                        return '<span class="badge bg-secondary">' . trans('hr.new') . '</span>';
                    case 'approved_by_manager':
                        return '<span class="badge bg-warning">' . trans('hr.approved_by_manager') . '</span>';
                    case 'approved_by_hr':
                        return '<span class="badge bg-warning">' . trans('hr.approved_by_hr') . '</span>';
                    case 'approved':
                        return '<span class="badge bg-success">' . trans('hr.approved') . '</span>';
                    case 'refused':
                        return '<span class="badge bg-danger">' . trans('hr.refused') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('created_at', function ($vacation) {
                return Carbon::parse($vacation->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($vacation) {
                $encryptedId = Crypt::encryptString($vacation->id);
                $deleteFunction = 'deleteFunction(' . $vacation->id . ')';

                return '
                <a href="' . route('hr_vacations.edit', ['id' => $vacation->id]) . '" 
                   class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                <a href="javascript:void(0)" onclick="' . $deleteFunction . '" 
                   class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['employee', 'vacation_type', 'approval_status', 'action', 'requested_amount'])
            ->make(true);
    }

}