<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Administrative\Adm_b_preoperative_checklist;
use App\Models\Hr\Employee;
use Illuminate\Support\Facades\Auth;
use App\Models\Administrative\Surgery_Medical_Team;

class PreoperativeChecklistForm extends Component
{
    public $booking_id;
    public $operation_name;
    public $equipment_status = [];
    public $consent_signed;
    public $site_marked;
    public $physical_exam_done;
    public $images_available;
    public $labs_available;
    public $allergy_recorded;
    public $surgeon_signature;
    public $nurse_signature;
    public $checklist_date;
    public $checklist_time;
    public $existingChecklist;

    // 🔹 البحث والاختيار للجراح والممرضة
    public $surgeonSearch = '';
    public $nurseSearch = '';
    public $surgeons = [];
    public $nurses = [];

    public $equipmentList = [
        'microscope_lumera','stellaris_bausch','suction_unit','schwind_amaris','moria_2',
        'anesthesia_monitor','needle_holder','tying','mc_fersone','speculum','capsulorexus',
        'irrigation','aspiration','cannula','saneski','spatula','scissor','hook','lens',
        'keratome','mvr_knife','nylon_thread_0_10','vicryl_thread_0_6','vicryl_thread_0_7',
        'vicryl_thread_0_8','prolene_thread_0_10',
    ];

    public function mount($booking_id)
    {
        $this->booking_id = $booking_id;
        $this->checklist_date = now()->format('Y-m-d');
        $this->checklist_time = now()->format('H:i');

        $this->existingChecklist = Adm_b_preoperative_checklist::where('booking_id', $booking_id)->first();

        if ($this->existingChecklist) {
            $this->fill($this->existingChecklist->toArray());
            $this->equipment_status = json_decode($this->existingChecklist->equipment_status ?? '[]', true);
        } else {
              $defaultSurgeon = Surgery_Medical_Team::with('medical_team')
                ->where('surgery_bookings_id', $this->booking_id)
                ->where('role', 'surgeon')
                ->first();

            if ($defaultSurgeon && $defaultSurgeon->medical_team) {
                $this->surgeon_signature = $defaultSurgeon->medical_team->full_name;
            }

            // ✅ تعبئة الممرضة الحالية (المستخدم الحالي)
            if (Auth::check()) {
                $user = Auth::user();
                $employee = Employee::where('id', $user->employee_id)->first();
                $this->nurse_signature = $employee->full_name ?? $user->name;
            }
        }

        $this->loadSurgeons();
        $this->loadNurses();
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

    public function updatedSurgeonSearch() { $this->loadSurgeons(); }
    public function updatedNurseSearch() { $this->loadNurses(); }

    public function save()
    {
        $data = [
            'booking_id' => $this->booking_id,
            'operation_name' => $this->operation_name,
            'equipment_status' => json_encode($this->equipment_status),
            'consent_signed' => $this->consent_signed,
            'site_marked' => $this->site_marked,
            'physical_exam_done' => $this->physical_exam_done,
            'images_available' => $this->images_available,
            'labs_available' => $this->labs_available,
            'allergy_recorded' => $this->allergy_recorded,
            'surgeon_signature' => $this->surgeon_signature,
            'nurse_signature' => $this->nurse_signature,
            'checklist_date' => $this->checklist_date,
            'checklist_time' => $this->checklist_time,
        ];

        Adm_b_preoperative_checklist::updateOrCreate(
            ['booking_id' => $this->booking_id],
            $data
        );

        session()->flash('message', __('surgical_trans.saved_successfully'));
    }

    public function render()
    {
        return view('livewire.surgical.preoperative-checklist-form', [
            'surgeons' => $this->surgeons,
            'nurses' => $this->nurses,
        ]);
    }
}