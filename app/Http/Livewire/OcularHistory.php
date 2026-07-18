<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_ocularhistory;
use App\models\medical\ms_diseases_list;

class OcularHistory extends Component
{
    use WithPagination;

    public $ocular_disease, $ocular_relationship, $ocular_remark;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $suggestions_disease = [];

    public function updatedOcularDisease()
    {
        if (!empty($this->ocular_disease)) {
            $this->suggestions_disease = ms_diseases_list::where('name', 'like', '%' . $this->ocular_disease . '%')
                ->limit(10)
                ->pluck('name')
                ->toArray();
        } else {
            $this->suggestions_disease = [];
        }
    }

    public function selectDisease($disease)
    {
        $this->ocular_disease = $disease;
        $this->suggestions_disease = [];
    }
    protected $rules = [
        'ocular_disease' => 'required|string|max:255',
        'ocular_relationship' => 'nullable|string|max:255',
        'ocular_remark' => 'nullable|string',
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
            'ocular_disease' => $this->ocular_disease,
            'ocular_relationship' => $this->ocular_relationship,
            'ocular_remark' => $this->ocular_remark,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_ocularhistory::find($this->editingId)->update($data);
        } else {
            m_d_ocularhistory::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_ocularhistory::findOrFail($id);
        $this->editingId = $id;
        $this->ocular_disease = $record->ocular_disease;
        $this->ocular_relationship = $record->ocular_relationship;
        $this->ocular_remark = $record->ocular_remark;
    }

    public function delete($id)
    {
        $record = m_d_ocularhistory::findOrFail($id);
        $record->delete();
        session()->flash('message', 'Record deleted successfully.');
    }

    public function resetInput()
    {
        $this->ocular_disease = '';
        $this->ocular_relationship = '';
        $this->ocular_remark = '';
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
        return view('livewire.ocular-history', [
            'ocular_records' => m_d_ocularhistory::where('ocular_disease', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}