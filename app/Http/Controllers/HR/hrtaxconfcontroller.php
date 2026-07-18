<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_tax_conf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class hrtaxconfcontroller extends Controller
{
    public function index()
    {
        $taxConfs = hr_tax_conf::all();
        return view('hr.payroll.settings.tax_conf.index', compact('taxConfs'));
    }

    public function create()
    {
        return view('hr.payroll.settings.tax_conf.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_tax_conf',
            'exemption_margin' => 'required|numeric',
            'segment_1' => 'required|numeric',
            'segment_2' => 'required|numeric',
            'segment_3' => 'required|numeric',
            'segment_4' => 'required|numeric',
            'segment_5' => 'required|numeric',
            'segment_6' => 'required|numeric',
            'segment_7' => 'required|numeric',
            'segment_8' => 'required|numeric',
            'segment_9' => 'required|numeric',
            'segment_10' => 'required|numeric',
        ]);

        hr_tax_conf::create($validated);

        return redirect()->route('hr_tax_conf.index')
            ->with('success', 'Tax configuration created successfully.');
    }

    public function show(hr_tax_conf $hrTaxConf)
    {
        return view('hr.payroll.settings.tax_conf.show', compact('hrTaxConf'));
    }

    public function edit(HrTahr_tax_confxConf $hrTaxConf)
    {
        return view('hr.payroll.settings.tax_conf.edit', compact('hrTaxConf'));
    }

    public function update(Request $request, hr_tax_conf $hrTaxConf)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hr_tax_conf,name,'.$hrTaxConf->id,
            'exemption_margin' => 'required|numeric',
            'segment_1' => 'required|numeric',
            'segment_2' => 'required|numeric',
            'segment_3' => 'required|numeric',
            'segment_4' => 'required|numeric',
            'segment_5' => 'required|numeric',
            'segment_6' => 'required|numeric',
            'segment_7' => 'required|numeric',
            'segment_8' => 'required|numeric',
            'segment_9' => 'required|numeric',
            'segment_10' => 'required|numeric',
        ]);

        $hrTaxConf->update($validated);

        return redirect()->route('hr_tax_conf.index')
            ->with('success', 'Tax configuration updated successfully.');
    }

    public function destroy(hr_tax_conf $hrTaxConf)
    {
        $hrTaxConf->delete();

        return redirect()->route('hr_tax_conf.index')
            ->with('success', 'Tax configuration deleted successfully.');
    }

    public function toggleStatus(Request $request, hr_tax_conf $hrTaxConf)
    {
        // If enabling this one, disable all others
        if ($request->status) {
            hr_tax_conf::where('id', '!=', $hrTaxConf->id)->update(['status' => false]);
        }
        
        $hrTaxConf->update(['status' => $request->status]);
    
        return redirect()->route('hr_tax_conf.index')
            ->with('success', 'Status updated successfully.');
    }


}