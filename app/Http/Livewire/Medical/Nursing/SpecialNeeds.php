<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\SpecialNeedsAssessment;

class SpecialNeeds extends Component
{
    use WithPagination;

    public $patient_id;
    public $visit_id;
    public $reservation_id;

    public $no_exercise = false;
    public $overweight = false;
    public $hates_school = false;
    public $family_issues = false;

    public $normal_elderly = false;
    public $cannot_count = false;
    public $doesnt_know_facility = false;

    public $physical_disability_yes = false;
    public $physical_disability_details;

    public $weak_immunity_yes = false;
    public $weak_immunity_reason;

    public $chronic_pain_yes = false;

    public $notify_doctor_special_yes = false;
    public $notify_doctor_special_no = false;

    public $selectedRecord;

    protected $rules = [
        'physical_disability_details' => 'nullable|string|max:255',
        'weak_immunity_reason'        => 'nullable|string|max:255',
    ];

    public function mount($patientId, $visitId = null, $reservationId = null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }

    public function save()
    {
        $this->validate();

        SpecialNeedsAssessment::create([
            'patient_id'                  => $this->patient_id,
            'visit_id'                    => $this->visit_id,
            'reservation_id'              => $this->reservation_id,
            'user_add'                    => Auth::id(),
            'no_exercise'                 => $this->no_exercise,
            'overweight'                  => $this->overweight,
            'hates_school'                => $this->hates_school,
            'family_issues'               => $this->family_issues,
            'normal_elderly'              => $this->normal_elderly,
            'cannot_count'                => $this->cannot_count,
            'doesnt_know_facility'        => $this->doesnt_know_facility,
            'physical_disability_yes'     => $this->physical_disability_yes,
            'physical_disability_details' => $this->physical_disability_details,
            'weak_immunity_yes'           => $this->weak_immunity_yes,
            'weak_immunity_reason'        => $this->weak_immunity_reason,
            'chronic_pain_yes'            => $this->chronic_pain_yes,
            'notify_doctor_special_yes'   => $this->notify_doctor_special_yes,
            'notify_doctor_special_no'    => $this->notify_doctor_special_no,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = SpecialNeedsAssessment::findOrFail($id);
        $this->selectedRecord = $record;

        foreach ([
            'no_exercise','overweight','hates_school','family_issues',
            'normal_elderly','cannot_count','doesnt_know_facility',
            'physical_disability_yes','physical_disability_details',
            'weak_immunity_yes','weak_immunity_reason','chronic_pain_yes',
            'notify_doctor_special_yes','notify_doctor_special_no'
        ] as $field) {
            $this->{$field} = $record->{$field};
        }
    }

    public function update()
    {
        $this->validate();

        $this->selectedRecord->update([
            'no_exercise'                 => $this->no_exercise,
            'overweight'                  => $this->overweight,
            'hates_school'                => $this->hates_school,
            'family_issues'               => $this->family_issues,
            'normal_elderly'              => $this->normal_elderly,
            'cannot_count'                => $this->cannot_count,
            'doesnt_know_facility'        => $this->doesnt_know_facility,
            'physical_disability_yes'     => $this->physical_disability_yes,
            'physical_disability_details' => $this->physical_disability_details,
            'weak_immunity_yes'           => $this->weak_immunity_yes,
            'weak_immunity_reason'        => $this->weak_immunity_reason,
            'chronic_pain_yes'            => $this->chronic_pain_yes,
            'notify_doctor_special_yes'   => $this->notify_doctor_special_yes,
            'notify_doctor_special_no'    => $this->notify_doctor_special_no,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset([
            'no_exercise','overweight','hates_school','family_issues',
            'normal_elderly','cannot_count','doesnt_know_facility',
            'physical_disability_yes','physical_disability_details',
            'weak_immunity_yes','weak_immunity_reason','chronic_pain_yes',
            'notify_doctor_special_yes','notify_doctor_special_no'
        ]);
    }

    public function render()
    {
        $records = SpecialNeedsAssessment::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.special-needs', compact('records'));
    }
}