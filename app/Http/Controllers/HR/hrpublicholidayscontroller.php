<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_public_holidays;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class hrpublicholidayscontroller extends Controller
{
    public function index()
    {
        return view('hr.settings.public_holidays.index');
    }

    public function create()
    {
        return view('hr.settings.public_holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:hr_public_holidays,name',
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        hr_public_holidays::create([
            'name' => $request->input('name'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'description' => $request->has('description'), 
        ]);
        return redirect()->route('hr_public_holidays.index')->with('success', 'Public Holiday created successfully!');

    }

    public function edit($id)
    {
        $public_holiday = hr_public_holidays::find($id);
        return view('hr.settings.public_holidays.edit', compact('public_holiday'));
    }

    public function update(Request $request, $id)
    {
        $public_holiday = hr_public_holidays::find($id);
        $request->validate([
            'name' => 'required|unique:hr_public_holidays,name,' . $id,
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);
    
    
        $public_holiday->update([
            'name' => $request->input('name'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'description' => $request->has('description'), 
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_public_holidays.index')->with('success', 'Public Holiday updated successfully!');
    }

    public function destroy($id)
    {   
        $public_holiday = hr_public_holidays::findOrFail($id);
        $public_holiday->delete();

        return redirect()->route('hr_public_holidays.index')->with('success', 'Public Holiday deleted successfully!');
    }


    public function get_public_holidays()
    {
        $public_holidays = hr_public_holidays::all();

        return DataTables::of($public_holidays)


            ->editColumn('created_at', function ($public_holiday) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($public_holiday->created_at)->format('d/m/Y h:i A');
            })

            ->addColumn('action', function ($public_holiday) {
                $encryptedId = Crypt::encryptString($public_holiday->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $public_holiday->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('hr_vacation_types.edit', ['id' => $public_holiday->id]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}