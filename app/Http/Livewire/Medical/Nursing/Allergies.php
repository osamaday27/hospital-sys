<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\allergyassessment;

class Allergies extends Component
{
    use WithPagination;

    public $patient_id;
    public $visit_id;
    public $reservation_id;

    public $allergies_no = false;
    public $allergies_yes = false;

    public $allergy_food = false;
    public $allergy_medication = false;
    public $allergy_others = false;
    public $allergy_others_details;

    public $notify_doctor_yes = false;
    public $notify_doctor_no = false;

    public $selectedRecord;

    protected $rules = [
        'allergy_others_details' => 'nullable|string|max:255',
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

        AllergyAssessment::create([
            'patient_id'               => $this->patient_id,
            'visit_id'                 => $this->visit_id,
            'reservation_id'           => $this->reservation_id,
            'user_add'                 => Auth::id(),
            'allergies_no'             => $this->allergies_no,
            'allergies_yes'            => $this->allergies_yes,
            'allergy_food'             => $this->allergy_food,
            'allergy_medication'       => $this->allergy_medication,
            'allergy_others'           => $this->allergy_others,
            'allergy_others_details'   => $this->allergy_others_details,
            'notify_doctor_yes'        => $this->notify_doctor_yes,
            'notify_doctor_no'         => $this->notify_doctor_no,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = AllergyAssessment::findOrFail($id);
        $this->selectedRecord = $record;

        foreach ([
            'allergies_no','allergies_yes',
            'allergy_food','allergy_medication','allergy_others','allergy_others_details',
            'notify_doctor_yes','notify_doctor_no'
        ] as $field) {
            $this->{$field} = $record->{$field};
        }
    }

    public function update()
    {
        $this->validate();

        $this->selectedRecord->update([
            'allergies_no'             => $this->allergies_no,
            'allergies_yes'            => $this->allergies_yes,
            'allergy_food'             => $this->allergy_food,
            'allergy_medication'       => $this->allergy_medication,
            'allergy_others'           => $this->allergy_others,
            'allergy_others_details'   => $this->allergy_others_details,
            'notify_doctor_yes'        => $this->notify_doctor_yes,
            'notify_doctor_no'         => $this->notify_doctor_no,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset([
            'allergies_no','allergies_yes',
            'allergy_food','allergy_medication','allergy_others','allergy_others_details',
            'notify_doctor_yes','notify_doctor_no'
        ]);
    }

    public function render()
    {
        $records = AllergyAssessment::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.allergies', compact('records'));
    }
}