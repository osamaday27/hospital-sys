<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\models\HR\hr_allowances_types;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrallowancestypescontroller extends Controller
{
    public function index()
    {
        return view('hr.payroll.settings.allowances_types.index');
    }

    public function create()
    {
        return view('hr.payroll.settings.allowances_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        hr_allowances_types::create($request->all());

        return redirect()->route('hr_allowances_types.index')->with('success', 'Allowance Type created successfully.');
    }

    public function edit($id)
    {
        $allowance_type = hr_allowances_types::findOrFail($id);
        return view('hr.payroll.settings.allowances_types.edit', compact('allowance_type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $allowance_type = hr_allowances_types::findOrFail($id);
        $allowance_type->update($request->all());

        return redirect()->route('hr_allowances_types.index')->with('success', 'Allowance Type updated successfully.');
    }

    public function destroy($id)
    {
        $allowance_type = hr_allowances_types::findOrFail($id);
        $allowance_type->delete();

        return response()->json(['success' => 'Allowance Type deleted successfully.']);
    }

    public function get_allowances_types()
    {
        $allowance_types = hr_allowances_types::query();

        return DataTables::of($allowance_types)
            ->addColumn('action', function ($allowance_type) {
                return '
                    <a href="' . route('hr_allowances_types.edit', $allowance_type->id) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="deleteFunction(' . $allowance_type->id . ')" class="btn btn-danger btn-sm">' . trans('buttons.delete') . '</a>';
            })
            ->editColumn('created_at', function ($allowance_type) {
                return Carbon::parse($allowance_type->created_at)->format('d/m/Y h:i A');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 