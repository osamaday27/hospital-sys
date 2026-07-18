<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Exports\EmployeeAcknowledgmentReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display the employee acknowledgment form selection page
     */
    public function employeeAcknowledgmentForm()
    {
        $employees = Employee::with(['job', 'department', 'section'])
            ->where('employee_status', 'in_service')
            ->get();

        return view('reports.employee-acknowledgment-form', compact('employees'));
    }

    /**
     * Generate employee acknowledgment report as PDF
     */
    public function generateEmployeeAcknowledgmentPdf(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $employee = Employee::with(['job', 'department', 'section'])
            ->findOrFail($request->employee_id);

        $pdf = Pdf::loadView('reports.employee-acknowledgment', compact('employee'));
        
        return $pdf->download("employee_acknowledgment_{$employee->employee_code}_{$employee->name}.pdf");
    }

    /**
     * Generate employee acknowledgment report as Excel
     */
    public function generateEmployeeAcknowledgmentExcel(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $employee = Employee::with(['job', 'department', 'section'])
            ->findOrFail($request->employee_id);

        return Excel::download(
            new EmployeeAcknowledgmentReport($employee),
            "employee_acknowledgment_{$employee->employee_code}_{$employee->name}.xlsx"
        );
    }

    /**
     * Preview employee acknowledgment report
     */
    public function previewEmployeeAcknowledgment(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $employee = Employee::with(['job', 'department', 'section'])
            ->findOrFail($request->employee_id);

        return view('reports.employee-acknowledgment', compact('employee'));
    }

    /**
     * Generate acknowledgment for multiple employees
     */
    public function generateBulkEmployeeAcknowledgment(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        $employees = Employee::with(['job', 'department', 'section'])
            ->whereIn('id', $request->employee_ids)
            ->get();

        $zip = new \ZipArchive();
        $zipFileName = 'employee_acknowledgments_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($employees as $employee) {
                $pdf = Pdf::loadView('reports.employee-acknowledgment', compact('employee'));
                $pdfContent = $pdf->output();
                
                $fileName = "employee_acknowledgment_{$employee->employee_code}_{$employee->name}.pdf";
                $zip->addFromString($fileName, $pdfContent);
            }
            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
