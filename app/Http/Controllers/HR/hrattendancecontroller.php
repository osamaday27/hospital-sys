<?php

namespace App\Http\Controllers\hr;

use App\models\hr\Employee;
use App\models\hr\hr_attendance;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;

class hrattendancecontroller extends Controller
{
    use file_storage;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.attendance.programs.attendance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $employees = Employee::all();
        return view('hr.attendance.programs.attendance.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check employee contract to determine validation rules
        $employee = Employee::with('activeContract')->findOrFail($request->employee_id);
        $contract = $employee->activeContract;
        $salaryLevel = strtolower($contract->salary_level ?? 'monthly');
        
        // Define validation rules based on salary level
        $rules = ['employee_id' => 'required|exists:employees,id'];
        
        if ($salaryLevel === 'hours') {
            // Hourly employees: only worked hours required
            $rules['worked_hours'] = 'required|numeric|min:0|max:24';
            $rules['check_in'] = 'nullable|date';
            $rules['check_out'] = 'nullable|date|after:check_in';
        } else {
            // Monthly/shift employees: check-in/out required
            $rules['check_in'] = 'required|date';
            $rules['check_out'] = 'required|date|after:check_in';
            $rules['worked_hours'] = 'nullable|numeric';
        }
        
        // Validate the request
        $request->validate($rules);

        // Employee and contract already loaded above for validation
        
        if (!$employee->activeContract) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['employee_id' => trans('hr.no_active_contract')]);
        }

        $contract = $employee->activeContract;

        // Validate contract is not expired
        $currentDate = now();
        if ($contract->end_date && $currentDate->gt($contract->end_date)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['employee_id' => trans('hr.contract_expired', ['end_date' => $contract->end_date->format('Y-m-d')])]);
        }
    
        // Check for overlapping attendance
        $overlappingAttendance = hr_attendance::where('employee_id', $request->input('employee_id'))
            ->whereDate('check_in', '=', date('Y-m-d', strtotime($request->input('check_in')))) // Same day
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->input('check_in'), $request->input('check_out')])
                      ->orWhereBetween('check_out', [$request->input('check_in'), $request->input('check_out')])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('check_in', '<=', $request->input('check_in'))
                            ->where('check_out', '>=', $request->input('check_out'));
                      });
            })
            ->exists();
    
        if ($overlappingAttendance) {
            return redirect()->back()->withErrors(['attendance' => 'This employee already has overlapping attendance records for the same day.']);
        }
    
        // Calculate worked hours
        $checkIn = Carbon::parse($request->input('check_in'));
        $checkOut = Carbon::parse($request->input('check_out'));
        
        // Ensure check-out is after check-in
        if ($checkOut->lt($checkIn)) {
            return redirect()->back()->withErrors(['check_out' => 'Check-out time must be after check-in time.']);
        }
        
        // Calculate worked hours with a maximum of 24 hours
        $workedHours = min(24, number_format($checkOut->diffInMinutes($checkIn) / 60, 2));

        // Calculate shortage based on employee's schedule
        $shortage = 0;
        $employee = Employee::with('schedule.days')->find($request->input('employee_id'));
        if ($employee && $employee->schedule) {
            $checkInDate = Carbon::parse($request->input('check_in'));
            $dayOfWeek = $checkInDate->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7
            $scheduleDay = $employee->schedule->days->where('day_of_week', $dayOfWeek)->first();
            
            if ($scheduleDay && $scheduleDay->start_time && $scheduleDay->end_time) {
                $scheduleStart = Carbon::parse($scheduleDay->start_time);
                $scheduleEnd = Carbon::parse($scheduleDay->end_time);
                $scheduledHours = $scheduleEnd->diffInMinutes($scheduleStart) / 60;
                $shortage = max(0, $scheduledHours - $workedHours);
            }
        }

        // Calculate penalty based on shortage (only for non-hourly employees)
        $penalty = 0;
        $shouldCalculatePenalty = !($contract->salary_level && strtolower($contract->salary_level) === 'hours');
        
        if ($shouldCalculatePenalty) {
            $shortageMinutes = $shortage * 60; // Convert hours to minutes
            
            if ($shortageMinutes >= 15 && $shortageMinutes < 120) {
                $penalty = 0.25; // 0.25 days
            } elseif ($shortageMinutes >= 120 && $shortageMinutes < 240) {
                $penalty = 0.5; // 0.5 days
            } elseif ($shortageMinutes >= 240) {
                $penalty = 1.0; // 1 day
            }
        }
    
        hr_attendance::create([
            'employee_id' => $request->input('employee_id'),
            'check_in' => $checkIn->format('Y-m-d H:i:s'),
            'check_out' => $checkOut->format('Y-m-d H:i:s'),
            'worked_hours' => $workedHours,
            'shortage' => number_format($shortage, 2),
            'penalty' => $penalty,
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_attendance.index')->with('success', 'Attendance created successfully!');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\models\hr_attendance  $hr_attendance
     * @return \Illuminate\Http\Response
     */
    public function show(hr_attendance $hr_attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\models\hr_attendance  $hr_attendance
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attendance = hr_attendance::findOrFail($id);
        $employees = Employee::all();
        return view('hr.attendance.programs.attendance.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\models\hr_attendance  $hr_attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'employee_id' => 'required|exists:employees,id',
            'penalty' => 'nullable|numeric|min:0|max:10',
        ]);

        // Check if employee has an active contract
        $employee = Employee::with('activeContract')->findOrFail($request->employee_id);
        
        if (!$employee->activeContract) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['employee_id' => trans('hr.no_active_contract')]);
        }

        $contract = $employee->activeContract;

        // Validate contract is not expired
        $currentDate = now();
        if ($contract->end_date && $currentDate->gt($contract->end_date)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['employee_id' => trans('hr.contract_expired', ['end_date' => $contract->end_date->format('Y-m-d')])]);
        }
    
        $attendance = hr_attendance::findOrFail($id);
    
        // Check for overlapping attendance (excluding current record)
        $overlappingAttendance = hr_attendance::where('employee_id', $request->input('employee_id'))
            ->where('id', '!=', $id) // Exclude current record
            ->whereDate('check_in', '=', date('Y-m-d', strtotime($request->input('check_in'))))
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->input('check_in'), $request->input('check_out')])
                      ->orWhereBetween('check_out', [$request->input('check_in'), $request->input('check_out')])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('check_in', '<=', $request->input('check_in'))
                            ->where('check_out', '>=', $request->input('check_out'));
                      });
            })
            ->exists();
    
        if ($overlappingAttendance) {
            return redirect()->back()->withErrors(['attendance' => 'This employee already has overlapping attendance records for the same day.']);
        }
    
        // Calculate worked hours
        $checkIn = Carbon::parse($request->input('check_in'));
        $checkOut = Carbon::parse($request->input('check_out'));
        
        // Ensure check-out is after check-in
        if ($checkOut->lt($checkIn)) {
            return redirect()->back()->withErrors(['check_out' => 'Check-out time must be after check-in time.']);
        }
        
        // Calculate worked hours with a maximum of 24 hours
        $workedHours = min(24, number_format($checkOut->diffInMinutes($checkIn) / 60, 2));

        // Calculate shortage based on employee's schedule
        $shortage = 0;
        $employee = Employee::with('schedule.days')->find($request->input('employee_id'));
        if ($employee && $employee->schedule) {
            $checkInDate = Carbon::parse($request->input('check_in'));
            $dayOfWeek = $checkInDate->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7
            $scheduleDay = $employee->schedule->days->where('day_of_week', $dayOfWeek)->first();
            
            if ($scheduleDay && $scheduleDay->start_time && $scheduleDay->end_time) {
                $scheduleStart = Carbon::parse($scheduleDay->start_time);
                $scheduleEnd = Carbon::parse($scheduleDay->end_time);
                $scheduledHours = $scheduleEnd->diffInMinutes($scheduleStart) / 60;
                $shortage = max(0, $scheduledHours - $workedHours);
            }
        }

        // Calculate penalty based on shortage (only for non-hourly employees)
        $penalty = 0;
        $shouldCalculatePenalty = !($contract->salary_level && strtolower($contract->salary_level) === 'hours');
        
        if ($shouldCalculatePenalty) {
            $shortageMinutes = $shortage * 60; // Convert hours to minutes
            
            if ($shortageMinutes >= 15 && $shortageMinutes < 120) {
                $penalty = 0.25; // 0.25 days
            } elseif ($shortageMinutes >= 120 && $shortageMinutes < 240) {
                $penalty = 0.5; // 0.5 days
            } elseif ($shortageMinutes >= 240) {
                $penalty = 1.0; // 1 day
            }
        }
    
        $attendance->update([
            'employee_id' => $request->input('employee_id'),
            'check_in' => $request->input('check_in'),
            'check_out' => $request->input('check_out'),
            'worked_hours' => $workedHours,
            'shortage' => number_format($shortage, 2),
            'penalty' => $shouldCalculatePenalty ? $request->input('penalty', $penalty) : 0,
        ]);
    
        return redirect()->route('hr_attendance.index')->with('success', 'Attendance updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\hr_attendance  $hr_attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $attendance = hr_attendance::findOrFail($id);
        $attendance->delete();

        // Return JSON response for AJAX requests
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully!'
            ]);
        }

        return redirect()->route('hr_attendance.index')->with('success', 'Attendance deleted successfully!');
    }




    public function get_attendance()
    {
        $attendance = hr_attendance::with(['employee.schedule', 'employee.emp_sec'])
            ->orderBy('check_in', 'desc'); // Sort by check_in date descending (newest first)

        return DataTables::of($attendance)

            ->addColumn('employee', function ($attendance) {
                return $attendance->employee->full_name;
            })

            ->addColumn('employee_code', function ($attendance) {
                return $attendance->employee->employee_code ?? '-';
            })

            ->addColumn('schedule_id', function ($attendance) {
                return $attendance->employee->schedule ? $attendance->employee->schedule->name : '-';
            })

            ->addColumn('section', function ($attendance) {
                return $attendance->employee->emp_sec ? $attendance->employee->emp_sec->name_ar : '-';
            })

            ->addColumn('schedule_start', function ($attendance) {
                if ($attendance->employee->schedule) {
                    $checkInDate = Carbon::parse($attendance->check_in);
                    $dayOfWeek = $checkInDate->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7
                    $scheduleDay = $attendance->employee->schedule->days->where('day_of_week', $dayOfWeek)->first();
                    return $scheduleDay ? $scheduleDay->start_time : '-';
                }
                return '-';
            })

            ->addColumn('schedule_end', function ($attendance) {
                if ($attendance->employee->schedule) {
                    $checkInDate = Carbon::parse($attendance->check_in);
                    $dayOfWeek = $checkInDate->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7
                    $scheduleDay = $attendance->employee->schedule->days->where('day_of_week', $dayOfWeek)->first();
                    return $scheduleDay ? $scheduleDay->end_time : '-';
                }
                return '-';
            })

            ->addColumn('shortage', function ($attendance) {
                if ($attendance->shortage > 0) {
                    return '<span class="badge bg-warning">' . number_format($attendance->shortage, 2) . ' hrs</span>';
                }
                return '<span class="badge bg-success">0 hrs</span>';
            })

            ->addColumn('penalty', function ($attendance) {
                $penaltyClass = 'bg-success';
                if ($attendance->penalty > 0) {
                    $penaltyClass = 'bg-danger';
                }
                return '<span class="badge ' . $penaltyClass . ' penalty-badge" data-id="' . $attendance->id . '" contenteditable="true">' . $attendance->penalty . ' days</span>';
            })

            ->editColumn('created_at', function ($attendance) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($attendance->created_at)->format('d/m/Y h:i A');
            })

            ->addColumn('action', function ($attendance) {
                $encryptedId = Crypt::encryptString($attendance->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $attendance->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('hr_attendance.edit', ['id' => $attendance->id]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['employee', 'employee_code', 'schedule_id', 'section', 'schedule_start', 'schedule_end', 'shortage', 'penalty', 'action'])
            ->make(true);
    }

    public function updatePenalty(Request $request, $id)
    {
        $request->validate([
            'penalty' => 'required|numeric|min:0|max:10',
        ]);

        $attendance = hr_attendance::findOrFail($id);
        $attendance->update(['penalty' => $request->penalty]);

        return response()->json([
            'success' => true,
            'message' => 'Penalty updated successfully',
            'penalty' => $attendance->penalty
        ]);
    }

    /**
     * Upload attendance data from Excel file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new AttendanceImport();
            Excel::import($import, $request->file('excel_file'));
            
            $successCount = $import->getSuccessCount();
            $failureCount = $import->getFailureCount();
            $errors = $import->getErrors();
            
            $message = "Upload completed! Successfully imported: {$successCount} records";
            
            if ($failureCount > 0) {
                $message .= ", Failed: {$failureCount} records";
            }

            // Store errors in session for display
            if (!empty($errors)) {
                session()->flash('upload_errors', $errors);
            }

            if ($successCount > 0) {
                return redirect()->route('hr_attendance.index')
                    ->with('success', $message);
            } else {
                return redirect()->route('hr_attendance.index')
                    ->with('error', 'No records were imported. Please check the file format and data.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('hr_attendance.index')
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Test datetime parsing for debugging
     */
    public function testDateTimeParsing()
    {
        $import = new \App\Imports\AttendanceImport();
        
        $testValues = [
            '15/09/2025  4:00:00 PM',  // Your exact format with double space
            '15/09/2025 4:00:00 PM',   // Single space version
            '21/08/2025 1:12:00 PM',
            '21/08/2025 13:12:00',
            '2025-08-21 13:12:00',
            '08/21/2025 1:12:00 PM',
        ];

        $results = [];
        foreach ($testValues as $testValue) {
            $reflection = new \ReflectionClass($import);
            $method = $reflection->getMethod('parseDateTime');
            $method->setAccessible(true);
            $result = $method->invoke($import, $testValue);
            
            $results[] = [
                'input' => $testValue,
                'output' => $result ? $result->format('Y-m-d H:i:s') : 'NULL',
                'success' => $result !== null
            ];
        }

        return response()->json($results);
    }

    /**
     * Test creating a single attendance record to debug the saving process
     */
    public function testCreateAttendance()
    {
        try {
            // Create a test attendance record
            $checkIn = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s A', '15/09/2025 4:00:00 PM');
            $checkOut = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s A', '15/09/2025 8:00:00 PM');
            
            \Log::info("Test attendance creation", [
                'check_in_parsed' => $checkIn->format('Y-m-d H:i:s'),
                'check_out_parsed' => $checkOut->format('Y-m-d H:i:s'),
            ]);

            // Get first employee for testing
            $employee = \App\models\hr\Employee::first();
            if (!$employee) {
                return response()->json(['error' => 'No employees found']);
            }

            $attendance = \App\models\hr\hr_attendance::create([
                'employee_id' => $employee->id,
                'check_in' => $checkIn->format('Y-m-d H:i:s'),
                'check_out' => $checkOut->format('Y-m-d H:i:s'),
                'worked_hours' => 4.0,
                'shortage' => 0,
                'penalty' => 0,
            ]);

            // Retrieve the saved record to see what was actually stored
            $saved = \App\models\hr\hr_attendance::find($attendance->id);

            return response()->json([
                'created' => [
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'check_in_type' => gettype($attendance->check_in),
                    'check_out_type' => gettype($attendance->check_out),
                ],
                'saved' => [
                    'check_in' => $saved->check_in,
                    'check_out' => $saved->check_out,
                    'check_in_type' => gettype($saved->check_in),
                    'check_out_type' => gettype($saved->check_out),
                ],
                'raw_database' => \DB::table('hr_attendance')->where('id', $attendance->id)->first()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}
