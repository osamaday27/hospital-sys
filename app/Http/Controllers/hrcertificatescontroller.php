<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\HR\hr_certificates;

class hrcertificatescontroller extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'certificate_name' => 'required|string|max:255',
            'certificate_type' => 'required',
            'join_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'school_name' => 'nullable|string|max:255',
            'certificate_file' => 'nullable|string|max:255',
        ]);
        $certificate = hr_certificates::create($validated);
        return response()->json($certificate);
    }

    public function update(Request $request, $id)
    {
        $certificate = hr_certificates::findOrFail($id);
        $validated = $request->validate([
            'certificate_name' => 'required|string|max:255',
            'certificate_type' => 'required',
            'join_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'school_name' => 'nullable|string|max:255',
            'certificate_file' => 'nullable|string|max:255',
        ]);
        $certificate->update($validated);
        return response()->json($certificate);
    }

    public function destroy($id)
    {
        $certificate = hr_certificates::findOrFail($id);
        $certificate->delete();
        return response()->json(['success' => true]);
    }

    public function index($employee_id)
    {
        $certificates = hr_certificates::where('employee_id', $employee_id)->get();
        return response()->json($certificates);
    }
} 