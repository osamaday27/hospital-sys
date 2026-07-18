<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\InfectiousAssessment;

class Infectious extends Component
{
    use WithPagination;

    public $patient_id;
    public $visit_id;
    public $reservation_id;

    public $cough_no = false;
    public $cough_yes = false;
    public $cough_details;

    public $fever_no = false;
    public $fever_yes = false;
    public $fever_details;

    public $rash_no = false;
    public $rash_yes = false;
    public $rash_details;

    public $travel_no = false;
    public $travel_yes = false;
    public $travel_details;

    public $wear_mask = false;
    public $separate_patient = false;
    public $notify_doctor_action = false;
    public $accelerate_examination = false;

    public $notify_doctor_infectious_yes = false;
    public $notify_doctor_infectious_no = false;

    public $selectedRecord;

    protected $rules = [
        'cough_details'    => 'nullable|string|max:255',
        'fever_details'    => 'nullable|string|max:255',
        'rash_details'     => 'nullable|string|max:255',
        'travel_details'   => 'nullable|string|max:255',
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

        InfectiousAssessment::create([
            'patient_id'                   => $this->patient_id,
            'visit_id'                     => $this->visit_id,
            'reservation_id'               => $this->reservation_id,
            'user_add'                     => Auth::id(),
            'cough_no'                     => $this->cough_no,
            'cough_yes'                    => $this->cough_yes,
            'cough_details'                => $this->cough_details,
            'fever_no'                     => $this->fever_no,
            'fever_yes'                    => $this->fever_yes,
            'fever_details'                => $this->fever_details,
            'rash_no'                      => $this->rash_no,
            'rash_yes'                     => $this->rash_yes,
            'rash_details'                 => $this->rash_details,
            'travel_no'                    => $this->travel_no,
            'travel_yes'                   => $this->travel_yes,
            'travel_details'               => $this->travel_details,
            'wear_mask'                    => $this->wear_mask,
            'separate_patient'             => $this->separate_patient,
            'notify_doctor_action'         => $this->notify_doctor_action,
            'accelerate_examination'       => $this->accelerate_examination,
            'notify_doctor_infectious_yes' => $this->notify_doctor_infectious_yes,
            'notify_doctor_infectious_no'  => $this->notify_doctor_infectious_no,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = InfectiousAssessment::findOrFail($id);
        $this->selectedRecord = $record;

        foreach ([
            'cough_no','cough_yes','cough_details',
            'fever_no','fever_yes','fever_details',
            'rash_no','rash_yes','rash_details',
            'travel_no','travel_yes','travel_details',
            'wear_mask','separate_patient',
            'notify_doctor_action','accelerate_examination',
            'notify_doctor_infectious_yes','notify_doctor_infectious_no'
        ] as $field) {
            $this->{$field} = $record->{$field};
        }
    }

    public function update()
    {
        $this->validate();

        $this->selectedRecord->update([
            'cough_no'                     => $this->cough_no,
            'cough_yes'                    => $this->cough_yes,
            'cough_details'                => $this->cough_details,
            'fever_no'                     => $this->fever_no,
            'fever_yes'                    => $this->fever_yes,
            'fever_details'                => $this->fever_details,
            'rash_no'                      => $this->rash_no,
            'rash_yes'                     => $this->rash_yes,
            'rash_details'                 => $this->rash_details,
            'travel_no'                    => $this->travel_no,
            'travel_yes'                   => $this->travel_yes,
            'travel_details'               => $this->travel_details,
            'wear_mask'                    => $this->wear_mask,
            'separate_patient'             => $this->separate_patient,
            'notify_doctor_action'         => $this->notify_doctor_action,
            'accelerate_examination'       => $this->accelerate_examination,
            'notify_doctor_infectious_yes' => $this->notify_doctor_infectious_yes,
            'notify_doctor_infectious_no'  => $this->notify_doctor_infectious_no,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset([
            'cough_no','cough_yes','cough_details',
            'fever_no','fever_yes','fever_details',
            'rash_no','rash_yes','rash_details',
            'travel_no','travel_yes','travel_details',
            'wear_mask','separate_patient',
            'notify_doctor_action','accelerate_examination',
            'notify_doctor_infectious_yes','notify_doctor_infectious_no'
        ]);
    }

    public function render()
    {
        $records = InfectiousAssessment::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.infectious', compact('records'));
    }
}