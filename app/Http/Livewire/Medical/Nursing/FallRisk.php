<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\FallRiskScreening;

class FallRisk extends Component
{
    use WithPagination;

    public $patient_id, $visit_id, $reservation_id;

    public $assist_patient = false;
    public $ensure_aids = false;
    public $review_medications = false;
    public $refer_conditions = false;
    public $avoid_crowding = false;

    public $advise_no_self_move = false;
    public $advise_no_sudden_stand = false;
    public $advise_no_left_alone_child = false;
    public $advise_report_dizziness = false;

    public $notify_doctor_yes = false;
    public $notify_doctor_no = false;

    public $selectedRecord;

    public function mount($patientId, $visitId = null, $reservationId = null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }

    public function save()
    {
        FallRiskScreening::create([
            'patient_id'                  => $this->patient_id,
            'visit_id'                    => $this->visit_id,
            'reservation_id'              => $this->reservation_id,
            'user_add'                    => Auth::id(),
            'assist_patient'              => $this->assist_patient,
            'ensure_aids'                 => $this->ensure_aids,
            'review_medications'          => $this->review_medications,
            'refer_conditions'            => $this->refer_conditions,
            'avoid_crowding'              => $this->avoid_crowding,
            'advise_no_self_move'         => $this->advise_no_self_move,
            'advise_no_sudden_stand'      => $this->advise_no_sudden_stand,
            'advise_no_left_alone_child'  => $this->advise_no_left_alone_child,
            'advise_report_dizziness'     => $this->advise_report_dizziness,
            'notify_doctor_yes'           => $this->notify_doctor_yes,
            'notify_doctor_no'            => $this->notify_doctor_no,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $r = FallRiskScreening::findOrFail($id);
        $this->selectedRecord = $r;

        foreach ([
            'assist_patient','ensure_aids','review_medications','refer_conditions','avoid_crowding',
            'advise_no_self_move','advise_no_sudden_stand','advise_no_left_alone_child','advise_report_dizziness',
            'notify_doctor_yes','notify_doctor_no'
        ] as $f) {
            $this->{$f} = $r->{$f};
        }
    }

    public function update()
    {
        $this->selectedRecord->update([
            'assist_patient'              => $this->assist_patient,
            'ensure_aids'                 => $this->ensure_aids,
            'review_medications'          => $this->review_medications,
            'refer_conditions'            => $this->refer_conditions,
            'avoid_crowding'              => $this->avoid_crowding,
            'advise_no_self_move'         => $this->advise_no_self_move,
            'advise_no_sudden_stand'      => $this->advise_no_sudden_stand,
            'advise_no_left_alone_child'  => $this->advise_no_left_alone_child,
            'advise_report_dizziness'     => $this->advise_report_dizziness,
            'notify_doctor_yes'           => $this->notify_doctor_yes,
            'notify_doctor_no'            => $this->notify_doctor_no,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset([
            'assist_patient','ensure_aids','review_medications','refer_conditions','avoid_crowding',
            'advise_no_self_move','advise_no_sudden_stand','advise_no_left_alone_child','advise_report_dizziness',
            'notify_doctor_yes','notify_doctor_no'
        ]);
    }

    public function render()
    {
        $records = FallRiskScreening::with('user')->latest()->paginate(10);
        return view('livewire.medical.nursing.fall-risk', compact('records'));
    }
}