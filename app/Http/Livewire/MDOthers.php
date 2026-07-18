<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_other;

class MDOthers extends Component
{
    use WithPagination;

    public $others, $remarks;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'others' => 'required|string|max:255',
        'remarks' => 'nullable|string',
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
            'others' => $this->others,
            'remarks' => $this->remarks,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_other::find($this->editingId)->update($data);
        } else {
            m_d_other::create($data);
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

    public function edit($id)
    {
        $record = m_d_other::findOrFail($id);
        $this->editingId = $id;
        $this->others = $record->others;
        $this->remarks = $record->remarks;
    }

    public function delete($id)
    {
        $record = m_d_other::findOrFail($id);
        $record->delete();
        session()->flash('message', 'Record deleted successfully.');
    }

    public function resetInput()
    {
        $this->others = '';
        $this->remarks = '';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.m-d-others', [
            'others_records' => m_d_other::where('others', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}