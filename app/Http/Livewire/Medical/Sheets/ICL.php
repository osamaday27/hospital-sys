<?php

namespace App\Http\Livewire\Medical\Sheets;

use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Administrative\adm_b_surgery_supply_issue;
use App\Models\Medical\mbcomplaintshispat;
use App\Models\Medical\mbdiagnoseshispat;
use App\Models\Medical\OphthalmicSurgicalReport;
use Livewire\Component;

class Icl extends BaseMedicalSheet
{
    public $patient_id, $patient, $reservationId, $booking;
    public $narratives = ['intro' => '', 'details' => ''];

    public $preOpDiagnosis = '';
    public $postOpDiagnosis = 'The same';
    public $surgicalProcedure = 'OD';
    public $anesthesiaType = '';
    public $surgeryType = '';
    public $complications = 'None';
    public $complicationType = '';
    public $incisionSize = '2.8';
    public $iclType = 'STAAR ICL';
    public $iridectomy = 'yes';

    public $completionPercentage = 0;
    public $complaints = [];
    public $diagnoses = [];
    public $supplyIssues = [];
    public $medicalTeam = [];
    public $surgicalServices = [];

    protected $listeners = ['updateNarrativeText' => 'updateNarrativeText'];

    protected $rules = [
        'preOpDiagnosis' => 'required|string',
        'surgicalProcedure' => 'required|in:OD,OS',
        'anesthesiaType' => 'required|string',
        'surgeryType' => 'required|string',
    ];

    protected $messages = [
        'preOpDiagnosis.required' => 'Preoperative diagnosis is required',
        'surgicalProcedure.required' => 'Surgical procedure is required',
        'anesthesiaType.required' => 'Anesthesia type is required',
        'surgeryType.required' => 'Surgery type is required',
    ];

    public function mount($sheet_type = null, $patient_id = null, $reservationId = null)
    {
        parent::mount('ICL');
        
        if ($reservationId) {
            $this->reservationId = $reservationId;
            $this->loadBookingData();
        } elseif ($patient_id) {
            $this->patient_id = $patient_id;
            $this->patient = \App\Models\Administrative\medical_record::find($patient_id);
        }
        
        // Load existing report if available
        $this->loadExistingReport();
        
        // Generate narrative if it's a new report
        if (empty($this->narratives['intro']) || empty($this->narratives['details'])) {
            $this->generateNarrative();
        }
    }

    public function loadExistingReport()
    {
        if (!$this->reservationId) return;
        
        $report = OphthalmicSurgicalReport::where('booking_id', $this->reservationId)
            ->where('report_type', 'ICL implantation')
            ->first();
        
        if ($report) {
            $this->preOpDiagnosis = $report->preoperative_diagnosis;
            $this->postOpDiagnosis = $report->postoperative_diagnosis;
            $this->surgicalProcedure = $report->eye;
            $this->anesthesiaType = $report->anesthesia;
            $this->surgeryType = $report->surgical_procedure;
            $this->complications = $report->complications === 'None' ? 'None' : 'Yes';
            $this->complicationType = $report->complications !== 'None' ? $report->complications : '';
            $this->iclType = $report->materials_used;
            $this->incisionSize = $report->instrumentation;
            $this->iridectomy = $report->iridectomy_type;
            
            // Load narrative texts
            $details = json_decode($report->operative_details, true);
            if ($details && is_array($details)) {
                $this->narratives['intro'] = $details['intro'] ?? '';
                $this->narratives['details'] = $details['details'] ?? '';
            }
            
            $this->calculateCompletion();
        }
    }

    // 🔥 الدالة الجديدة لتوليد الـ Narrative تلقائياً
    public function generateNarrative()
    {
        $eye = $this->surgicalProcedure === 'OD' ? 'right' : 'left';
        $eyeUpper = ucfirst($eye);
        
        // Anesthesia text
        $anesthesiaText = $this->anesthesiaType ?: 'anesthesia';
        
        // ✅ Introductory Statement
        $this->narratives['intro'] = "After the risks and benefits of the planned surgical interventions were explained to the patient, the patient was identified by the attending surgeon and was brought to the operating theater. {$anesthesiaText} was induced and the patient was monitored. The {$eye} eye was prepped and dressed in the usual fashion.\n\nAn eyelid speculum was applied to the {$eye} eye.";
        
        // ✅ Operative Details
        $iridectomyText = $this->iridectomy === 'yes' 
            ? ' A superior iridectomy was made at 12 O\'clock.' 
            : '';
        
        $this->narratives['details'] = "A {$this->incisionSize} mm keratome incision was made at 12 O'clock, with 2 paracentesis incisions at 10 and 2 O'clock. The {$this->iclType} was injected in the pupillary plane. The ICL was positioned behind the iris plane.{$iridectomyText} Irrigation/aspiration of the healon was done. Irrigation of the wounds was performed.\n\nAn eye patch was applied to the eye. Reversal of anesthesia was induced. The patient was brought to the preoperative area with no complications.";
    }

    public function updateNarrativeText($section, $newText)
    {
        $this->narratives[$section] = $newText;
        $this->dispatchBrowserEvent('report-saved');
    }

    // 🔥 التحديث التلقائي عند تغيير أي حقل
    public function updated($propertyName)
    {
        // Auto-regenerate narrative when key fields change
        if (in_array($propertyName, [
            'surgicalProcedure', 
            'anesthesiaType', 
            'incisionSize', 
            'iclType', 
            'iridectomy'
        ])) {
            $this->generateNarrative();
        }
        
        // Auto-set postoperative diagnosis
        if ($propertyName === 'preOpDiagnosis') {
            $this->postOpDiagnosis = 'The same';
        }
        
        $this->calculateCompletion();
    }

    public function calculateCompletion()
    {
        $fields = ['preOpDiagnosis', 'anesthesiaType', 'surgeryType', 'surgicalProcedure', 'incisionSize', 'iclType'];
        $completed = collect($fields)->filter(fn($f) => !empty($this->$f))->count();
        $this->completionPercentage = round(($completed / count($fields)) * 100);
    }

    public function loadBookingData()
    {
        $this->booking = adm_b_surgery_bookings::with([
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
        $this->patient_id = $this->patient->id;
        $this->loadMedicalData();
        $this->loadSupplies();
        $this->loadTeamAndServices();
    }

    public function loadMedicalData()
    {
        if (!$this->patient_id) return;
        $visitId = $this->booking->visit_id ?? null;

        $this->complaints = mbcomplaintshispat::where('id_patient', $this->patient_id)
            ->where('id_visit', $visitId)
            ->latest()
            ->get()
            ->toArray();

        $this->diagnoses = mbdiagnoseshispat::where('id_patient', $this->patient_id)
            ->where('id_visit', $visitId)
            ->latest()
            ->get()
            ->toArray();
    }

    public function loadSupplies()
    {
        if (!$this->reservationId) return;
        
        $this->supplyIssues = adm_b_surgery_supply_issue::where('surgery_booking_id', $this->reservationId)
            ->with(['items.product', 'items.unit'])
            ->get()
            ->toArray();
    }

    public function loadTeamAndServices()
    {
        if (!$this->booking) return;

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
    }

    public function saveReport()
    {
        $this->validate();
        $doctorId = $this->booking?->medicalTeam->first()?->employee_id ?? null;

        try {
            $report = OphthalmicSurgicalReport::updateOrCreate(
                [
                    'booking_id' => $this->reservationId,
                    'report_type' => 'ICL implantation',
                ],
                [
                    'patient_id' => $this->patient_id,
                    'visit_id' => $this->booking->visit_id ?? null,
                      'doctor_id' => $doctorId ?? null, // ✅ هنا التعديل المهم
                    'surgery_date' => $this->booking->booking_date ?? now(),
                    'eye' => $this->surgicalProcedure,
                    'anesthesia' => $this->anesthesiaType,
                    'surgeon' => $this->medicalTeam[0]['name'] ?? auth()->user()->name ?? 'N/A',
                    'preoperative_diagnosis' => $this->preOpDiagnosis,
                    'postoperative_diagnosis' => $this->postOpDiagnosis,
                    'surgical_procedure' => $this->surgeryType,
                    'operative_details' => json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                    'complications' => $this->complications === 'Yes' ? $this->complicationType : 'None',
                    'materials_used' => $this->iclType,
                    'instrumentation' => $this->incisionSize,
                    'iridectomy_type' => $this->iridectomy,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]
            );

            session()->flash('success', '✅ ICL Operative Report saved successfully');
            $this->dispatchBrowserEvent('report-saved', ['message' => 'Report saved successfully!']);
            
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Save failed: ' . $e->getMessage());
            \Log::error('Save ICL Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'patient_id' => $this->patient_id,
                    'booking_id' => $this->reservationId,
                ]
            ]);
        }
    }

    public function autoSave()
    {
        // Silent auto-save without validation
        try {
            if ($this->reservationId && $this->patient_id) {
                OphthalmicSurgicalReport::updateOrCreate(
                    [
                        'booking_id' => $this->reservationId,
                        'report_type' => 'ICL implantation',
                    ],
                    [
                        'patient_id' => $this->patient_id,
                        'operative_details' => json_encode($this->narratives, JSON_UNESCAPED_UNICODE),
                        'eye' => $this->surgicalProcedure,
                        'materials_used' => $this->iclType,
                        'instrumentation' => $this->incisionSize,
                        'updated_at' => now(),
                    ]
                );
                
                $this->dispatchBrowserEvent('auto-saved');
            }
        } catch (\Exception $e) {
            // Silent fail
        }
    }

    public function refreshData()
    {
        $this->loadBookingData();
        $this->generateNarrative();
        session()->flash('info', '🔄 Data refreshed successfully');
    }

    public function exportPdf()
    {
        if (!$this->reservationId) {
            session()->flash('error', 'No booking selected');
            return;
        }
        return redirect()->route('medical.reports.icl.export-pdf', ['bookingId' => $this->reservationId]);
    }

    public function printReport()
    {
        $this->dispatchBrowserEvent('print-report');
    }

    public function render()
    {
        return view('livewire.medical.sheets.icl', [
            'age' => $this->patient?->age ?? 'N/A',
            'surgeon' => $this->medicalTeam[0]['name'] ?? auth()->user()->name ?? 'N/A',
        ]);
    }
}