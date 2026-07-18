<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\PainAssessment;

class Pain extends Component
{
    use WithPagination;

    public $patient_id;
    public $visit_id;
    public $reservation_id;

    public $pain_no = false;
    public $pain_yes = false;

    public $selectedRecord;

    public function mount($patientId, $visitId = null, $reservationId = null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }

    public function save()
    {
        PainAssessment::create([
            'patient_id'       => $this->patient_id,
            'visit_id'         => $this->visit_id,
            'reservation_id'   => $this->reservation_id,
            'user_add'         => Auth::id(),
            'pain_no'          => $this->pain_no,
            'pain_yes'         => $this->pain_yes,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = PainAssessment::findOrFail($id);
        $this->selectedRecord = $record;
        $this->pain_no  = $record->pain_no;
        $this->pain_yes = $record->pain_yes;
    }

    public function update()
    {
        $this->selectedRecord->update([
            'pain_no'  => $this->pain_no,
            'pain_yes' => $this->pain_yes,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset(['pain_no', 'pain_yes']);
    }

    public function render()
    {
        $records = PainAssessment::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.pain', compact('records'));
    }
}