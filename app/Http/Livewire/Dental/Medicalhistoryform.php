<?php

namespace App\Http\Livewire\Dental;

use Livewire\Component;
use App\models\medical\MedicalHistory;

class MedicalHistoryForm extends Component
{
    public $patient_id, $id_reservation, $id_visit, $type_visit;
    public $medical_conditions, $medications = [];
    public $medical_treatment, $medical_treatment_note;
    public $serious_operation, $serious_operation_note;
    public $taking_medication, $taking_medication_note;
    public $smoker, $smoke_count;
    public $female, $allergies;
    public $editId, $tab = 'form';

    public function mount($patientId, $reservationId = null, $visitId = null, $typeVisit = null)
    {
        $this->patient_id = $patientId;
        $this->id_reservation = $reservationId;
        $this->id_visit = $visitId;
        $this->type_visit = $typeVisit;
        $this->initializeFields();
    }

    private function initializeFields()
    {
        $this->medical_conditions = [
            'active_tuberculosis' => ['checked' => false, 'note' => ''],
            'diabetes' => ['checked' => false, 'note' => ''],
            'high_blood_pressure' => ['checked' => false, 'note' => ''],
            'heart_condition' => ['checked' => false, 'note' => ''],
            'hepatitis' => ['checked' => false, 'note' => ''],
            'other_infectious_disease' => [],
        ];
        $this->medical_treatment = false;
        $this->medical_treatment_note = '';
        $this->serious_operation = false;
        $this->serious_operation_note = '';
        $this->taking_medication = false;
        $this->taking_medication_note = '';
        $this->smoker = false;
        $this->smoke_count = '';
        $this->female = ['pregnant' => '', 'weeks' => '', 'nursing' => ''];
        $this->allergies = [
            'penicillins' => false,
            'aspirin' => false,
            'local_anesthesia' => false,
            'latex' => false,
            'other_drugs' => [],
            'other_substances' => [],
        ];
    }

    public function addOtherInfectious() { $this->medical_conditions['other_infectious_disease'][] = ''; }
    public function removeOtherInfectious($index) { unset($this->medical_conditions['other_infectious_disease'][$index]); $this->medical_conditions['other_infectious_disease'] = array_values($this->medical_conditions['other_infectious_disease']); }
    public function addMedication() { $this->medications[] = ''; }
    public function removeMedication($index) { unset($this->medications[$index]); $this->medications = array_values($this->medications); }
    public function addOtherDrug() { $this->allergies['other_drugs'][] = ''; }
    public function removeOtherDrug($index) { unset($this->allergies['other_drugs'][$index]); $this->allergies['other_drugs'] = array_values($this->allergies['other_drugs']); }
    public function addOtherSubstance() { $this->allergies['other_substances'][] = ''; }
    public function removeOtherSubstance($index) { unset($this->allergies['other_substances'][$index]); $this->allergies['other_substances'] = array_values($this->allergies['other_substances']); }

    public function save()
    {
        MedicalHistory::updateOrCreate(['id' => $this->editId], [
            'patient_id' => $this->patient_id,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'medical_conditions' => $this->medical_conditions,
            'medications' => $this->medications,
            'medical_treatment' => $this->medical_treatment,
            'medical_treatment_note' => $this->medical_treatment_note,
            'serious_operation' => $this->serious_operation,
            'serious_operation_note' => $this->serious_operation_note,
            'taking_medication' => $this->taking_medication,
            'taking_medication_note' => $this->taking_medication_note,
            'smoker' => $this->smoker,
            'smoke_count' => $this->smoke_count,
            'female' => $this->female,
            'allergies' => $this->allergies,
            'status' => 'active',
        ]);

        session()->flash('message', $this->editId ? 'Updated successfully!' : 'Saved successfully!');
        $this->resetExcept(['patient_id', 'id_reservation', 'id_visit', 'type_visit']);
        $this->initializeFields();
    }

    public function edit($id)
    {
        $record = MedicalHistory::findOrFail($id);
        $this->editId = $record->id;
        $this->medical_conditions = $record->medical_conditions;
        $this->medications = $record->medications;
        $this->medical_treatment = $record->medical_treatment;
        $this->medical_treatment_note = $record->medical_treatment_note;
        $this->serious_operation = $record->serious_operation;
        $this->serious_operation_note = $record->serious_operation_note;
        $this->taking_medication = $record->taking_medication;
        $this->taking_medication_note = $record->taking_medication_note;
        $this->smoker = $record->smoker;
        $this->smoke_count = $record->smoke_count;
        $this->female = $record->female;
        $this->allergies = $record->allergies;
        $this->tab = 'form';
    }

    public function delete($id)
    {
        MedicalHistory::findOrFail($id)->delete();
        session()->flash('message', 'Deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $record = MedicalHistory::findOrFail($id);
        $record->status = $record->status === 'active' ? 'inactive' : 'active';
        $record->save();
        session()->flash('message', 'Status updated!');
    }

    public function render()
    {
        $records = MedicalHistory::where('patient_id', $this->patient_id)->latest()->get();
        return view('livewire.dental.medical-history-form', ['records' => $records]);
    }
}