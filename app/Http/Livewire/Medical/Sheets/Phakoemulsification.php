<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;
use App\Models\Medical\OphthalmicSurgicalReport;
use App\Models\Administrative\adm_b_surgery_bookings;

class Phakoemulsification extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $eye = '';
    public $anesthesia = '';
    public $incision_size = '';
    public $axis = '';
    public $staining = '';
    public $capsulorrhexis_tool = '';
    public $technique = '';
    public $iol_type = '';
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
        parent::mount('Phakoemulsification and IOL Insertion');
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
    // ✅ تحديد العين بالنص الصحيح
    $eyeText = match (strtoupper(trim($this->eye))) {
        'OD' => 'the right eye (OD)',
        'OS' => 'the left eye (OS)',
        'OD/OS' => 'both eyes (OD/OS)',
        default => 'the eye (unspecified)',
    };

    // ✅ البدء في تكوين النص الأساسي
    $details = "Phacoemulsification with intraocular lens (IOL) insertion was performed for {$eyeText} ";

    // ✅ التخدير
    if (!empty($this->anesthesia)) {
        $details .= "under {$this->anesthesia} anesthesia. ";
    } else {
        $details .= "under appropriate anesthesia. ";
    }

    // ✅ حجم الفتحة واتجاهها
    $details .= "A " . ($this->incision_size ?: '2.8 mm') . " clear corneal incision was made ";
    if (!empty($this->axis)) {
        $details .= "along the {$this->axis} axis. ";
    } else {
        $details .= "at the steepest corneal meridian. ";
    }

    // ✅ صبغ الكبسولة والأداة
    $details .= "The anterior capsule was stained with " . ($this->staining ?: 'trypan blue') . ", ";
    $details .= "and continuous curvilinear capsulorrhexis was performed using " . ($this->capsulorrhexis_tool ?: 'rhexis forceps') . ". ";

    // ✅ التقنية
    $details .= "Phacoemulsification was carried out using the " . ($this->technique ?: 'divide and conquer') . " technique. ";

    // ✅ العدسة
    $details .= "A " . ($this->iol_type ?: 'foldable') . " intraocular lens was implanted into the capsular bag. ";

    // ✅ إنهاء العملية
    $details .= "Wound hydration was performed, and subconjunctival steroid and antibiotic were administered. ";

    // ✅ إجراء إضافي (إن وجد)
    if (!empty($this->additional_procedure)) {
        $details .= "An additional procedure was performed: {$this->additional_procedure}. ";
    } else {
        $details .= "No additional procedures were required. ";
    }

    // ✅ المضاعفات
    if (!empty($this->complications) && strtolower($this->complications) !== 'none') {
        $details .= "Intraoperative complications included: {$this->complications}. ";
    } else {
        $details .= "No intraoperative complications were encountered. ";
    }

    // ✅ حفظ النص النهائي
    $this->narrative = trim($details);
}


    public function saveReport()
    {
        $this->validate();

        try {
            $doctorId = $this->booking->medicalTeam->first()->employee_id ?? null;
            OphthalmicSurgicalReport::updateOrCreate(
                ['booking_id' => $this->reservationId, 'report_type' => 'Phakoemulsification and IOL Insertion'],
                [
                    'patient_id' => $this->patient_id,
                    'visit_id' => $this->booking->visit_id ?? null,
                    'doctor_id' => $doctorId,
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => $this->eye,
                    'anesthesia' => $this->anesthesia,
                    'surgical_procedure' => 'Phakoemulsification with IOL insertion',
                    'operative_details' => $this->narrative,
                    'preoperative_diagnosis' => 'Cataract',
                    'postoperative_diagnosis' => 'Same',
                    'complications' => $this->complications,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ Phakoemulsification Report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.medical.sheets.phakoemulsification', [
            'age' => $this->patient->age ?? 'N/A',
            'surgeon' => $this->booking->medicalTeam->first()->employee->full_name ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}