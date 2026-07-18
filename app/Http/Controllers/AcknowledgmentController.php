<?php

namespace App\Http\Controllers;

use App\models\HR\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class AcknowledgmentController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'acknowledgment_type' => 'required|string',
            'format' => 'required|in:word,pdf',
            'language' => 'required|in:ar,en'
        ]);

        $employee = Employee::with(['emp_pos', 'emp_dep', 'emp_sec'])
            ->findOrFail($request->employee_id);

        $acknowledgmentType = $request->acknowledgment_type;
        $language = $request->language;
        $format = $request->format;

        // Generate the appropriate acknowledgment document
        switch ($acknowledgmentType) {
            case 'policy_acknowledgment':
                return $this->generatePolicyAcknowledgment($employee, $language, $format);
            case 'ethics_confidentiality_acknowledgment':
                return $this->generateEthicsConfidentialityAcknowledgment($employee, $language, $format);
            case 'qualifications_verification_request':
                return $this->generateQualificationsVerificationRequest($employee, $language, $format);
            case 'departmental_job_orientation':
                return $this->generateDepartmentalJobOrientation($employee, $language, $format);
            case 'new_employee_general_orientation':
                return $this->generateNewEmployeeGeneralOrientation($employee, $language, $format);
            case 'outsourced_employee_orientation':
                return $this->generateOutsourcedEmployeeOrientation($employee, $language, $format);
            case 'training_needs_assessment':
                return $this->generateTrainingNeedsAssessment($employee, $language, $format);
            case 'annual_training_plan':
                return $this->generateAnnualTrainingPlan($employee, $language, $format);
            case 'training_course_effectiveness':
                return $this->generateTrainingCourseEffectiveness($employee, $language, $format);
            default:
                return $this->generatePolicyAcknowledgment($employee, $language, $format);
        }
    }

    private function generatePolicyAcknowledgment($employee, $language, $format)
    {
        // Generate filename
        $filename = 'acknowledgment_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.pdf';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'policy_acknowledgment',
            'title' => $language === 'ar' ? 'إقرار الاطلاع على دليل السياسات والإجراءات' : 'Policy and Procedures Acknowledgment'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.policy', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateEthicsConfidentialityAcknowledgment($employee, $language, $format)
    {
        // Generate filename
        $filename = 'ethics_confidentiality_acknowledgment_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'ethics_confidentiality_acknowledgment',
            'title' => $language === 'ar' ? 'ميثاق الأخلاق والسرية' : 'Code of Ethics and Confidentiality'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.ethics_confidentiality', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateQualificationsVerificationRequest($employee, $language, $format)
    {
        // Generate filename
        $filename = 'qualifications_verification_request_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'qualifications_verification_request',
            'title' => $language === 'ar' ? 'طلب تحقق من مؤهلات' : 'Qualifications Verification Request'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.qualifications_verification', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateDepartmentalJobOrientation($employee, $language, $format)
    {
        // Generate filename
        $filename = 'departmental_job_orientation_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'departmental_job_orientation',
            'title' => $language === 'ar' ? 'البرنامج التعريفي للقسم والوظيفة' : 'Departmental & Job-Specific Orientation'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.departmental_job_orientation', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateNewEmployeeGeneralOrientation($employee, $language, $format)
    {
        // Generate filename
        $filename = 'new_employee_general_orientation_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'new_employee_general_orientation',
            'title' => $language === 'ar' ? 'البرنامج التعريفى العام للموظفين الجدد' : 'New Employee General Orientation Program'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.new_employee_general_orientation', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateOutsourcedEmployeeOrientation($employee, $language, $format)
    {
        // Generate filename
        $filename = 'outsourced_employee_orientation_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'outsourced_employee_orientation',
            'title' => $language === 'ar' ? 'البرنامج التعريفي العام للعاملين المتعاقد معهم من جهات خارجية' : 'New Employee General Orientation Program for Outsourced Workers'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.outsourced_employee_orientation', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateTrainingNeedsAssessment($employee, $language, $format)
    {
        // Generate filename
        $filename = 'training_needs_assessment_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'training_needs_assessment',
            'title' => $language === 'ar' ? 'تقييم الاحتياجات التدريبية - Training Needs Assessment' : 'Training Needs Assessment'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.training_needs_assessment', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateAnnualTrainingPlan($employee, $language, $format)
    {
        // Generate filename
        $filename = 'annual_training_plan_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'annual_training_plan',
            'title' => $language === 'ar' ? 'الخطة التدريبية السنوية - Annual Training Plan' : 'Annual Training Plan'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.annual_training_plan', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

    private function generateTrainingCourseEffectiveness($employee, $language, $format)
    {
        // Generate filename
        $filename = 'training_course_effectiveness_' . ($employee->employee_code ?? 'unknown') . '_' . date('Y-m-d') . '.html';
        
        // Prepare data for the view
        $data = [
            'employee' => $employee,
            'language' => $language,
            'acknowledgment_type' => 'training_course_effectiveness',
            'title' => $language === 'ar' ? 'نموذج فاعلية البرنامج التدريبي - Training Course Effectiveness' : 'Training Course Effectiveness Form'
        ];

        // Set the locale for translations
        app()->setLocale($language);
        
        // Generate HTML for printing (better Arabic support)
        $html = view('acknowledgments.training_course_effectiveness', $data)->render();
        
        // Return HTML response with proper headers for printing
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '.html"');
    }

}
