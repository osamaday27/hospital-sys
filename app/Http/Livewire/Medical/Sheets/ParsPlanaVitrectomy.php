<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;
use App\Models\Medical\OphthalmicSurgicalReport;
use App\Models\Administrative\adm_b_surgery_bookings;

class ParsPlanaVitrectomy extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $eye = '';
    public $anesthesia = '';
    public $surgeryType = 'Pars Plana Vitrectomy';
    public $preOpDiagnosis = '';
    public $pvr_grade = '';
    public $retinal_type = '';
    public $combined = [];
    public $postOpDiagnosis = 'Same as preoperative';
    public $complications = '';
    public $position = '';
    public $tamponade = '';
    public $laser = '';
    public $membrane = '';
    public $trocarSize = '';
    public $trocarPosition = '';
    public $vitrectomyDetails = '';
    public $postOpMeds = '';
    public $followUp = '';
    public $stage5 = '';
    public $traction_direction = '';
    public $silicone_type = '';
    public $narratives = ['intro' => '', 'details' => ''];
    public $completionPercentage = 0;
    public $activeTab = 'preop'; // tab الحالي

    protected $rules = [
        'eye' => 'required|string',
        'anesthesia' => 'required|string',
        'surgeryType' => 'required|string',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('Pars Plana Vitrectomy');
        if ($reservationId) {
            $this->reservationId = $reservationId;
            $this->loadBookingData();
        } elseif ($patient_id) {
            $this->patient_id = $patient_id;
            $this->patient = \App\Models\Administrative\medical_record::find($patient_id);
        }
        $this->generateNarrative();
    }

    // ✅ أي تحديث في الفورم يغير التاب التالي تلقائياً
public function updated($field)
{
    $this->generateNarrative();
    $this->calculateCompletion();

 
}


    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function calculateCompletion()
    {
        $fields = ['eye', 'anesthesia', 'surgeryType'];
        $filled = 0;
        foreach ($fields as $f) {
            if (!empty($this->$f)) $filled++;
        }
        $this->completionPercentage = round(($filled / count($fields)) * 100);
    }

    public function loadBookingData()
    {
        $this->booking = adm_b_surgery_bookings::with(['patient', 'medicalTeam.employee'])->find($this->reservationId);
        if (empty($this->booking)) {
            session()->flash('error', 'Booking not found');
            return;
        }
        $this->patient = $this->booking->patient;
        $this->patient_id = !empty($this->patient->id) ? $this->patient->id : null;
    }

    public function generateNarrative()
    {
        $eyeText = match (strtoupper($this->eye)) {
            'OD' => 'right eye (OD)',
            'OS' => 'left eye (OS)',
            'OD/OS' => 'both eyes (OD/OS)',
            default => 'eye (unspecified)'
        };

        $intro = "Pars Plana Vitrectomy was performed for {$eyeText} under {$this->anesthesia}.";
        $details = "Three " . ($this->trocarSize ?: '23') . "G trocars were placed at " .
            ($this->trocarPosition ?: '3.5 mm') . " from the limbus. ";

        $details .= "Vitrectomy with removal of " . ($this->membrane ?: 'posterior hyaloid and epiretinal membranes') . ". ";

        if ($this->preOpDiagnosis == 'Rhegmatogenous retinal detachment') {
            $details .= "Retinal break identified, flattened using " . ($this->tamponade ?: 'Air') .
                ". Endolaser applied to breaks. ";
        } elseif ($this->preOpDiagnosis == 'Tractional retinal detachment') {
            $details .= "Membranes peeled, traction released, Endolaser to ischemic areas. Tamponade: " . ($this->tamponade ?: 'Silicone oil') . ". ";
        } elseif ($this->preOpDiagnosis == 'Macular hole') {
            $details .= "ILM stained with " . ($this->laser ?: 'Brilliant Blue') .
                " and peeled. Gas tamponade applied; face-down positioning advised. ";
        } elseif ($this->preOpDiagnosis == 'ROP stage 5') {
            $details .= "Anterior chamber maintainer used, lensectomy, vitrectomy, tractional membranes removed (" . ($this->traction_direction ?: 'outside-in') .
                "). Tamponade: " . ($this->tamponade ?: 'Silicone oil') . ". ";
        }

        $details .= "Sclerotomies checked watertight. Subconjunctival antibiotics and steroids given. ";
        $details .= "Position: " . ($this->position ?: 'Face down') .
            ". Medications: " . ($this->postOpMeds ?: 'Topical antibiotics and steroids') .
            ". Follow-up after " . ($this->followUp ?: '3 days') . ".";

        $this->narratives = compact('intro', 'details');
    }

    public function saveReport()
    {
        $this->validate();

        try {
            $doctorId = null;
            if (!empty($this->booking) && $this->booking->medicalTeam && $this->booking->medicalTeam->first()) {
                $doctorId = $this->booking->medicalTeam->first()->employee_id;
            }

            OphthalmicSurgicalReport::updateOrCreate(
                ['booking_id' => $this->reservationId, 'report_type' => 'Pars Plana Vitrectomy'],
                [
                    'patient_id' => $this->patient_id,
                    'visit_id' => $this->booking->visit_id ?? null,
                    'doctor_id' => $doctorId,
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => $this->eye,
                    'anesthesia' => $this->anesthesia,
                    'surgical_procedure' => $this->surgeryType,
                    'operative_details' => json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                    'preoperative_diagnosis' => $this->preOpDiagnosis,
                    'postoperative_diagnosis' => $this->postOpDiagnosis,
                    'complications' => $this->complications ?: 'None',
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ Report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.medical.sheets.pars-plana-vitrectomy', [
            'age' => $this->patient->age ?? 'N/A',
            'surgeon' => $this->booking->medicalTeam->first()->employee->full_name
                ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}