<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\models\HR\hr_certificates_types;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrcertificatestypescontroller extends Controller
{
    public function index()
    {
        return view('hr.employees.settings.certificate_types.index');
    }

    public function create()
    {
        return view('hr.employees.settings.certificate_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        hr_certificates_types::create($request->all());

        return redirect()->route('hr_certificates_types.index')->with('success', 'Certificate Type created successfully.');
    }

    public function edit($id)
    {
        $certificate_type = hr_certificates_types::findOrFail($id);
        return view('hr.employees.settings.certificate_types.edit', compact('certificate_type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $certificate_type = hr_certificates_types::findOrFail($id);
        $certificate_type->update($request->all());

        return redirect()->route('hr_certificates_types.index')->with('success', 'Certificate Type updated successfully.');
    }

    public function destroy($id)
    {
        $certificate_type = hr_certificates_types::findOrFail($id);
        $certificate_type->delete();

        return response()->json(['success' => 'Certificate Type deleted successfully.']);
    }

    public function get_certificates_types()
    {
        $certificate_types = hr_certificates_types::query();

        return DataTables::of($certificate_types)
            ->addColumn('action', function ($certificate_type) {
                return '
                    <a href="' . route('hr_certificates_types.edit', $certificate_type->id) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="deleteFunction(' . $certificate_type->id . ')" class="btn btn-danger btn-sm">' . trans('buttons.delete') . '</a>';
            })
            ->editColumn('created_at', function ($certificate_type) {
                return Carbon::parse($certificate_type->created_at)->format('d/m/Y h:i A');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 