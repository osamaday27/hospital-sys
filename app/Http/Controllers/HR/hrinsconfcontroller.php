<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_ins_conf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class hrinsconfcontroller extends Controller
{
    public function index()
    {
        $insConfs = hr_ins_conf::all();
        return view('hr.payroll.settings.ins_conf.index', compact('insConfs'));
    }

    public function create()
    {
        return view('hr.payroll.settings.ins_conf.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_ins_conf',
            'employee_share' => 'required|numeric',
            'company_share' => 'required|numeric',
            'social_insurance_margin' => 'required|numeric',
        ]);

        hr_ins_conf::create($validated);

        return redirect()->route('hr_ins_conf.index')
            ->with('success', 'Insurance configuration created successfully.');
    }

    public function show(hr_ins_conf $insConfs)
    {
        return view('hr.payroll.settings.ins_conf.show', compact('insConfs'));
    }

    public function edit(hr_ins_conf $insConfs)
    {
        return view('hr.payroll.settings.ins_conf.edit', compact('insConfs'));
    }

    public function update(Request $request, hr_ins_conf $insConfs)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_ins_conf,name,'.$insConfs->id,
            'employee_share' => 'required|numeric',
            'company_share' => 'required|numeric',
            'social_insurance_margin' => 'required|numeric',
        ]);

        $insConfs->update($validated);

        return redirect()->route('hr_ins_conf.index')
            ->with('success', 'Insurance configuration updated successfully.');
    }

    public function destroy(hr_ins_conf $hrInsConf)
    {
        $hrInsConf->delete();

        return redirect()->route('hr_ins_conf.index')
            ->with('success', 'Insurance configuration deleted successfully.');
    }

    public function toggleStatus(Request $request, hr_ins_conf $hrInsConf)
    {
        // If enabling this one, disable all others
        if ($request->status) {
            hr_ins_conf::where('id', '!=', $hrInsConf->id)->update(['status' => false]);
        }
        
        $hrInsConf->update(['status' => $request->status]);
    
        return redirect()->route('hr_ins_conf.index')
            ->with('success', 'Status updated successfully.');
    }


}