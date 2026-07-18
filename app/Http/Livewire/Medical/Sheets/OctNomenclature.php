<?php

namespace App\Http\Livewire\Medical\Sheets;

use App\Models\Medical\OphthalmicSurgicalReport;
use App\Models\Administrative\adm_b_surgery_bookings;

class OctNomenclature extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $eye = '';
    public $diagnosis = '';
    public $interpretation = '';
    public $completionPercentage = 0;

    // 🔹 Anterior Segment
    public $epithelium_integrity, $epithelium_thickness, $epithelium_regular, $epithelium_opacity;
    public $stroma_thickness_status, $stroma_opacity_location, $stroma_opacity_site, $stroma_opacity_value;
    public $cct_status;
    public $angle_temporal, $angle_nasal, $angle_degree, $angle_classification;

    // 🔹 Macula
    public $pvd, $vmt, $vmt_pattern;
    public $erm, $wrinkling, $traction, $detachment, $edema_ap;
    public $ocular_contour, $foveal_pit;

    // 🔹 Retinal Layers
    public $reflectivity, $hyporeflective_type, $hyperreflective_type;
    public $edema_location, $edema_shape;
    public $elm, $ellipsoid_zone, $neurosensory_retina;
    public $rpe_line, $rpe_bruch, $drusen, $chorio, $central_foveal_thickness;

    public $narratives = ['intro' => '', 'details' => ''];
    public $activeTab = 'anterior';

    protected $rules = [
        'eye' => 'string',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('OCT Nomenclature');

        if ($reservationId) {
            $this->reservationId = $reservationId;
            $this->loadBookingData();
        } elseif ($patient_id) {
            $this->patient_id = $patient_id;
            $this->patient = \App\Models\Administrative\medical_record::find($patient_id);
        }

        $this->generateNarrative();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updated($field)
    {
        $this->generateNarrative();
        $this->calculateCompletion();
    }

    public function calculateCompletion()
    {
        $fields = ['eye'];
        $this->completionPercentage = round(
            collect($fields)->filter(fn($f) => !empty($this->$f))->count() / count($fields) * 100
        );
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
        $eyeText = match (strtoupper($this->eye)) {
            'OD' => 'right eye (OD)',
            'OS' => 'left eye (OS)',
            'OD/OS' => 'both eyes (OD/OS)',
            default => 'eye (unspecified)',
        };

        $intro = "OCT examination was performed for {$eyeText}. Scan quality was adequate and centered properly.";

        $details = "OCT Anterior Segment:\n";
        $details .= "1. Epithelium: {$this->epithelium_integrity}, {$this->epithelium_thickness}, {$this->epithelium_regular}, {$this->epithelium_opacity}.\n";
        $details .= "2. Stroma: {$this->stroma_thickness_status}, {$this->stroma_opacity_location}, {$this->stroma_opacity_site}, Thickness {$this->stroma_opacity_value} µ.\n";
        $details .= "3. Central Corneal Thickness: {$this->cct_status}.\n";
        $details .= "4. Anterior chamber angle: Temporal {$this->angle_temporal} µm, Nasal {$this->angle_nasal} µm, Degree {$this->angle_degree}, Classification {$this->angle_classification}.\n\n";

        $details .= "OCT Macula:\n";
        $details .= "1. PVD: {$this->pvd}, VMT: {$this->vmt} ({$this->vmt_pattern}).\n";
        $details .= "2. ERM: {$this->erm}, Wrinkling: {$this->wrinkling}, Tangential traction: {$this->traction}, Detached: {$this->detachment}, Edema A-P traction: {$this->edema_ap}.\n";
        $details .= "3. Ocular Contour: {$this->ocular_contour}.\n";
        $details .= "4. Foveal Pit: {$this->foveal_pit}.\n\n";

        $details .= "Retinal Layers:\n";
        $details .= "1. Reflectivity: {$this->reflectivity}, Hyporeflective: {$this->hyporeflective_type}, Hyperreflective: {$this->hyperreflective_type}.\n";
        $details .= "2. Intra-retinal Edema: Location {$this->edema_location}, Shape {$this->edema_shape}.\n";
        $details .= "3. External Limiting Membrane: {$this->elm}.\n";
        $details .= "4. Ellipsoid Zone: {$this->ellipsoid_zone}.\n";
        $details .= "5. Neuro Sensory Retina: {$this->neurosensory_retina}.\n";
        $details .= "6. RPE Line: {$this->rpe_line}.\n";
        $details .= "7. RPE/Bruch’s membrane: {$this->rpe_bruch}, Drusen: {$this->drusen}, Chorio-capillaries: {$this->chorio}.\n";
        $details .= "8. Central Foveal Thickness: {$this->central_foveal_thickness}.\n\n";

        $details .= "Conclusion:\nDiagnosis: {$this->diagnosis}\nInterpretation: {$this->interpretation}.";

        $this->narratives['intro'] = $intro;
        $this->narratives['details'] = $details;
    }

    public function saveReport()
    {
        $this->validate();

        try {
            $doctorId = $this->booking?->medicalTeam->first()?->employee_id ?? null;

            OphthalmicSurgicalReport::updateOrCreate(
                ['booking_id' => $this->reservationId, 'report_type' => 'OCT Nomenclature'],
                [
                    'patient_id' => $this->patient_id,
                    'visit_id' => $this->booking->visit_id ?? null,
                    'doctor_id' => $doctorId,
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => $this->eye,
                    'surgical_procedure' => 'OCT Nomenclature',
                    'operative_details' => json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                    'preoperative_diagnosis' => $this->diagnosis,
                    'postoperative_diagnosis' => $this->interpretation,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ OCT nomenclature  Surgery Report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.medical.sheets.oct-nomenclature', [
            'age' => $this->patient?->age ?? 'N/A',
            'surgeon' => $this->booking?->medicalTeam->first()?->employee?->full_name ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}