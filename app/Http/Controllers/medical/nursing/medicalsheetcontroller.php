<?php
namespace App\Http\Controllers\medical\nursing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class medicalsheetcontroller extends Controller
{
    public function painAssessment()
    {
        return view('medical.nursing.components.program.medical_sheet.pain_assessment');
    }

    public function initialAssessment()
    {
        return view('medical.nursing.components.program.medical_sheet.Initial_assessment');
    }

    public function follow_up()
    {
        return view('medical.nursing.components.program.medical_sheet.follow_up_assessment');
    }

    public function labTests()
    {
        return view('medical.nursing.components.program.medical_sheet.lab_tests');
    }

    public function radiology()
    {
        return view('medical.nursing.components.program.medical_sheet.radiology');
    }
}