<?php
namespace App\Http\Livewire\Doctor;

use Livewire\Component;

class PatientOphthalmologyTab extends Component
{
    public $id_patient;
    public $id_reservation;
    public $type_visit;
    public $id_visit;
    public $activeTab = 'overview';

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.doctor.patient-ophthalmology-tab');
    }
}