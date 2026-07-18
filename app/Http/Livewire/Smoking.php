<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_smoking;

class Smoking extends Component
{
    use WithPagination;

    public $smoking_status = 'No', $smoking_type, $smoking_packs, $smoking_duration, $smoking_duration_unit = 'New';
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'smoking_status' => 'required|string|max:20',
        'smoking_type' => 'nullable|string|max:50',
        'smoking_packs' => 'nullable|integer|min:0',
        'smoking_duration' => 'nullable|integer|min:0',
        'smoking_duration_unit' => 'required|string|max:10',
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
            'smoking_status' => $this->smoking_status,
            'smoking_type' => $this->smoking_type,
            'smoking_packs' => $this->smoking_packs,
            'smoking_duration' => $this->smoking_duration,
            'smoking_duration_unit' => $this->smoking_duration_unit,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_smoking::find($this->editingId)->update($data);
        } else {
            m_d_smoking::create($data);
        }

        $this->resetInput();
    }
public function edit($id)
{
    $record = m_d_smoking::findOrFail($id);
    $this->editingId = $id;
    $this->smoking_status = $record->smoking_status;
    $this->smoking_type = $record->smoking_type;
    $this->smoking_packs = $record->smoking_packs;
    $this->smoking_duration = $record->smoking_duration;
    $this->smoking_duration_unit = $record->smoking_duration_unit;
}

public function delete($id)
{
    $record = m_d_smoking::findOrFail($id);
    $record->delete();
    session()->flash('message', 'Record deleted successfully.');
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
        $this->smoking_status = 'No';
        $this->smoking_type = null;
        $this->smoking_packs = null;
        $this->smoking_duration = null;
        $this->smoking_duration_unit = 'New';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.smoking', [
            'smoking_records' => m_d_smoking::where('smoking_status', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orWhere('smoking_type', 'like', '%' . $this->search . '%')
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}