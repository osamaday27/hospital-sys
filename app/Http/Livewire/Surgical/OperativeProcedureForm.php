<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Medical\OperativeProcedureReport;
use App\Models\Administrative\Adm_b_surgery_bookings;
use App\Models\Administrative\Surgery_Medical_Team;
use App\Models\Hr\Employee;
use Illuminate\Support\Facades\Auth;

class OperativeProcedureForm extends Component
{
    public $patient_id;
    public $visit_id;
    public $booking_id;

    // 🔹 الفريق الطبي
    public $surgeon_id;
    public $first_assistant_id;
    public $anesthesiologist_id;
    public $surgeon_signature;
    public $assistant_signature;
    public $anesthesiologist_signature;

    public $surgeons = [];
    public $assistants = [];
    public $anesthesiologists = [];

    // 🔹 باقي الحقول
    public $preoperative_diagnosis;
    public $postoperative_diagnosis;
    public $time_started;
    public $time_ended;
    public $anesthesia_type;
    public $anesthesia_other;
    public $procedures_done;
    public $operation_details;
    public $blood_loss_ml;
    public $blood_transfused_ml;
    public $complication_occurred = false;
    public $complication_details;
    public $specimens_sent = false;
    public $specimen_no;
    public $specimen_origin;
    public $specimen_size_description;
    public $specimen_sent_to_lab = false;
    public $specimen_pathology = false;
    public $specimen_culture = false;
    public $implantable_devices = false;
    public $implant_details;
    public $report_date;
    public $report_time;

    protected $rules = [
        'preoperative_diagnosis' => 'nullable|string',
        'postoperative_diagnosis' => 'nullable|string',
        'time_started' => 'nullable',
        'time_ended' => 'nullable',
        'anesthesia_type' => 'nullable|string',
        'procedures_done' => 'nullable|string',
        'operation_details' => 'nullable|string',
        'surgeon_id' => 'nullable|integer|exists:employees,id',
        'first_assistant_id' => 'nullable|integer|exists:employees,id',
        'anesthesiologist_id' => 'nullable|integer|exists:employees,id',
    ];

    public function mount($patient_id, $visit_id = null, $booking_id = null)
    {
        $this->patient_id = $patient_id;
        $this->visit_id = $visit_id;
        $this->booking_id = $booking_id;
        $this->report_date = now()->toDateString();
        $this->report_time = now()->format('H:i');

        // 🩺 تحميل الفريق الطبي من جدول الحجز
        $this->loadMedicalTeam();

        // 🧠 تحميل القوائم
        $this->loadSurgeons();
        $this->loadAssistants();
        $this->loadAnesthesiologists();

        // ✅ تحديد الافتراضيات لو مش متسجلين
        if (!$this->surgeon_id && $this->surgeons->isNotEmpty()) {
            $this->surgeon_id = $this->surgeons->first()->id;
        }
        if (!$this->first_assistant_id && $this->assistants->isNotEmpty()) {
            $this->first_assistant_id = $this->assistants->first()->id;
        }
        if (!$this->anesthesiologist_id && $this->anesthesiologists->isNotEmpty()) {
            $this->anesthesiologist_id = $this->anesthesiologists->first()->id;
        }
    }

    // 🩺 تحميل الفريق الطبي الافتراضي
    protected function loadMedicalTeam()
    {
        $team = Surgery_Medical_Team::with('medical_team')
            ->where('surgery_bookings_id', $this->booking_id)
            ->get();

        foreach ($team as $member) {
            switch ($member->role) {
                case 'surgeon':
                    $this->surgeon_id = $member->medical_team_id;
                    $this->surgeon_signature = $member->medical_team->full_name ?? '';
                    break;
                case 'assistant':
                    $this->first_assistant_id = $member->medical_team_id;
                    $this->assistant_signature = $member->medical_team->full_name ?? '';
                    break;
                case 'anesthesiologist':
                    $this->anesthesiologist_id = $member->medical_team_id;
                    $this->anesthesiologist_signature = $member->medical_team->full_name ?? '';
                    break;
            }
        }
    }

    // 🧑‍⚕️ تحميل القوائم
    protected function loadSurgeons()
    {
        $this->surgeons = Employee::where('employee_type', 'physician')
            ->select('id', 'full_name', 'employee_code')
            ->limit(50)
            ->get();
    }

    protected function loadAssistants()
    {
        $this->assistants = Employee::whereIn('employee_type', ['physician', 'nursing', 'technician'])
            ->select('id', 'full_name', 'employee_code')
            ->limit(50)
            ->get();
    }

    protected function loadAnesthesiologists()
    {
        $this->anesthesiologists = Employee::where('employee_type', 'physician')
            ->select('id', 'full_name', 'employee_code')
            ->limit(50)
            ->get();
    }

    // 💾 الحفظ
    public function save()
    {
        $this->validate();

        $report = OperativeProcedureReport::create([
            'patient_id' => $this->patient_id,
            'visit_id' => $this->visit_id,
            'booking_id' => $this->booking_id,
            'preoperative_diagnosis' => $this->preoperative_diagnosis,
            'postoperative_diagnosis' => $this->postoperative_diagnosis,
            'time_started' => $this->time_started,
            'time_ended' => $this->time_ended,
            'anesthesia_type' => $this->anesthesia_type,
            'procedures_done' => $this->procedures_done,
            'operation_details' => $this->operation_details,
            'blood_loss_ml' => $this->blood_loss_ml,
            'blood_transfused_ml' => $this->blood_transfused_ml,
            'complication_occurred' => $this->complication_occurred,
            'complication_details' => $this->complication_details,
            'specimens_sent' => $this->specimens_sent,
            'specimen_no' => $this->specimen_no,
            'specimen_origin' => $this->specimen_origin,
            'specimen_size_description' => $this->specimen_size_description,
            'specimen_sent_to_lab' => $this->specimen_sent_to_lab,
            'specimen_pathology' => $this->specimen_pathology,
            'specimen_culture' => $this->specimen_culture,
            'implantable_devices' => $this->implantable_devices,
            'implant_details' => $this->implant_details,
            'report_date' => $this->report_date,
            'report_time' => $this->report_time,
            'created_by' => Auth::id(),
        ]);

        // ✅ حفظ الفريق الطبي
        $this->saveMedicalTeam();

        session()->flash('success', '✅ Operative procedure report saved successfully.');
        return redirect()->back();
    }

    protected function saveMedicalTeam()
    {
        if ($this->surgeon_id) {
            Surgery_Medical_Team::updateOrCreate(
                ['surgery_bookings_id' => $this->booking_id, 'role' => 'surgeon'],
                ['medical_team_id' => $this->surgeon_id, 'user_id' => Auth::id(), 'status' => 'active']
            );
        }

        if ($this->first_assistant_id) {
            Surgery_Medical_Team::updateOrCreate(
                ['surgery_bookings_id' => $this->booking_id, 'role' => 'assistant'],
                ['medical_team_id' => $this->first_assistant_id, 'user_id' => Auth::id(), 'status' => 'active']
            );
        }

        if ($this->anesthesiologist_id) {
            Surgery_Medical_Team::updateOrCreate(
                ['surgery_bookings_id' => $this->booking_id, 'role' => 'anesthesiologist'],
                ['medical_team_id' => $this->anesthesiologist_id, 'user_id' => Auth::id(), 'status' => 'active']
            );
        }
    }

    public function render()
    {
        return view('livewire.surgical.operative-procedure-form', [
            'surgeons' => $this->surgeons,
            'assistants' => $this->assistants,
            'anesthesiologists' => $this->anesthesiologists,
        ]);
    }
}