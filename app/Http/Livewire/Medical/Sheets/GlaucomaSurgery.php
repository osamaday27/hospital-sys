<?php

namespace App\Http\Livewire\Medical\Sheets;

use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Medical\OphthalmicSurgicalReport;
use Livewire\Component;

class GlaucomaSurgery extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $eye = '', $surgeryType = '', $anesthesia = '', $surgeon = '';
    public $preDiagnosis = '', $postDiagnosis = 'The same', $mitomycin = '', $phakoSize = '';
    public $applications = '', $power = '', $duration = '', $approach = '';
    public $complications = 'None', $narratives = ['intro' => '', 'details' => ''];
    public $completionPercentage = 0;

    protected $rules = [
        'eye' => 'required|string',
        'surgeryType' => 'required|string',
        'anesthesia' => 'required|string',
    ];

    protected $messages = [
        'eye.required' => 'Eye selection is required',
        'surgeryType.required' => 'Surgery type is required',
        'anesthesia.required' => 'Anesthesia type is required',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('Glaucoma Surgery');

        $this->reservationId = $reservationId;
        if ($reservationId) {
            $this->loadBookingData();
        } elseif ($patient_id) {
            $this->patient_id = $patient_id;
            $this->patient = \App\Models\Administrative\medical_record::find($patient_id);
        }

        $this->loadExistingReport();

        if (empty($this->narratives['intro']) || empty($this->narratives['details'])) {
            $this->generateNarrative();
        }
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
        if (in_array($field, [
            'surgeryType', 'eye', 'anesthesia',
            'mitomycin', 'phakoSize', 'applications',
            'power', 'duration', 'approach'
        ])) {
            $this->generateNarrative();
        }

        $this->calculateCompletion();
    }

    public function generateNarrative()
    {
        $eyeText = match (strtoupper($this->eye)) {
            'OD' => 'right eye (OD)',
            'OS' => 'left eye (OS)',
            'OD/OS' => 'both eyes (OD/OS)',
            default => 'the eye',
        };

        $intro = "After the risks and benefits of the planned surgical intervention were explained to the patient, the patient was identified by the attending surgeon and brought to the operating theater. {$this->anesthesia} was administered, and the patient was monitored throughout the procedure.";

        $details = '';

        switch ($this->surgeryType) {
            case 'Subscleral Trabeculectomy':
                $details = "An eyelid speculum was applied to {$eyeText}. "
                    . "A localized limbal-based conjunctival flap was made with Westcott scissors. "
                    . "A triangular scleral flap was fashioned. "
                    . ($this->mitomycin === 'With Mitomycin C'
                        ? "Mitomycin C (0.2%) was applied to the scleral bed for 2 minutes and thoroughly irrigated with BSS. "
                        : "The scleral bed was irrigated with balanced salt solution (BSS) without Mitomycin C. ")
                    . "A temporal paracentesis was made using a 15° knife. "
                    . "The trabeculectomy block was excised, and a peripheral iridectomy was performed. "
                    . "Closure of the scleral flap was done using 10/0 nylon sutures. "
                    . "The conjunctiva was closed with 8/0 vicryl. "
                    . "A sterile patch was applied. The patient was transferred to recovery without complications.";
                break;

            case 'Phako + Trabeculectomy + IOL':
                $size = $this->phakoSize ?: '2.2';
                $details = "A localized limbal-based conjunctival periotomy was performed superiorly. "
                    . "A triangular partial-thickness scleral flap was fashioned superiorly. "
                    . "Mitomycin C (0.02%) was applied to the scleral bed for 2 minutes, then washed with BSS. "
                    . "A {$size} mm phaco-incision was performed along the 90° axis. "
                    . "Two paracentesis corneal stab wounds were created. "
                    . "Continuous circular capsulorrhexis was performed using a cystitome. "
                    . "Phacoemulsification was performed using a quick phaco-chop technique. "
                    . "Irrigation/aspiration of cortical material was performed using bimanual technique. "
                    . "A posterior chamber intraocular lens (IOL) was injected into the capsular bag. "
                    . "Closure of the scleral flap was done using 10/0 nylon sutures. "
                    . "Aspiration of viscoelastic was completed, wound hydration performed. "
                    . "Subconjunctival steroid/antibiotic/atropine injection was given. "
                    . "A sterile dressing was applied. The patient was brought to recovery with no complications.";
                break;

            case 'Ahmed Glaucoma Valve':
                $details = "A conjunctival periotomy was performed superotemporally. "
                    . "The superior and lateral recti muscles were hooked. "
                    . "The Ahmed Glaucoma Valve was conditioned and placed 8 mm from the limbus. "
                    . "A scleral tunnel was created, and the AGV tube was inserted into the anterior chamber. "
                    . "A scleral patch graft was placed over the tube. "
                    . "The conjunctiva was closed with 8/0 vicryl. "
                    . "Subconjunctival garamycin and decadron were applied. "
                    . "A sterile patch was applied. The patient tolerated the procedure well.";
                break;

            case 'Cyclophotocoagulation':
                $applications = $this->applications ?: '30';
                $power = $this->power ?: '2000';
                $duration = $this->duration ?: '2000';
                $details = "The glaucoma probe was applied to the limbus in 360° for {$applications} applications "
                    . "at a power setting of {$power} mW with duration {$duration} msec. "
                    . "Treatment was uneventful. Topical ointment was applied to the eye with a sterile patch. "
                    . "The patient went to the recovery area with no complications.";
                break;

            case 'Trabeculotomy':
                $details = $this->approach === 'Ab Interno'
                    ? "A clear corneal incision was made. Viscoelastic was injected. "
                        . "The trabeculotome was introduced into Schlemm’s canal and rotated, tearing the trabecular meshwork. "
                        . "The anterior chamber was reformed and the wound sealed. The patient tolerated the procedure well."
                    : "A conjunctival and partial-thickness scleral flap were created. "
                        . "Schlemm’s canal was deroofed, and the trabeculotome was inserted and rotated to open the trabecular meshwork. "
                        . "The conjunctiva was sutured. The patient tolerated the procedure well.";
                break;

            case 'Goniotomy':
                $details = "A clear corneal incision was made. Viscoelastic was injected. "
                    . "A goniolens was placed to visualize the angle. "
                    . "A goniotomy knife was used to incise the trabecular meshwork over 120°. "
                    . "The anterior chamber was irrigated and reformed. The wound was sealed. The patient tolerated the procedure well.";
                break;

            case 'Deep Sclerectomy':
                $details = "A fornix-based or limbus-based conjunctival flap was created. "
                    . "A partial-thickness scleral flap was created, followed by a deep dissection to expose Descemet’s membrane. "
                    . "Schlemm’s canal was deroofed, and a collagen implant was placed. "
                    . "The flap was loosely sutured, and the conjunctiva was closed. "
                    . "The anterior chamber was reformed. The patient tolerated the procedure well.";
                break;

            case 'Non-Penetrating Glaucoma Surgery (Viscocanalostomy)':
                $details = "A conjunctival flap was created, and the sclera was exposed. "
                    . "A partial-thickness scleral flap was dissected, followed by a deeper dissection to open Schlemm’s canal. "
                    . "Viscoelastic was injected to enhance aqueous outflow. "
                    . "The flaps were sutured. The anterior chamber was reformed. The patient tolerated the procedure well.";
                break;
        }

        $this->narratives['intro'] = $intro;
        $this->narratives['details'] = $details;
    }

    public function saveReport()
    {
        $this->validate();
                $doctorId = $this->booking?->medicalTeam->first()?->employee_id ?? null;

        try {
            OphthalmicSurgicalReport::updateOrCreate(
                [
                    'booking_id' => $this->reservationId,
                    'report_type' => 'Glaucoma Surgery',
                ],
                [
                    'patient_id' => $this->patient_id,
                      'doctor_id' => $doctorId ?? null, // ✅ هنا التعديل المهم
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => $this->eye,
                    'anesthesia' => $this->anesthesia,
                    'surgical_procedure' => $this->surgeryType,
                    'operative_details' => json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                    'complications' => $this->complications,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ Glaucoma Surgery Report saved successfully');
            $this->dispatchBrowserEvent('report-saved');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function loadExistingReport()
    {
        if (!$this->reservationId) return;

        $report = OphthalmicSurgicalReport::where('booking_id', $this->reservationId)
            ->where('report_type', 'Glaucoma Surgery')
            ->first();

        if ($report) {
            $this->eye = $report->eye;
            $this->anesthesia = $report->anesthesia;
            $this->surgeryType = $report->surgical_procedure;
            $details = json_decode($report->operative_details, true);
            if (is_array($details)) {
                $this->narratives = $details;
            }
        }
    }

    public function calculateCompletion()
    {
        $fields = ['eye', 'anesthesia', 'surgeryType'];
        $completed = collect($fields)->filter(fn($f) => !empty($this->$f))->count();
        $this->completionPercentage = round(($completed / count($fields)) * 100);
    }

    public function printReport()
    {
        $this->dispatchBrowserEvent('print-report');
    }

    public function render()
    {
        return view('livewire.medical.sheets.glaucoma-surgery', [
            'age' => $this->patient?->age ?? 'N/A',
            'surgeon' => auth()->user()->name ?? 'N/A',
        ]);
    }
}