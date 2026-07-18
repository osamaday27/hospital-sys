<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Medical\DischargeSummary;
use App\Models\Medical\DischargeSummaryMedication;
use Illuminate\Support\Facades\Auth;

class DischargeSummaryForm extends Component
{
    public $patient_id;
    public $visit_id;
    public $booking_id;

    public $final_diagnosis;
    public $operation_done;
    public $significant_history;
    public $case_progression;
    public $patient_condition;

    public $diet_instruction;
    public $diet_other;

    public $implanted_device = false;
    public $implanted_device_instructions;

    public $follow_up_activity;
    public $follow_up_visit;
    public $emergency_instructions;

    public $notes;
    public $copy_given = false;

    public $doctor_name;
    public $doctor_signature;
    public $report_date;
    public $report_time;

    public $medications = [];

    public $currentSummaryId = null;

    public function mount($patient_id, $visit_id = null, $booking_id = null)
    {
        $this->patient_id = $patient_id;
        $this->visit_id = $visit_id;
        $this->booking_id = $booking_id;

        $this->report_date = now()->toDateString();
        $this->report_time = now()->format('H:i');

        $latestSummary = DischargeSummary::with('medications')
            ->where('patient_id', $this->patient_id)
            ->when($this->visit_id, function($query){
                $query->where('visit_id', $this->visit_id);
            })
            ->orderByDesc('created_at')
            ->first();

        if($latestSummary) {
            $this->currentSummaryId = $latestSummary->id;
            $this->final_diagnosis = $latestSummary->final_diagnosis;
            $this->operation_done = $latestSummary->operation_done;
            $this->significant_history = $latestSummary->significant_history;
            $this->case_progression = $latestSummary->case_progression;
            $this->patient_condition = $latestSummary->patient_condition;
            $this->diet_instruction = $latestSummary->diet_instruction;
            $this->diet_other = $latestSummary->diet_other;
            $this->implanted_device = $latestSummary->implanted_device;
            $this->implanted_device_instructions = $latestSummary->implanted_device_instructions;
            $this->follow_up_activity = $latestSummary->follow_up_activity;
            $this->follow_up_visit = $latestSummary->follow_up_visit;
            $this->emergency_instructions = $latestSummary->emergency_instructions;
            $this->notes = $latestSummary->notes;
            $this->copy_given = $latestSummary->copy_given;
            $this->doctor_name = $latestSummary->doctor_name;
            $this->doctor_signature = $latestSummary->doctor_signature;
            $this->report_date = $latestSummary->report_date;
            $this->report_time = $latestSummary->report_time;

            $this->medications = [];
            foreach ($latestSummary->medications as $med) {
                $this->medications[] = [
                    'medication_name' => $med->medication_name,
                    'dose_route' => $med->dose_route,
                    'frequency' => $med->frequency,
                ];
            }
        }
    }

    public function addMedication()
    {
        $this->medications[] = [
            'medication_name' => '',
            'dose_route' => '',
            'frequency' => '',
        ];
    }

    public function removeMedication($index)
    {
        unset($this->medications[$index]);
        $this->medications = array_values($this->medications);
    }

    public function save()
    {
        if ($this->currentSummaryId) {
            // تحديث السجل القديم
            $summary = DischargeSummary::find($this->currentSummaryId);
            $summary->update([
                'final_diagnosis' => $this->final_diagnosis,
                'operation_done' => $this->operation_done,
                'significant_history' => $this->significant_history,
                'case_progression' => $this->case_progression,
                'patient_condition' => $this->patient_condition,
                'diet_instruction' => $this->diet_instruction,
                'diet_other' => $this->diet_other,
                'implanted_device' => $this->implanted_device,
                'implanted_device_instructions' => $this->implanted_device_instructions,
                'follow_up_activity' => $this->follow_up_activity,
                'follow_up_visit' => $this->follow_up_visit,
                'emergency_instructions' => $this->emergency_instructions,
                'notes' => $this->notes,
                'copy_given' => $this->copy_given,
                'doctor_name' => $this->doctor_name,
                'doctor_signature' => $this->doctor_signature,
                'report_date' => $this->report_date,
                'report_time' => $this->report_time,
            ]);

            // حذف الأدوية القديمة
            $summary->medications()->delete();

            // إعادة إدخال الأدوية
            foreach ($this->medications as $med) {
                $summary->medications()->create([
                    'medication_name' => $med['medication_name'],
                    'dose_route' => $med['dose_route'],
                    'frequency' => $med['frequency'],
                ]);
            }
        } else {
            // إنشاء جديد
            $summary = DischargeSummary::create([
                'patient_id' => $this->patient_id,
                'visit_id' => $this->visit_id,
                'booking_id' => $this->booking_id,
                'final_diagnosis' => $this->final_diagnosis,
                'operation_done' => $this->operation_done,
                'significant_history' => $this->significant_history,
                'case_progression' => $this->case_progression,
                'patient_condition' => $this->patient_condition,
                'diet_instruction' => $this->diet_instruction,
                'diet_other' => $this->diet_other,
                'implanted_device' => $this->implanted_device,
                'implanted_device_instructions' => $this->implanted_device_instructions,
                'follow_up_activity' => $this->follow_up_activity,
                'follow_up_visit' => $this->follow_up_visit,
                'emergency_instructions' => $this->emergency_instructions,
                'notes' => $this->notes,
                'copy_given' => $this->copy_given,
                'doctor_name' => $this->doctor_name,
                'doctor_signature' => $this->doctor_signature,
                'report_date' => $this->report_date,
                'report_time' => $this->report_time,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->medications as $med) {
                $summary->medications()->create([
                    'medication_name' => $med['medication_name'],
                    'dose_route' => $med['dose_route'],
                    'frequency' => $med['frequency'],
                ]);
            }

            $this->currentSummaryId = $summary->id;
        }

        session()->flash('success', 'Discharge summary saved successfully.');
    }

    public function render()
    {
        return view('livewire.surgical.discharge-summary-form');
    }
}