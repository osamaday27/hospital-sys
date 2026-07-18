<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Administrative\Adm_b_surgery_timeout;
use App\Models\Administrative\Adm_b_surgery_bookings;
use App\Models\Administrative\Surgery_Medical_Team;
use App\Models\Finance\Service;
use App\Models\Hr\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SurgeryTimeoutForm extends Component
{
    public $booking_id;
    public $timeoutRecord;
    public $medical_team = [];
    public $procedure;
    public $pre_op_diagnosis;
    public $patient_identity;
    public $correct_side;
    public $correct_procedure;
    public $availability_of_implants;
    public $anesthesia_concerns;
    public $antibiotic_prophylaxis_given;
    public $essential_imaging_displayed;
    public $timeout_documented = false;
    // 🟩 ضيف السطر ده
    public $procedures = [];
    // الفريق الطبي
    public $assistant_surgeon;
    public $assistantSearch = '';
    public $assistants = [];

    public $anesthesiologist;
    public $anesthSearch = '';
    public $anesthesiologists = [];

    public $sterile_nurse;
    public $sterileSearch = '';
    public $steriles = [];

    // باقي الخصائص
    public $timeout_time;
    public $timeout_date;
    public $procedure_start_time;
    public $procedure_end_time;
    public $surgeon_signature;
    public $nurse_signature;
    public $surgeonSearch = '';
    public $nurseSearch = '';
    public $surgeons = [];
    public $nurses = [];

    public function mount($booking_id)
    {
        $this->booking_id = $booking_id;
        $this->timeout_date = now()->format('Y-m-d');
        $this->timeout_time = now()->format('H:i');

        $booking = Adm_b_surgery_bookings::with(['services', 'medicalTeam'])->find($this->booking_id);
        if ($booking) {
             // تحميل جميع الإجراءات
        foreach ($booking->bookingSurgeries as $bs) {
            $this->procedures[] = [
                'name' => $bs->surgery?->getTranslation('name', app()->getLocale()),
                'side' => $bs->side ?? '',
            ];
        }
            if ($booking->scheduled_from) {
                $start = Carbon::parse($booking->scheduled_from);
                $this->procedure_start_time = $start->format('H:i');
                $this->procedure_end_time = $start->copy()->addHours(2)->format('H:i');
            } else {
                $this->procedure_start_time = now()->format('H:i');
                $this->procedure_end_time = now()->addHours(2)->format('H:i');
            }

            $firstService = $booking->services->first();
            if ($firstService) {
                $this->procedure = $firstService->getTranslation('name', app()->getLocale());
            }

            $this->medical_team = $booking->medicalTeam()->with('medical_team')->get();
        }

        $this->timeoutRecord = Adm_b_surgery_timeout::where('surgery_booking_id', $this->booking_id)->first();

        if ($this->timeoutRecord) {
            foreach ($this->timeoutRecord->getAttributes() as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            $defaultSurgeon = Surgery_Medical_Team::with('medical_team')
                ->where('surgery_bookings_id', $this->booking_id)
                ->where('role', 'surgeon')
                ->first();

            if ($defaultSurgeon && $defaultSurgeon->medical_team) {
                $this->surgeon_signature = $defaultSurgeon->medical_team->full_name;
            }

            if (Auth::check()) {
                $user = Auth::user();
                $employee = Employee::where('id', $user->employee_id)->first();
                $this->nurse_signature = $employee->full_name ?? $user->name;
            }
        }

        $this->loadSurgeons();
        $this->loadNurses();
        $this->loadAssistants();
        $this->loadAnesthesiologists();
        $this->loadSteriles();
    }
// 🔹 الجراحين
public function bookingSurgeries()
{
    return $this->hasMany(BookingSurgery::class, 'booking_id');
}

public function loadSurgeons()
{
    $this->surgeons = Employee::query()
        ->where('employee_type', 'physician')
        ->where(function ($q) {
            $q->where('full_name', 'like', "%{$this->surgeonSearch}%")
              ->orWhere('employee_code', 'like', "%{$this->surgeonSearch}%");
        })
        ->limit(20)
        ->get(['id', 'full_name', 'employee_code']);
}

// 🔹 التمريض
public function loadNurses()
{
    $this->nurses = Employee::query()
        ->where('employee_type', 'nursing')
        ->orWhere('department', 'like', '%تمريض%')
        ->where(function ($q) {
            $q->where('full_name', 'like', "%{$this->nurseSearch}%")
              ->orWhere('employee_code', 'like', "%{$this->nurseSearch}%");
        })
        ->limit(20)
        ->get(['id', 'full_name', 'employee_code']);
}

// 🔹 المساعد الجراح
public function loadAssistants()
{
    $this->assistants = Employee::query()
        ->whereIn('employee_type', ['physician', 'nursing', 'technician'])
        ->where(function ($q) {
            $q->where('full_name', 'like', "%{$this->assistantSearch}%")
              ->orWhere('employee_code', 'like', "%{$this->assistantSearch}%");
        })
        ->limit(20)
        ->get(['id', 'full_name', 'employee_code']);
}

// 🔹 أطباء التخدير
public function loadAnesthesiologists()
{
    $this->anesthesiologists = Employee::query()
        ->where('employee_type', 'physician')
        ->where(function ($q) {
            $q->where('full_name', 'like', "%{$this->anesthSearch}%")
              ->orWhere('employee_code', 'like', "%{$this->anesthSearch}%");
        })
        ->limit(20)
        ->get(['id', 'full_name', 'employee_code']);
}

// 🔹 التمريض المعقم
public function loadSteriles()
{
    $this->steriles = Employee::query()
        ->where('employee_type', 'nursing')
        ->where(function ($q) {
            $q->where('full_name', 'like', "%{$this->sterileSearch}%")
              ->orWhere('employee_code', 'like', "%{$this->sterileSearch}%");
        })
        ->limit(20)
        ->get(['id', 'full_name', 'employee_code']);
}

    // 🔹 تحديث البحث أثناء الكتابة
    public function updatedSurgeonSearch() { $this->loadSurgeons(); }
    public function updatedNurseSearch() { $this->loadNurses(); }
    public function updatedAssistantSearch() { $this->loadAssistants(); }
    public function updatedAnesthSearch() { $this->loadAnesthesiologists(); }
    public function updatedSterileSearch() { $this->loadSteriles(); }

    // 🔹 حفظ البيانات
    public function save()
    {
        $data = [
            'surgery_booking_id' => $this->booking_id,
            'procedure' => $this->procedure,
            'pre_op_diagnosis' => $this->pre_op_diagnosis,
            'patient_identity' => $this->patient_identity,
            'correct_side' => $this->correct_side,
            'correct_procedure' => $this->correct_procedure,
            'availability_of_implants' => $this->availability_of_implants,
            'anesthesia_concerns' => $this->anesthesia_concerns,
            'antibiotic_prophylaxis_given' => $this->antibiotic_prophylaxis_given,
            'essential_imaging_displayed' => $this->essential_imaging_displayed,
            'timeout_documented' => $this->timeout_documented,
            'assistant_surgeon' => $this->assistant_surgeon,
            'anesthesiologist' => $this->anesthesiologist,
            'sterile_nurse' => $this->sterile_nurse,
            'timeout_time' => $this->timeout_time,
            'timeout_date' => $this->timeout_date,
            'procedure_start_time' => $this->procedure_start_time,
            'procedure_end_time' => $this->procedure_end_time,
            'surgeon_signature' => $this->surgeon_signature,
            'nurse_signature' => $this->nurse_signature,
        ];

        if ($this->timeoutRecord) {
            $this->timeoutRecord->update($data);
            session()->flash('message', __('surgical_trans.updated_successfully'));
        } else {
            Adm_b_surgery_timeout::create($data);
            session()->flash('message', __('surgical_trans.saved_successfully'));
        }
    }

    public function render()
    {
        return view('livewire.surgical.surgery-timeout-form', [
            'surgeons' => $this->surgeons,
            'nurses' => $this->nurses,
            'assistants' => $this->assistants,
            'anesthesiologists' => $this->anesthesiologists,
            'steriles' => $this->steriles,
        ]);
    }
}