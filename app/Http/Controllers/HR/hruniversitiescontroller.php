<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\models\HR\hr_universities;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hruniversitiescontroller extends Controller
{
    public function index()
    {
        return view('hr.employees.settings.universities.index');
    }

    public function create()
    {
        return view('hr.employees.settings.universities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        hr_universities::create($request->all());

        return redirect()->route('hr_universities.index')->with('success', 'University created successfully.');
    }

    public function edit($id)
    {
        $university = hr_universities::findOrFail($id);
        return view('hr.employees.settings.universities.edit', compact('university'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $university = hr_universities::findOrFail($id);
        $university->update($request->all());

        return redirect()->route('hr_universities.index')->with('success', 'University updated successfully.');
    }

    public function destroy($id)
    {
        $university = hr_universities::findOrFail($id);
        $university->delete();

        return response()->json(['success' => 'University deleted successfully.']);
    }

    public function get_universities()
    {
        $universities = hr_universities::query();

        return DataTables::of($universities)
            ->addColumn('action', function ($university) {
                return '
                    <a href="' . route('hr_universities.edit', $university->id) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="deleteFunction(' . $university->id . ')" class="btn btn-danger btn-sm">' . trans('buttons.delete') . '</a>';
            })
            ->editColumn('created_at', function ($university) {
                return Carbon::parse($university->created_at)->format('d/m/Y h:i A');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 