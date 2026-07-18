<?php

namespace App\Http\Livewire\Medical\Sheets;

use App\Models\Medical\OphthalmicSurgicalReport;
use App\Models\Administrative\adm_b_surgery_bookings;

class Keratoplasty extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $eye = '';
    public $anesthesia = 'General anesthesia';
    public $surgeryType = '';
    public $preOpDiagnosis = '';
    public $postOpDiagnosis = 'The same';
    public $graftType = '';
    public $trephineSize = '';
    public $recipientBedSize = '';
    public $suturingType = '';
    public $complications = 'None';
    public $narratives = ['intro' => '', 'details' => ''];
    public $completionPercentage = 0;

    protected $rules = [
        'eye' => 'required|string',
        'surgeryType' => 'required|string',
        'anesthesia' => 'required|string',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('Keratoplasty Surgery');

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
        $this->booking = adm_b_surgery_bookings::with(['patient', 'medicalTeam.employee'])->find($this->reservationId);

        if (!$this->booking) {
            session()->flash('error', 'Booking not found');
            return;
        }

        $this->patient = $this->booking->patient;
        $this->patient_id = $this->patient->id ?? null;
    }

    public function updated($field)
    {
        if (in_array($field, ['eye', 'surgeryType', 'graftType', 'trephineSize', 'recipientBedSize', 'suturingType'])) {
            $this->generateNarrative();
        }

        $this->calculateCompletion();
    }

    public function calculateCompletion()
    {
        $fields = ['eye', 'surgeryType', 'anesthesia'];
        $completed = collect($fields)->filter(fn($f) => !empty($this->$f))->count();
        $this->completionPercentage = round(($completed / count($fields)) * 100);
    }

    public function generateNarrative()
    {
        $eyeText = match (strtoupper($this->eye)) {
            'OD' => 'right eye (OD)',
            'OS' => 'left eye (OS)',
            'OD/OS' => 'both eyes (OD/OS)',
            default => 'unspecified eye',
        };

        $intro = "After explaining the risks and benefits of the planned surgical intervention, the patient was identified by the attending surgeon and brought to the operating theater. {$this->anesthesia} was administered, and the patient was monitored.";

        $details = '';

        switch ($this->surgeryType) {
            case 'Penetrating Keratoplasty (PKP)':
                $trephine = $this->trephineSize ?: '7.5';
                $graft = $this->graftType ?: 'full-thickness donor cornea';
                $suture = $this->suturingType ?: 'interrupted 10/0 nylon';
                $details = "A lid speculum was applied to the {$eyeText}. "
                    . "A trephine of {$trephine} mm was used to trephine the host cornea. "
                    . "The anterior chamber was entered with a keratome, and the cornea was excised. "
                    . "The donor button of {$graft} was prepared using a {$trephine} mm trephine. "
                    . "The graft was sutured to the host bed using {$suture} sutures. "
                    . "The anterior chamber was reformed, wounds were inspected and found watertight. "
                    . "Subconjunctival antibiotic and steroid injections were administered. "
                    . "The patient tolerated the procedure well.";
                break;

            case 'Deep Anterior Lamellar Keratoplasty (DALK)':
                $trephine = $this->trephineSize ?: '8';
                $suture = $this->suturingType ?: 'interrupted 10/0 nylon';
                $details = "A lid speculum was applied to the {$eyeText}. "
                    . "Partial trephination was performed using a {$trephine} mm trephine. "
                    . "A deep lamellar dissection was carried out until Descemet’s membrane was reached. "
                    . "The donor corneal button was prepared by removing the endothelium. "
                    . "The donor button was sutured to the host bed using {$suture} sutures. "
                    . "Subconjunctival antibiotic and steroid injections were administered. "
                    . "The patient tolerated the procedure well.";
                break;

            case 'Descemet Membrane Endothelial Keratoplasty (DMEK)':
                $bed = $this->recipientBedSize ?: '8';
                $details = "A lid speculum was applied to the {$eyeText}. "
                    . "A Descemetorhexis of {$bed} mm was performed. "
                    . "The donor Descemet membrane was stained with trypan blue and injected into the anterior chamber. "
                    . "The graft was unfolded and attached to the posterior stroma using an air bubble. "
                    . "The anterior chamber was filled with air to secure graft adherence. "
                    . "The patient tolerated the procedure well.";
                break;

            case 'Descemet Stripping Endothelial Keratoplasty (DSEK)':
                $bed = $this->recipientBedSize ?: '8';
                $details = "A lid speculum was applied to the {$eyeText}. "
                    . "Descemet’s membrane was stripped over {$bed} mm. "
                    . "The donor lamella was prepared and inserted using the Busin glide. "
                    . "The graft was attached with an air bubble and left for 10 minutes. "
                    . "The anterior chamber was reformed with BSS. "
                    . "The patient tolerated the procedure well.";
                break;

            case 'Boston Keratoprosthesis (KPro)':
                $trephine = $this->trephineSize ?: '8';
                $suture = $this->suturingType ?: 'interrupted 10/0 nylon';
                $details = "A lid speculum was applied to the {$eyeText}. "
                    . "Host corneal trephination was performed using a {$trephine} mm trephine. "
                    . "The Boston type I keratoprosthesis was assembled and secured between the donor button and backplate. "
                    . "The complex was sutured to the host cornea using {$suture} sutures. "
                    . "A bandage contact lens was applied. "
                    . "The patient tolerated the procedure well.";
                break;

            default:
                $details = "The {$this->surgeryType} was performed successfully on the {$eyeText}. "
                    . "The patient tolerated the procedure well.";
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
                [
                    'booking_id' => $this->reservationId,
                    'report_type' => 'Keratoplasty Surgery',
                ],
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
                    'complications' => $this->complications,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ Keratoplasty Surgery Report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.medical.sheets.keratoplasty', [
            'age' => $this->patient?->age ?? 'N/A',
            'surgeon' => $this->booking?->medicalTeam->first()?->employee?->full_name ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}