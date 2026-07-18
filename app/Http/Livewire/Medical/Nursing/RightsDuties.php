<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\RightsAssessment;

class RightsDuties extends Component
{
    use WithPagination;

    public $patient_id;
    public $visit_id;
    public $reservation_id;

    public $rights_yes = false;
    public $rights_no = false;
    public $rights_action;

    public $selectedRecord;

    protected $rules = [
        'rights_action' => 'nullable|string|max:255',
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

        RightsAssessment::create([
            'patient_id'     => $this->patient_id,
            'visit_id'       => $this->visit_id,
            'reservation_id' => $this->reservation_id,
            'user_add'       => Auth::id(),
            'rights_yes'     => $this->rights_yes,
            'rights_no'      => $this->rights_no,
            'rights_action'  => $this->rights_action,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = RightsAssessment::findOrFail($id);
        $this->selectedRecord = $record;

        $this->rights_yes    = $record->rights_yes;
        $this->rights_no     = $record->rights_no;
        $this->rights_action = $record->rights_action;
    }

    public function update()
    {
        $this->validate();

        $this->selectedRecord->update([
            'rights_yes'    => $this->rights_yes,
            'rights_no'     => $this->rights_no,
            'rights_action' => $this->rights_action,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset(['rights_yes','rights_no','rights_action']);
    }

    public function render()
    {
        $records = RightsAssessment::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.rights-duties', compact('records'));
    }
}