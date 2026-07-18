<?php

namespace App\Http\Livewire\Medical\Sheets;

use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Medical\OphthalmicSurgicalReport;
use Livewire\Component;

class CornealCrossLinking extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $narratives = ['intro' => '', 'details' => ''];

    public $preOpDiagnosis = 'Keratoconus OD/OS';
    public $postOpDiagnosis = 'The same';
    public $surgicalProcedure = 'Corneal Cross Linking OD/OS';
    public $anesthesiaType = 'Topical anesthesia';
    public $dropDuration = '';
    public $uvDuration = '';
    public $contactLens = '';
    public $postOpMed = '';
    public $complications = 'None';
    public $completionPercentage = 0;

    protected $rules = [
        'preOpDiagnosis' => 'required|string',
        'surgicalProcedure' => 'required|string',
        'anesthesiaType' => 'required|string',
    ];

    protected $listeners = ['updateNarrativeText' => 'updateNarrativeText'];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('Corneal Cross Linking');

        if ($reservationId) {
            $this->reservationId = $reservationId;
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

    public function loadExistingReport()
    {
        if (!$this->reservationId) return;

        $report = OphthalmicSurgicalReport::where('booking_id', $this->reservationId)
            ->where('report_type', 'Corneal Cross Linking')
            ->first();

        if ($report) {
            $this->preOpDiagnosis = $report->preoperative_diagnosis;
            $this->postOpDiagnosis = $report->postoperative_diagnosis;
            $this->surgicalProcedure = $report->surgical_procedure;
            $this->anesthesiaType = $report->anesthesia;
            $this->narratives = json_decode($report->operative_details, true) ?? $this->narratives;
            $this->calculateCompletion();
        }
    }
public function loadBookingData()
{
    // ✅ تحميل بيانات الحجز والربط مع المريض
    $this->booking = \App\Models\Administrative\adm_b_surgery_bookings::with([
        'patient',
        'room',
        'services',
        'medicalTeam.employee'
    ])->find($this->reservationId);

    if (!$this->booking) {
        session()->flash('error', 'Booking not found');
        return;
    }

    $this->patient = $this->booking->patient;
    $this->patient_id = $this->patient->id ?? null;

    // تحميل الفريق الطبي والخدمات لو حبيت تستخدمهم
    $this->medicalTeam = $this->booking->medicalTeam->map(fn($m) => [
        'name' => $m->employee->full_name ?? '---',
        'role' => $m->role,
        'status' => $m->status,
    ])->toArray();

    $this->surgicalServices = $this->booking->services->map(fn($s) => [
        'name_ar' => $s->getTranslation('name', 'ar'),
        'name_en' => $s->getTranslation('name', 'en'),
        'side' => $s->pivot->side ?? '',
    ])->toArray();

    // ✅ تحديث النسبة والبيانات
    $this->calculateCompletion();
}

 public function generateNarrative()
{
    // 🩺 تحديد نوع العين حسب الإجراء
    $eyeText = match (strtolower($this->contactLens)) {
        'right' => 'the right eye (OD)',
        'left' => 'the left eye (OS)',
        'both', 'od/os', 'od and os' => 'both eyes (OD/OS)',
        default => 'the eye',
    };

    // 💉 النص التمهيدي (Intro)
    $this->narratives['intro'] = "After the risks and benefits of the planned surgical intervention were explained to the patient, the patient was identified by the attending surgeon and brought to the operating theater. {$this->anesthesiaType} was induced and the patient was monitored throughout the procedure.\n\nThe corneal epithelium was carefully removed (epithelial debridement) to expose Bowman’s layer in {$eyeText}.";

    // 🔬 النص التفصيلي (Details)
    $dropDuration = $this->dropDuration ?: '15';
    $uvDuration = $this->uvDuration ?: '15';
    $contactLens = $this->contactLens ?: 'right';
    $postOpMed = $this->postOpMed ?: 'steroid/antibiotic';

    $this->narratives['details'] = "Riboflavin (Vitamin B2) solution in heavy molecular weight dextran was applied to the corneal surface of {$eyeText} for approximately {$dropDuration} minutes to achieve stromal saturation.\n\nFollowing adequate absorption, ultraviolet-A (UVA) light irradiation was applied for {$uvDuration} minutes at standard intensity. A bandage contact lens was then placed over {$eyeText}. Postoperatively, topical {$postOpMed} drops were administered.\n\nThe patient tolerated the procedure well and was transferred to the recovery area in good condition with no complications noted.";
}

    public function updateNarrativeText($section, $newText)
    {
        $this->narratives[$section] = $newText;
        $this->dispatchBrowserEvent('report-saved');
    }

    public function updated($property)
    {
        if (in_array($property, ['dropDuration', 'uvDuration', 'contactLens', 'postOpMed'])) {
            $this->generateNarrative();
        }
        $this->calculateCompletion();
    }

    public function calculateCompletion()
    {
        $fields = ['preOpDiagnosis', 'surgicalProcedure', 'anesthesiaType'];
        $completed = collect($fields)->filter(fn($f) => !empty($this->$f))->count();
        $this->completionPercentage = round(($completed / count($fields)) * 100);
    }

    public function saveReport()
    {
        $this->validate();
        $doctorId = $this->booking?->medicalTeam->first()?->employee_id ?? null;
        try {
            OphthalmicSurgicalReport::updateOrCreate(
                [
                    'booking_id' => $this->reservationId,
                    'report_type' => 'Corneal Cross Linking',
                ],
                [
                    'patient_id' => $this->patient_id,
                    'visit_id' => $this->booking->visit_id ?? null,
                  'doctor_id' => $doctorId ?? null, // ✅ هنا التعديل المهم
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => 'OD/OS',
                    'anesthesia' => $this->anesthesiaType,
                    'surgeon' => auth()->user()->name ?? 'N/A',
                    'preoperative_diagnosis' => $this->preOpDiagnosis,
                    'postoperative_diagnosis' => $this->postOpDiagnosis,
                    'surgical_procedure' => $this->surgicalProcedure,
                    'operative_details' => json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                    'complications' => 'None',
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ Corneal Cross Linking report saved successfully');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
        }
    }

    public function printReport() { $this->dispatchBrowserEvent('print-report'); }

    public function render()
    {
        return view('livewire.medical.sheets.corneal-cross-linking', [
            'age' => $this->patient?->age ?? 'N/A',
            'surgeon' => auth()->user()->name ?? 'N/A',
        ]);
    }
}