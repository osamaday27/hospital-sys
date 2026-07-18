<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\models\HR\hr_allowances;
use App\models\HR\hr_allowances_types;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrallowancescontroller extends Controller
{
    public function index()
    {
        return view('hr.payroll.settings.allowances.index');
    }

    public function create()
    {
        $allowance_types = hr_allowances_types::all();
        return view('hr.payroll.settings.allowances.create', compact('allowance_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'allowance_id' => 'required|exists:hr_allowances_types,id',
            'amount' => 'required|numeric|min:0',
        ]);

        hr_allowances::create($request->all());

        return redirect()->route('hr_allowances.index')->with('success', 'Allowance created successfully.');
    }

    public function edit($id)
    {
        $allowance = hr_allowances::findOrFail($id);
        $allowance_types = hr_allowances_types::all();
        return view('hr.payroll.settings.allowances.edit', compact('allowance', 'allowance_types'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'allowance_id' => 'required|exists:hr_allowances_types,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $allowance = hr_allowances::findOrFail($id);
        $allowance->update($request->all());

        return redirect()->route('hr_allowances.index')->with('success', 'Allowance updated successfully.');
    }

    public function destroy($id)
    {
        $allowance = hr_allowances::findOrFail($id);
        $allowance->delete();

        return response()->json(['success' => 'Allowance deleted successfully.']);
    }

    public function get_allowances()
    {
        $allowances = hr_allowances::with('allowance_type');

        return DataTables::of($allowances)
            ->addColumn('action', function ($allowance) {
                return '
                    <a href="' . route('hr_allowances.edit', $allowance->id) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                    <a href="javascript:void(0)" onclick="deleteFunction(' . $allowance->id . ')" class="btn btn-danger btn-sm">' . trans('buttons.delete') . '</a>';
            })
            ->addColumn('allowance_type_name', function ($allowance) {
                return $allowance->allowance_type ? $allowance->allowance_type->name_ar : 'N/A';
            })
            ->editColumn('amount', function ($allowance) {
                return number_format($allowance->amount, 2);
            })
            ->editColumn('created_at', function ($allowance) {
                return Carbon::parse($allowance->created_at)->format('d/m/Y h:i A');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 