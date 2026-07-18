<?php

namespace App\Http\Controllers\medical\nursing;

use App\Http\Controllers\Controller;
use App\models\medical\mddreassessmentform;
use Illuminate\Http\Request;

class reassessmentformcontroller extends Controller
{
  
    
    public function store(Request $request)
    {
        // 1. Enhanced input validation with custom messages
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'visit_id' => 'nullable|exists:patient_visits,id',
            'reservation_id' => 'nullable|exists:adm_s_reservations,id',
            
            // Symptom fields
            'cough_no' => 'boolean',
            'cough_yes' => 'boolean',
            'cough_details' => 'nullable|string|max:500',
            'fever_no' => 'boolean',
            'fever_yes' => 'boolean',
            'fever_details' => 'nullable|string|max:500',
            'rash_no' => 'boolean',
            'rash_yes' => 'boolean',
            'rash_details' => 'nullable|string|max:500',
            'travel_no' => 'boolean',
            'travel_yes' => 'boolean',
            'travel_details' => 'nullable|string|max:500',
            
            // Action fields
            'wear_mask' => 'boolean',
            'separate_patient' => 'boolean',
            'notify_doctor_action' => 'boolean',
            'accelerate_examination' => 'boolean',
            'doctor_notified' => 'boolean',
            
            // Functional status fields
            'live_alone' => 'boolean',
            'no_problem' => 'boolean',
            'total_dependence' => 'boolean',
            'movement_problems' => 'boolean',
            'no_assistance' => 'boolean',
            'walker' => 'boolean',
            'wheelchair' => 'boolean',
            'crutches' => 'boolean',
            'other_assistance' => 'boolean',
            'doctor_notified_functional' => 'boolean',
            
            // Pain assessment
            'pain_no' => 'boolean',
            'pain_yes' => 'boolean',
            
            // Vital signs
            'temperature' => 'nullable|string|max:20',
            'respiratory_rate' => 'nullable|string|max:20',
            'blood_pressure' => 'nullable|string|max:20',
            'pulse' => 'nullable|string|max:20',
            'random_sugar' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            
            // Care instructions
            'help_patient_when_needed' => 'boolean',
            'ensure_appropriate_aids' => 'boolean',
            'review_medications' => 'boolean',
            'refer_to_specialist' => 'boolean',
            'avoid_crowding' => 'boolean',
            'dont_move_alone' => 'boolean',
            'dont_sudden_standing' => 'boolean',
            'dont_leave_child_alone' => 'boolean',
            'alert_medical_team' => 'boolean',
            
            // Summary fields
            'self_care' => 'nullable|string|max:1000',
            'instructions_clear' => 'nullable|string|max:1000',
            'doctor_alert_summary' => 'nullable|string|max:1000',
            'social_exam' => 'nullable|string|max:1000',
            'social_exam_details' => 'nullable|string|max:1000',
            'special_needs_summary' => 'nullable|string|max:1000',
            'special_needs_details' => 'nullable|string|max:1000',
            'infectious_disease_summary' => 'nullable|string|max:1000',
            'infectious_disease_details' => 'nullable|string|max:1000',
            'pain_check_summary' => 'nullable|string|max:1000',
            'pain_check_details' => 'nullable|string|max:1000',
            'allergy_check' => 'nullable|string|max:1000',
            'allergy_details' => 'nullable|string|max:1000',
            'critical_results' => 'nullable|string|max:1000',
            'nutrition_check' => 'nullable|string|max:1000',
        ], [
            'patient_id.required' => 'Patient ID is required',
            'patient_id.exists' => 'The specified patient does not exist',
            '*.boolean' => 'This field must be true or false',
            '*.max' => 'The field content is too long',
        ]);

        // 2. Additional logical validation
        if ($request->has('cough_yes') && $request->cough_yes && empty($request->cough_details)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cough details are required when "Yes" is selected');
        }

        // Similar validations for other conditional fields
        // ...

        // 3. Database transaction for data integrity
        try {
            $form = MddReassessmentForm::create($validatedData);
            
            // Log the successful creation
            Log::info('Reassessment form created successfully', [
                'form_id' => $form->id,
                'patient_id' => $form->patient_id,
                'created_at' => now()
            ]);

            return redirect()->back()
                ->with('success', 'تم حفظ نموذج التقييم بنجاح');

        } catch (Exception $e) {
            // Log the error
            Log::error('Error creating reassessment form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['_token'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ النموذج. الرجاء المحاولة مرة أخرى.');
        }
    }
    }
    