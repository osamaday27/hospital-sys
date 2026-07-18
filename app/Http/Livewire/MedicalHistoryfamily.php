<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_medicalhistoryfamily;

class medicalHistoryfamily extends Component
{
    use WithPagination;

    public $medical_disease, $medical_relationship, $medical_remark;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'medical_disease' => 'required|string|max:255',
        'medical_relationship' => 'nullable|string|max:255',
        'medical_remark' => 'nullable|string',
        'id_patient' => 'required',
        'id_reservation' => 'required',
        'type_visit' => 'required',
        'id_visit' => 'required',
    ];

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'medical_disease' => $this->medical_disease,
            'medical_relationship' => $this->medical_relationship,
            'medical_remark' => $this->medical_remark,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_medicalhistoryfamily::find($this->editingId)->update($data);
        } else {
            m_d_medicalhistoryfamily::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_medicalhistoryfamily::findOrFail($id);
        $this->editingId = $id;
        $this->medical_disease = $record->medical_disease;
        $this->medical_relationship = $record->medical_relationship;
        $this->medical_remark = $record->medical_remark;
    }

    public function delete($id)
    {
        $record = m_d_medicalhistoryfamily::findOrFail($id);
        $record->delete();
        session()->flash('message', 'Record deleted successfully.');
    }

    public function resetInput()
    {
        $this->medical_disease = '';
        $this->medical_relationship = '';
        $this->medical_remark = '';
        $this->editingId = null;
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        return view('livewire.medical-historyfamily', [
            'medical_records' => m_d_medicalhistoryfamily::where('medical_disease', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}