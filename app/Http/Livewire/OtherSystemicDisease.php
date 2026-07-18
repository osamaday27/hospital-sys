<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_othersystemicdisease;

class OtherSystemicDisease extends Component
{
    use WithPagination;

    public $disease, $duration, $duration_unit = 'New', $remark;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'disease' => 'required|string|max:255',
        'duration' => 'nullable|integer|min:0',
        'duration_unit' => 'required|string|max:10',
        'remark' => 'nullable|string',
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
            'disease' => $this->disease,
            'duration' => $this->duration,
            'duration_unit' => $this->duration_unit,
            'remark' => $this->remark,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_othersystemicdisease::find($this->editingId)->update($data);
        } else {
            m_d_othersystemicdisease::create($data);
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
        $this->disease = '';
        $this->duration = null;
        $this->duration_unit = 'New';
        $this->remark = '';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.other-systemic-disease', [
            'disease_records' => m_d_othersystemicdisease::where('disease', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}