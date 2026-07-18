<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_oph_medication;

class MDOphMedication extends Component
{
    use WithPagination;

    public $anticoag_antiplat = 'No', $medication_details;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'anticoag_antiplat' => 'required|string|max:10',
        'medication_details' => 'nullable|string|max:255',
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
            'anticoag_antiplat' => $this->anticoag_antiplat,
            'medication_details' => $this->medication_details,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_oph_medication::find($this->editingId)->update($data);
        } else {
            m_d_oph_medication::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_oph_medication::findOrFail($id);
        $this->editingId = $id;
        $this->anticoag_antiplat = $record->anticoag_antiplat;
        $this->medication_details = $record->medication_details;
    }

    public function delete($id)
    {
        $record = m_d_oph_medication::findOrFail($id);
        $record->delete();
        session()->flash('message', 'Record deleted successfully.');
    }

    public function resetInput()
    {
        $this->anticoag_antiplat = 'No';
        $this->medication_details = '';
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
        return view('livewire.m-d-oph-medication', [
            'medication_records' => m_d_oph_medication::where('medication_details', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}