<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_diabetes_mellitus;

class DiabetesMellitus extends Component
{
    use WithPagination;

    public $type = 'I',
        $controlled = 'Yes',
        $medication = 'Insulin',
        $hba1c,
        $duration,
        $duration_unit = 'New';
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'type' => 'required|string|max:10',
        'controlled' => 'required|string|max:10',
        'medication' => 'required|string|max:20',
        'hba1c' => 'nullable|numeric|min:0|max:20',
        'duration' => 'nullable|integer|min:0',
        'duration_unit' => 'required|string|max:10',
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
            'type' => $this->type,
            'controlled' => $this->controlled,
            'medication' => $this->medication,
            'hba1c' => $this->hba1c,
            'duration' => $this->duration,
            'duration_unit' => $this->duration_unit,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_diabetes_mellitus::find($this->editingId)->update($data);
        } else {
            m_d_diabetes_mellitus::create($data);
        }

        $this->resetInput();
    }

    public function resetInput()
    {
        $this->type = 'I';
        $this->controlled = 'Yes';
        $this->medication = 'Insulin';
        $this->hba1c = null;
        $this->duration = null;
        $this->duration_unit = 'New';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.diabetes-mellitus', [
            'dm_records' => m_d_diabetes_mellitus::where('type', 'like', '%' . $this->search . '%')
               ->where('id_patient', $this->id_patient)
                ->orWhere('medication', 'like', '%' . $this->search . '%')
                ->orWhere('controlled', 'like', '%' . $this->search . '%')
                
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
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
}