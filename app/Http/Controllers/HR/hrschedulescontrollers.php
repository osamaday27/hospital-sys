<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_schedules;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class hrschedulescontrollers extends Controller
{
    use file_storage;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.settings.schedules.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('hr.settings.schedules.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_schedules,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
            'days' => 'required|array|size:7', // Ensure exactly 7 days are provided
            'days.*.day_of_week' => 'required|integer|between:1,7',
            'days.*.start_time' => 'nullable|date_format:H:i', // Optional but must be a valid time format if provided
            'days.*.end_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // Get the index (e.g., "days.1.end_time")
                    $start_time = data_get($request->days, "$index.start_time");
        
                    if ($value && !$start_time) {
                        $fail(trans('validation.required', ['attribute' => trans('hr.start_time')])); // Ensures end_time is not set alone
                    }
        
                    if ($start_time && $value && $value <= $start_time) {
                        $fail(trans('validation.after', ['attribute' => trans('hr.start_time')]));
                    }
                },
            ],
        ]);
    
    
        // Create the contract if validation passes
        $schedule = hr_schedules::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'is_active' => $request->input('is_active'),
        ]);

        // Create the schedule days (7 days)
        foreach ($validated['days'] as $day) {
            $schedule->days()->create([
                'schedule_id' => $schedule->id,
                'day_of_week' => $day['day_of_week'],
                'start_time' => $day['start_time'],
                'end_time' => $day['end_time'],
            ]);
        }

        // Calculate and save total hours
        $schedule->updateTotalHours();
    
        // Redirect with success message
        return redirect()->route('hr_schedules.index')->with('success', 'Schedule created successfully!');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\models\hr_schedules  $hr_schedules
     * @return \Illuminate\Http\Response
     */
    public function show(hr_schedules $hr_schedules)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\models\hr_schedules  $hr_schedules
     * @return \Illuminate\Http\Response
     */
    public function edit($hr_schedules)
    {
        $schedule = hr_schedules::find($hr_schedules);
        return view('hr.settings.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\models\hr_schedules  $hr_schedules
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $schedule = hr_schedules::findOrFail($id);
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_schedules,name,' . $schedule->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
            'days' => 'required|array|size:7', // Ensure exactly 7 days are provided
            'days.*.day_of_week' => 'required|integer|between:1,7',
            'days.*.end_time' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // Get the index (e.g., "days.1.end_time")
                    $start_time = data_get($request->days, "$index.start_time");
        
                    if ($value && !$start_time) {
                        $fail(trans('validation.required', ['attribute' => trans('hr.start_time')])); // Ensures end_time is not set alone
                    }
        
                    if ($start_time && $value && $value <= $start_time) {
                        $fail(trans('validation.after', ['attribute' => trans('hr.start_time')]));
                    }
                },
            ],
        ]);
    
        // Update schedule details
        $schedule->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'is_active' => $request->input('is_active'),
        ]);
    
        // Handle schedule days
        if ($request->has('days')) {
            foreach ($request->input('days') as $day) {
                if (!empty($day['start_time']) && !empty($day['end_time'])) {
                    $schedule->days()->updateOrCreate(
                        ['day_of_week' => $day['day_of_week']],
                        [
                            'start_time' => $day['start_time'],
                            'end_time' => $day['end_time'],
                        ]
                    );
                } else {
                    $schedule->days()->where('day_of_week', $day['day_of_week'])->delete();
                }
            }
        }

        // Calculate and save total hours
        $schedule->updateTotalHours();
    
        return redirect()->route('hr_schedules.index')->with('success', 'Schedule updated successfully!');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\hr_schedules  $hr_schedules
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $schedules = hr_schedules::findOrFail($id);
        $schedules->delete();

        return redirect()->route('hr_schedules.index')->with('success', 'Schedules deleted successfully!');
    }



    public function get_schedules()
{
    $schedules = hr_schedules::all(); // Fetch all schedules

    return DataTables::of($schedules)
        ->addColumn('is_active', function ($schedules) {
            // Determine the status in Arabic
            $is_active = $schedules->is_active;
            switch ($is_active) {
                case true:
                    return '<span class="badge bg-success">' . trans('hr.yes') . '</span>';
                case false:
                    return '<span class="badge bg-warning">' . trans('hr.no') . '</span>';
                default:
                    return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
            }
        })
        ->addColumn('total_hours', function ($schedules) {
            return $schedules->total_hours ? number_format($schedules->total_hours, 2) . ' hrs' : '0 hrs';
        })
        ->editColumn('created_at', function ($schedules) {
            // Format the date (day/month/year hour:minute AM/PM)
            return Carbon::parse($schedules->created_at)->format('d/m/Y h:i A');
        })
        ->addColumn('action', function ($schedules) {
            $encryptedId = Crypt::encrypt($schedules->id); // Encrypt the ID
            $deleteFunction = 'deleteFunction(' . $schedules->id . ')'; // Call the delete function

            // Return the action buttons
            return '
                <a href="' . route('hr_schedules.edit', ['id' => $schedules->id]) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                <a href="javascript:void(0)" onclick="' . $deleteFunction . '" class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>
            ';
        })
        ->rawColumns(['is_active', 'total_hours', 'action']) // Render HTML in columns
        ->make(true);
}

}
