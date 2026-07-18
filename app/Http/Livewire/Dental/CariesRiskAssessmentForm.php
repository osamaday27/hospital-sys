<?php

namespace App\Http\Livewire\Dental;

use Livewire\Component;
use App\models\medical\CariesRiskAssessment;

class CariesRiskAssessmentForm extends Component
{
    public $patient_id, $id_reservation, $id_visit, $type_visit;
    public $high_risk = [], $moderate_risk = [], $protective_factors = [];
    public $brushing_frequency, $brushing_method, $brush_type, $interproximal_cleansing;
    public $caries_risk_overall, $recommendations_given = false, $date_given;
    public $faculty_signature, $date_reviewed;
    public $tab = 'form';
    public $editId = null;

    protected $rules = [
        'caries_risk_overall' => 'required|string',
        'faculty_signature' => 'required|string',
    ];

    public function mount($patientId, $reservationId = null, $visitId = null, $typeVisit = null)
    {
        $this->patient_id = $patientId;
        $this->id_reservation = $reservationId;
        $this->id_visit = $visitId;
        $this->type_visit = $typeVisit;

        // Initialize empty structure
        $this->initializeQuestions();
    }

    private function initializeQuestions()
    {
        $high = [
            'Visible caries into dentin by radiograph',
            'Caries restored in past three years',
            '# of new carious lesions',
            '# of carious lesions with existing restorations',
            'DMFT',
            'Heavy plaque on teeth',
            'Frequent snacks of sugars/starch',
            'Hyposalivatory medications',
            'Radiation to head/neck',
            'Systemic reasons (Sjogren\'s)',
            'Inadequate salivary flow',
            'Appliances present'
        ];

        $moderate = [
            'Exposed roots',
            'Deep pits & fissures',
            'Interproximal enamel lesions',
            'White spot lesions/discoloration',
            'Uses recreational drugs'
        ];

        $protective = [
            'Lives/works in fluoridated community',
            'Uses fluoride toothpaste daily',
            'Uses fluoride mouthwash',
            'Adequate salivary flow',
            'Uses Xylitol gum/mints'
        ];

        foreach ($high as $item) {
            $this->high_risk[$item] = ['checked' => false, 'note' => ''];
        }
        foreach ($moderate as $item) {
            $this->moderate_risk[$item] = ['checked' => false, 'note' => ''];
        }
        foreach ($protective as $item) {
            $this->protective_factors[$item] = ['checked' => false, 'note' => ''];
        }
    }

    public function save()
    {
        $this->validate();

        CariesRiskAssessment::updateOrCreate(
            ['id' => $this->editId],
            [
                'patient_id' => $this->patient_id,
                'id_reservation' => $this->id_reservation,
                'id_visit' => $this->id_visit,
                'type_visit' => $this->type_visit,
                'high_risk' => $this->high_risk,
                'moderate_risk' => $this->moderate_risk,
                'protective_factors' => $this->protective_factors,
                'brushing_frequency' => $this->brushing_frequency,
                'brushing_method' => $this->brushing_method,
                'brush_type' => $this->brush_type,
                'interproximal_cleansing' => $this->interproximal_cleansing,
                'caries_risk_overall' => $this->caries_risk_overall,
                'recommendations_given' => $this->recommendations_given,
                'date_given' => $this->date_given,
                'faculty_signature' => $this->faculty_signature,
                'date_reviewed' => $this->date_reviewed,
                'status' => 'active',
            ]
        );

        session()->flash('message', $this->editId ? 'Updated successfully!' : 'Saved successfully!');

        $this->resetExcept(['patient_id', 'id_reservation', 'id_visit', 'type_visit']);
        $this->initializeQuestions();
    }

    public function edit($id)
    {
        $record = CariesRiskAssessment::findOrFail($id);
        $this->editId = $record->id;
        $this->high_risk = $record->high_risk ?? [];
        $this->moderate_risk = $record->moderate_risk ?? [];
        $this->protective_factors = $record->protective_factors ?? [];
        $this->brushing_frequency = $record->brushing_frequency;
        $this->brushing_method = $record->brushing_method;
        $this->brush_type = $record->brush_type;
        $this->interproximal_cleansing = $record->interproximal_cleansing;
        $this->caries_risk_overall = $record->caries_risk_overall;
        $this->recommendations_given = $record->recommendations_given;
        $this->date_given = $record->date_given;
        $this->faculty_signature = $record->faculty_signature;
        $this->date_reviewed = $record->date_reviewed;
        $this->tab = 'form';
    }

    public function delete($id)
    {
        CariesRiskAssessment::findOrFail($id)->delete();
        session()->flash('message', 'Deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $record = CariesRiskAssessment::findOrFail($id);
        $record->status = $record->status === 'active' ? 'inactive' : 'active';
        $record->save();
        session()->flash('message', 'Status updated!');
    }

    public function render()
    {
        $records = CariesRiskAssessment::where('patient_id', $this->patient_id)->latest()->get();
        return view('livewire.dental.caries-risk-assessment-form', [
            'records' => $records,
        ]);
    }
}