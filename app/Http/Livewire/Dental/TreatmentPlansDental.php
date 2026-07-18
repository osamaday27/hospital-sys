<?php

namespace App\Http\Livewire\Dental;

use Livewire\Component;
use App\models\medical\MDTreatmentPlanDental;

class TreatmentPlansDental extends Component
{
    public $patient_id, $id_reservation, $id_visit, $type_visit;
    public $tooth, $seq, $clinical_radiographic_findings, $department, $treatment_options, $faculty_signature, $status = 'active';
    public $tab = 'form';
    public $editId = null;

    protected $rules = [
        'tooth' => 'required|string|max:255',
        'seq' => 'required|integer',
        'clinical_radiographic_findings' => 'required|string',
        'department' => 'required|string',
        'treatment_options' => 'required|string',
        'faculty_signature' => 'required|string',
    ];

    public function mount($patientId, $reservationId = null, $visitId = null, $typeVisit = null)
    {
        $this->patient_id = $patientId;
        $this->id_reservation = $reservationId;
        $this->id_visit = $visitId;
        $this->type_visit = $typeVisit;
    }

    public function save()
    {
        $this->validate();

        MDTreatmentPlanDental::updateOrCreate(
            ['id' => $this->editId],
            [
                'patient_id' => $this->patient_id,
                'id_reservation' => $this->id_reservation,
                'id_visit' => $this->id_visit,
                'type_visit' => $this->type_visit,
                'tooth' => $this->tooth,
                'seq' => $this->seq,
                'clinical_radiographic_findings' => $this->clinical_radiographic_findings,
                'department' => $this->department,
                'treatment_options' => $this->treatment_options,
                'faculty_signature' => $this->faculty_signature,
                'status' => $this->status,
            ]
        );

        session()->flash('message', $this->editId ? 'Updated successfully!' : 'Saved successfully!');

        $this->reset(['tooth', 'seq', 'clinical_radiographic_findings', 'department', 'treatment_options', 'faculty_signature', 'status', 'editId']);
    }

    public function edit($id)
    {
        $plan = MDTreatmentPlanDental::findOrFail($id);
        $this->editId = $plan->id;
        $this->tooth = $plan->tooth;
        $this->seq = $plan->seq;
        $this->clinical_radiographic_findings = $plan->clinical_radiographic_findings;
        $this->department = $plan->department;
        $this->treatment_options = $plan->treatment_options;
        $this->faculty_signature = $plan->faculty_signature;
        $this->status = $plan->status;
        $this->tab = 'form';
    }

    public function delete($id)
    {
        MDTreatmentPlanDental::findOrFail($id)->delete();
        session()->flash('message', 'Deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $plan = MDTreatmentPlanDental::findOrFail($id);
        $plan->status = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->save();
        session()->flash('message', 'Status updated!');
    }

    public function render()
    {
        $previousPlans = MDTreatmentPlanDental::where('patient_id', $this->patient_id)->latest()->get();
        return view('livewire.dental.treatment-plans-dental', compact('previousPlans'));
    }
}