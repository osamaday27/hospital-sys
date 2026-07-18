<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\functionalassessment;

class MotorFunctional extends Component
{
    use WithPagination;

    public $patient_id;
    public $visit_id;
    public $reservation_id;

    public $live_alone = false;
    public $no_problem = false;
    public $total_dependence = false;
    public $movement_problems = false;

    public $no_assistance = false;
    public $walker = false;
    public $wheelchair = false;
    public $crutches = false;
    public $other_assistance = false;

    public $notify_doctor_functional_yes = false;
    public $notify_doctor_functional_no = false;

    public $selectedRecord;

    public function mount($patientId, $visitId = null, $reservationId = null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }

    public function save()
    {
        FunctionalAssessment::create([
            'patient_id'                        => $this->patient_id,
            'visit_id'                          => $this->visit_id,
            'reservation_id'                    => $this->reservation_id,
            'user_add'                          => Auth::id(),
            'live_alone'                        => $this->live_alone,
            'no_problem'                        => $this->no_problem,
            'total_dependence'                  => $this->total_dependence,
            'movement_problems'                 => $this->movement_problems,
            'no_assistance'                     => $this->no_assistance,
            'walker'                            => $this->walker,
            'wheelchair'                        => $this->wheelchair,
            'crutches'                          => $this->crutches,
            'other_assistance'                  => $this->other_assistance,
            'notify_doctor_functional_yes'      => $this->notify_doctor_functional_yes,
            'notify_doctor_functional_no'       => $this->notify_doctor_functional_no,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = FunctionalAssessment::findOrFail($id);
        $this->selectedRecord = $record;

        foreach ([
            'live_alone','no_problem','total_dependence','movement_problems',
            'no_assistance','walker','wheelchair','crutches','other_assistance',
            'notify_doctor_functional_yes','notify_doctor_functional_no'
        ] as $field) {
            $this->{$field} = $record->{$field};
        }
    }

    public function update()
    {
        $this->selectedRecord->update([
            'live_alone'                        => $this->live_alone,
            'no_problem'                        => $this->no_problem,
            'total_dependence'                  => $this->total_dependence,
            'movement_problems'                 => $this->movement_problems,
            'no_assistance'                     => $this->no_assistance,
            'walker'                            => $this->walker,
            'wheelchair'                        => $this->wheelchair,
            'crutches'                          => $this->crutches,
            'other_assistance'                  => $this->other_assistance,
            'notify_doctor_functional_yes'      => $this->notify_doctor_functional_yes,
            'notify_doctor_functional_no'       => $this->notify_doctor_functional_no,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset([
            'live_alone','no_problem','total_dependence','movement_problems',
            'no_assistance','walker','wheelchair','crutches','other_assistance',
            'notify_doctor_functional_yes','notify_doctor_functional_no'
        ]);
    }

    public function render()
    {
        $records = FunctionalAssessment::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.motor-functional', compact('records'));
    }
}