<?php

namespace App\Http\Controllers\medical\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\medical_record;
use App\models\finance\service;
use App\models\medical\patienteducation;

class patienteducationcontroller extends Controller
{
 
    // حفظ طلب العملية الجراحية
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'assessment_date' => 'required|date',
            'assessment_time' => 'required',
            'department' => 'required|string',
            'education_recipient' => 'required|string',
            'relation_to_patient' => 'nullable|string',
            'education_level' => 'required|string',
            'learning_ability' => 'required|string',
            'main_language' => 'required|string',
            'other_language' => 'nullable|string',
            'barriers' => 'nullable|array',
            'interventions' => 'nullable|array',
            'nurse_name' => 'required|string',
            'nurse_date' => 'required|date',
            'nurse_time' => 'required',
            'education_sessions' => 'nullable|array',
            'education_method' => 'nullable|array',
            'evaluation' => 'nullable|string',
            'action_taken' => 'nullable|string',
        ]);
        $patientEducation = patienteducation::create($validatedData);
        return response()->json([
            'message' => 'تم حفظ البيانات بنجاح',
            'data' => $patientEducation,
        ], 201);
    }
  
}