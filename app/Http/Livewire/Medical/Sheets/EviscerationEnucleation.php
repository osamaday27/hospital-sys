<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;
use App\Models\Medical\OphthalmicSurgicalReport;
use App\Models\Administrative\adm_b_surgery_bookings;

class EviscerationEnucleation extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $eye = '';
    public $anesthesia = 'General anesthesia';
    public $surgeryType = '';
    public $preOpDiagnosis = '';
    public $postOpDiagnosis = 'The same';
    public $graftType = '';
    public $narratives = ['intro' => '', 'details' => ''];
    public $completionPercentage = 0;

    protected $rules = [
        'eye' => 'required|string',
        'surgeryType' => 'required|string',
        'anesthesia' => 'required|string',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('Evisceration / Enucleation / Exenteration');
        if ($reservationId) {
            $this->reservationId = $reservationId;
            $this->loadBookingData();
        } elseif ($patient_id) {
            $this->patient_id = $patient_id;
            $this->patient = \App\Models\Administrative\medical_record::find($patient_id);
        }
        $this->generateNarrative();
    }

    public function loadBookingData()
    {
        $this->booking = adm_b_surgery_bookings::with(['patient','medicalTeam.employee'])->find($this->reservationId);
        if (!$this->booking) {
            session()->flash('error','Booking not found');
            return;
        }
        $this->patient = $this->booking->patient;
        $this->patient_id = $this->patient->id ?? null;
    }

    public function updated($field)
    {
        if (in_array($field, ['eye','surgeryType','graftType'])) {
            $this->generateNarrative();
        }
        $this->calculateCompletion();
    }

    public function calculateCompletion()
    {
        $fields = ['eye','surgeryType','anesthesia'];
        $this->completionPercentage = round(collect($fields)->filter(fn($f)=>!empty($this->$f))->count()/count($fields)*100);
    }

    public function generateNarrative()
    {
        $eyeText = match(strtoupper($this->eye)) {
            'OD' => 'right eye (OD)',
            'OS' => 'left eye (OS)',
            'OD/OS' => 'both eyes (OD/OS)',
            default => 'eye (unspecified)',
        };

        $intro = "After the risks and benefits of the planned surgical interventions were explained to the patient, the patient was identified by the attending surgeon and was brought to the operating theater. {$this->anesthesia} was induced and the patient was monitored.";

        $details = '';

        switch ($this->surgeryType) {
            case 'Evisceration':
                $details = "A speculum was applied to the {$eyeText}. Conjunctival periotomy was made using Westcott scissors. The conjunctival adhesions were dissected. Excision of the cornea from the limbus was performed. Evisceration of the phthisical eye contents was performed. The sclera was bisected to accommodate the orbital implant. A biointegrated orbital implant was placed in the socket. The sclera and conjunctiva were closed in layers using 6/0-7/0 vicryl sutures. A conformer was placed and a sterile dressing was applied. The patient was transferred to recovery with no complications.";
                break;

            case 'Enucleation':
                $details = "A speculum was applied to the {$eyeText}. Conjunctival periotomy was made using Westcott scissors. The adhesions were dissected. The four rectus muscles were hooked, sutured, and severed. The oblique muscles were cut, and the optic nerve was transected. The globe was removed. A biointegrated orbital implant was placed. Tenon’s and conjunctiva were closed in layers using vicryl sutures. A conformer and sterile dressing were applied. The patient tolerated the procedure well.";
                break;

            case 'Socket Reconstruction':
                $graft = match($this->graftType) {
                    'Dermis/Fat graft' => 'Dermis/Fat graft harvested from the iliac region was applied to the contracted conjunctiva.',
                    'Scleral patch graft' => 'Scleral patch graft was applied as an expander to the conjunctiva.',
                    'Amniotic membrane graft' => 'Amniotic membrane graft was applied to the conjunctival contracted socket as an expander.',
                    default => '',
                };
                $details = "A speculum was applied to the {$eyeText}. Conjunctival periotomy was made using Westcott scissors, and adhesions were dissected. {$graft} The conjunctiva was closed in layers with 6/0-7/0 vicryl sutures. A conformer and sterile dressing were applied. The patient was brought to recovery with no complications.";
                break;

            case 'Orbital Exenteration':
                $details = "The eyelids were closed with 4/0 silk sutures. A 360° myocutaneous flap was fashioned 4 mm from eyelid margin and extended beyond the orbital margin. Extraperiosteal dissection and removal of orbital contents were performed till the apex. Hemostasis achieved with diathermy. The flap was closed in layers with vicryl and silk sutures. A pressure dressing was applied to the socket.";
                break;
        }

        $this->narratives['intro'] = trim($intro);
        $this->narratives['details'] = trim($details);
    }

    public function saveReport()
    {
        $this->validate();

        try {
            $doctorId = $this->booking?->medicalTeam->first()?->employee_id ?? null;

            OphthalmicSurgicalReport::updateOrCreate(
                ['booking_id'=>$this->reservationId,'report_type'=>'Evisceration/Enucleation/Exenteration'],
                [
                    'patient_id'=>$this->patient_id,
                    'visit_id'=>$this->booking->visit_id ?? null,
                    'doctor_id'=>$doctorId,
                    'surgery_date'=>$this->booking->booking_date ?? now(),
                    'eye'=>$this->eye,
                    'anesthesia'=>$this->anesthesia,
                    'surgeon'=>$this->booking?->medicalTeam->first()?->employee?->full_name ?? auth()->user()->name ?? 'N/A',
                    'preoperative_diagnosis'=>$this->preOpDiagnosis ?: '---',
                    'postoperative_diagnosis'=>$this->postOpDiagnosis,
                    'surgical_procedure'=>$this->surgeryType,
                    'operative_details'=>json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                    'complications'=>'None',
                    'status'=>'active',
                    'created_by'=>auth()->id(),
                ]
            );

            session()->flash('success','✅ Report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error','❌ Save failed: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.medical.sheets.evisceration-enucleation', [
            'age'=>$this->patient?->age ?? 'N/A',
            'surgeon'=>$this->booking?->medicalTeam->first()?->employee?->full_name ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}