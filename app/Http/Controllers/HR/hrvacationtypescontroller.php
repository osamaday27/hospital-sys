<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_vacation_types;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class hrvacationtypescontroller extends Controller
{
    public function index()
    {
        return view('hr.settings.vacation_types.index');
    }

    public function create()
    {
        return view('hr.settings.vacation_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'take_vacation_in' => 'required',
            'approval_type' => 'required'
        ]);

        hr_vacation_types::create([
            'name' => $request->input('name'),
            'take_vacation_in' => $request->input('take_vacation_in'),
            'approval_type' => $request->input('approval_type'),
            'has_allocation' => $request->has('has_allocation') ? true : false, // Ensure false when unchecked
            'no_salary' => $request->has('no_salary') ? true : false,
        ]);
        return redirect()->route('hr_vacation_types.index')->with('success', 'Vacation type created successfully!');

    }

    public function edit($type)
    {
        $vacation_type = hr_vacation_types::find($type);
        return view('hr.settings.vacation_types.edit', compact('vacation_type'));
    }

    public function update(Request $request, $id)
    {
        $vacation_type = hr_vacation_types::find($id);
        $request->validate([
            'name' => 'required',
            'take_vacation_in' => 'required',
            'approval_type' => 'required'
        ]);
    
    
        // Create the contract if validation passes
        $vacation_type->update([
            'name' => $request->input('name'),
            'take_vacation_in' => $request->input('take_vacation_in'),
            'approval_type' => $request->input('approval_type'),
            'has_allocation' => $request->has('has_allocation') ? true : false, // Ensure false when unchecked
            'no_salary' => $request->has('no_salary') ? true : false,
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_vacation_types.index')->with('success', 'Vacation type updated successfully!');
    }

    public function destroy($id)
    {   
        $vacation_type = hr_vacation_types::findOrFail($id);
        $vacation_type->delete();

        return redirect()->route('hr_vacation_types.index')->with('success', 'Vacation type deleted successfully!');
    }


    public function get_vacation_types()
    {
        $vacation_types = hr_vacation_types::all();

        return DataTables::of($vacation_types)

            ->addColumn('take_vacation_in', function ($vacation_types) {
                // تحديد الحالة باللغة العربية
                $take_vacation_in = $vacation_types->take_vacation_in;
                switch ($take_vacation_in) {
                    case 'hours':
                        return '<span class="badge bg-success">' . trans('hr.hours') . '</span>';
                    case 'days':
                        return '<span class="badge bg-warning">' . trans('hr.days') . '</span>';
                    case 'half_day':
                        return '<span class="badge bg-secondary">' . trans('hr.half_day') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })

            ->addColumn('approval_type', function ($vacation_types) {
                // تحديد الحالة باللغة العربية
                $approval_type = $vacation_types->approval_type;
                switch ($approval_type) {
                    case 'no_validation':
                        return '<span class="badge bg-success">' . trans('hr.no_validation') . '</span>';
                    case 'manager':
                        return '<span class="badge bg-warning">' . trans('hr.manager') . '</span>';
                    case 'hr_manager':
                        return '<span class="badge bg-secondary">' . trans('hr.hr_and_manager') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })


            ->editColumn('created_at', function ($vacation_types) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($vacation_types->created_at)->format('d/m/Y h:i A');
            })

            ->addColumn('action', function ($vacation_type) {
                $encryptedId = Crypt::encryptString($vacation_type->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $vacation_type->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('hr_vacation_types.edit', ['id' => $vacation_type->id]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['take_vacation_in', 'approval_type', 'action'])
            ->make(true);
    }

}