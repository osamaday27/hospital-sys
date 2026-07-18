<?php

namespace App\Http\Controllers\administrative\surgical;

use App\Http\Controllers\Controller;
use App\Models\Medical\OphthalmicSurgicalReport;

class SurgicalReportPrintController extends Controller
{
    public function print($id)
    {
        $report = OphthalmicSurgicalReport::with(['patient'])->findOrFail($id);
        return view('medical.reports.print', compact('report'));
    }
}