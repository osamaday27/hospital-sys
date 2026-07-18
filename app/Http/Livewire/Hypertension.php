<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_hypertension;


class Hypertension extends Component
{
    use WithPagination;

    public $controlled = 'Yes', $duration, $duration_unit = 'New';
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'controlled' => 'required|string|max:10',
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
            'controlled' => $this->controlled,
            'duration' => $this->duration,
            'duration_unit' => $this->duration_unit,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_hypertension::find($this->editingId)->update($data);
        } else {
            m_d_hypertension::create($data);
        }

        $this->resetInput();
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

    public function resetInput()
    {
        $this->controlled = 'Yes';
        $this->duration = null;
        $this->duration_unit = 'New';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.hypertension', [
            'htn_records' => m_d_hypertension::where('controlled', 'like', '%' . $this->search . '%')
            ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}