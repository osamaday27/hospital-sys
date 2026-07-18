<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_violations;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrviolationscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.payroll.settings.violations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hr.payroll.settings.violations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'violation_name_en' => 'required|string|max:255|unique:hr_violations,violation_name_en',
            'violation_name_ar' => 'required|string|max:255|unique:hr_violations,violation_name_ar',
        ]);

        hr_violations::create([
            'violation_name_en' => $request->input('violation_name_en'),
            'violation_name_ar' => $request->input('violation_name_ar'),
        ]);

        return redirect()->route('hr_violations.index')->with('success', 'Violation created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\models\hr\Violation  $violation
     * @return \Illuminate\Http\Response
     */
    public function show(hr_violations $violation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\models\hr\Violation  $violation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $violation = hr_violations::findOrFail($id);
        return view('hr.payroll.settings.violations.edit', compact('violation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\models\hr\Violation  $violation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $violation = hr_violations::findOrFail($id);
        
        $request->validate([
            'violation_name_en' => 'required|string|max:255|unique:hr_violations,violation_name_en,'.$violation->id,
            'violation_name_ar' => 'required|string|max:255|unique:hr_violations,violation_name_ar,'.$violation->id,
        ]);

        $violation->update([
            'violation_name_en' => $request->input('violation_name_en'),
            'violation_name_ar' => $request->input('violation_name_ar'),
        ]);

        return redirect()->route('hr_violations.index')->with('success', 'Violation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\hr\Violation  $violation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $violation = hr_violations::findOrFail($id);
        $violation->delete();

        return redirect()->route('hr_violations.index')->with('success', 'Violation deleted successfully!');
    }

    /**
     * Get violations data for DataTables
     *
     * @return \Illuminate\Http\Response
     */
    public function get_violations()
    {
        $violations = hr_violations::query();

        return DataTables::of($violations)
            ->editColumn('created_at', function ($violation) {
                return Carbon::parse($violation->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($violation) {
                $encryptedId = Crypt::encryptString($violation->id);
                $deleteFunction = 'deleteFunction(' . $violation->id . ')';

                return '
                    <a href="' . route('hr_violations.edit', ['id' => $violation->id]) . '" 
                       class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    
                    <a href="javascript:void(0)" onclick="' . $deleteFunction . '" 
                       class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}