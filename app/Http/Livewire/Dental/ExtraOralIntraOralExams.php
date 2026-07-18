<?php
namespace App\Http\Livewire\Dental;

use Livewire\Component;
use App\models\medical\ExtraOralIntraOralExam;

class ExtraOralIntraOralExams extends Component
{
    public $patient_id, $id_reservation, $id_visit, $type_visit;
    public $general_appearance, $mobility, $lesions_or_scars, $other_notes;
    public $lymph_node, $head_neck_notes;
    public $tmj, $tmj_notes;
    public $asymmetries;
    public $lips_labial_mucosa, $buccal_vestibular_mucosa, $hard_soft_palate, $tongue, $floor_of_mouth, $gingiva_alveolar_mucosa;
    public $further_examinations;
    public $tab = 'form';

    public function mount($patientId, $reservationId = null, $visitId = null, $typeVisit = null)
    {
        $this->patient_id = $patientId;
        $this->id_reservation = $reservationId;
        $this->id_visit = $visitId;
        $this->type_visit = $typeVisit;
    }

    public function save()
    {
        ExtraOralIntraOralExam::create([
            'patient_id' => $this->patient_id,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'general_appearance' => $this->general_appearance,
            'mobility' => $this->mobility,
            'lesions_or_scars' => $this->lesions_or_scars,
            'other_notes' => $this->other_notes,
            'lymph_node' => $this->lymph_node,
            'head_neck_notes' => $this->head_neck_notes,
            'tmj' => $this->tmj,
            'tmj_notes' => $this->tmj_notes,
            'asymmetries' => $this->asymmetries,
            'lips_labial_mucosa' => $this->lips_labial_mucosa,
            'buccal_vestibular_mucosa' => $this->buccal_vestibular_mucosa,
            'hard_soft_palate' => $this->hard_soft_palate,
            'tongue' => $this->tongue,
            'floor_of_mouth' => $this->floor_of_mouth,
            'gingiva_alveolar_mucosa' => $this->gingiva_alveolar_mucosa,
            'further_examinations' => $this->further_examinations,
        ]);

        session()->flash('message', 'Examination saved successfully!');
        $this->reset([
            'general_appearance', 'mobility', 'lesions_or_scars', 'other_notes', 'lymph_node', 'head_neck_notes',
            'tmj', 'tmj_notes', 'asymmetries', 'lips_labial_mucosa', 'buccal_vestibular_mucosa', 'hard_soft_palate',
            'tongue', 'floor_of_mouth', 'gingiva_alveolar_mucosa', 'further_examinations'
        ]);
    }

    public function render()
    {
        $previousExams = ExtraOralIntraOralExam::where('patient_id', $this->patient_id)->latest()->get();
        return view('livewire.dental.extra-oral-intra-oral-exams', compact('previousExams'));
    }
}