<?php
namespace App\Http\Livewire\Dental;

use Livewire\Component;
use App\models\medical\MDDentalHistory;

class DentalHistories extends Component
{
    public $patient_id, $id_reservation, $id_visit, $type_visit;
    public $dental_care, $past_treatment = [];
    public $jaw_trauma_notes, $adverse_reaction_notes, $dental_fears_notes, $oral_health_state;

    public $historyTab = 'form'; // tab controller

    public function mount($patientId, $reservationId = null, $visitId = null, $typeVisit = null)
    {
        $this->patient_id = $patientId;
        $this->id_reservation = $reservationId;
        $this->id_visit = $visitId;
        $this->type_visit = $typeVisit;
    }

    public function save()
    {
        MDDentalHistory::create([
            'patient_id' => $this->patient_id,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'dental_care' => $this->dental_care,
            'past_treatment' => json_encode($this->past_treatment),
            'jaw_trauma_notes' => $this->jaw_trauma_notes,
            'adverse_reaction_notes' => $this->adverse_reaction_notes,
            'dental_fears_notes' => $this->dental_fears_notes,
            'oral_health_state' => $this->oral_health_state,
        ]);

        session()->flash('message', 'Dental history saved successfully!');
        $this->reset(['dental_care', 'past_treatment', 'jaw_trauma_notes', 'adverse_reaction_notes', 'dental_fears_notes', 'oral_health_state']);
    }

    public function render()
    {
        $previousHistories = MDDentalHistory::where('patient_id', $this->patient_id)->latest()->get();
        return view('livewire.dental.dental-histories', compact('previousHistories'));
    }
}