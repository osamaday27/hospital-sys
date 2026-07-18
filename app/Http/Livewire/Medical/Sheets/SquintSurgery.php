<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;
use App\Models\Medical\OphthalmicSurgicalReport;
use App\Models\Administrative\adm_b_surgery_bookings;

class SquintSurgery extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;

    public $eye = '';
    public $anesthesia = '';
    public $squint_type = '';
    public $muscle = '';
    public $procedure_type = '';
    public $mm_recession = '';
    public $mm_resection = '';
    public $other_mm = '';
    public $additional_procedure = '';
    public $complications = 'None';
    public $narrative = '';

    public $activeTab = 'preop';

    protected $rules = [
        'eye' => 'required|string',
        'anesthesia' => 'required|string',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('Squint Surgery');
        if ($reservationId) {
            $this->reservationId = $reservationId;
            $this->loadBookingData();
        } elseif ($patient_id) {
            $this->patient_id = $patient_id;
            $this->patient = \App\Models\Administrative\medical_record::find($patient_id);
        }
        $this->generateNarrative();
    }

    public function updated($field)
    {
        $this->generateNarrative();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function loadBookingData()
    {
        $this->booking = adm_b_surgery_bookings::with(['patient', 'medicalTeam.employee'])->find($this->reservationId);
        if (!$this->booking) {
            session()->flash('error', 'Booking not found');
            return;
        }
        $this->patient = $this->booking->patient;
        $this->patient_id = $this->patient->id ?? null;
    }

  public function generateNarrative()
{
    // ✅ استبدال match بـ switch لمتوافقة مع كل الإصدارات
    switch (strtoupper(trim($this->eye))) {
        case 'OD':
            $eyeText = 'the right eye (OD)';
            break;
        case 'OS':
            $eyeText = 'the left eye (OS)';
            break;
        case 'OU':
        case 'OD/OS':
            $eyeText = 'both eyes (OU)';
            break;
        default:
            $eyeText = 'the eye (unspecified)';
            break;
    }

    $details = "Squint surgery was performed for {$eyeText} under " . ($this->anesthesia ?: 'general') . " anesthesia. ";

    if ($this->squint_type) {
        $details .= "Diagnosis: {$this->squint_type}. ";
    }

    if ($this->muscle && $this->procedure_type) {
        $details .= "Procedure: {$this->procedure_type} of {$this->muscle}. ";
    }

    if ($this->mm_recession) {
        $details .= "Recession: {$this->mm_recession} mm. ";
    }

    if ($this->mm_resection) {
        $details .= "Resection: {$this->mm_resection} mm. ";
    }

    if ($this->other_mm) {
        $details .= "Other adjustment: {$this->other_mm} mm. ";
    }

    if ($this->additional_procedure) {
        $details .= "Additional procedure: {$this->additional_procedure}. ";
    }

    if (!empty($this->complications) && strtolower($this->complications) !== 'none') {
        $details .= "Intraoperative complications included: {$this->complications}. ";
    } else {
        $details .= "No intraoperative complications were encountered. ";
    }

    $details .= "Conjunctiva closed with 8/0 Vicryl, subconjunctival injection of steroid and antibiotic given. Patient tolerated procedure well.";

    $this->narrative = trim($details);
}


    public function saveReport()
    {
        $this->validate();

        try {
            $doctorId = $this->booking->medicalTeam->first()->employee_id ?? null;
            OphthalmicSurgicalReport::updateOrCreate(
                ['booking_id' => $this->reservationId, 'report_type' => 'Squint Surgery'],
                [
                    'patient_id' => $this->patient_id,
                    'visit_id' => $this->booking->visit_id ?? null,
                    'doctor_id' => $doctorId,
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => $this->eye,
                    'anesthesia' => $this->anesthesia,
                    'surgical_procedure' => 'Squint Surgery',
                    'operative_details' => $this->narrative,
                    'preoperative_diagnosis' => $this->squint_type ?: 'Strabismus',
                    'postoperative_diagnosis' => 'Same',
                    'complications' => $this->complications,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ Squint Surgery Report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.medical.sheets.squint-surgery', [
            'age' => $this->patient->age ?? 'N/A',
            'surgeon' => $this->booking->medicalTeam->first()->employee->full_name ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}