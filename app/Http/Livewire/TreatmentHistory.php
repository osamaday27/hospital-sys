<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\mdtreatmenthistory;

class TreatmentHistory extends Component
{
    use WithPagination;

    public $drug,
        $eye = 'OD',
        $forNumber,
        $period = 'D',
        $dose,
        $remarks;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'drug' => 'required|string|max:255',
        'eye' => 'required|string|max:10',
        'forNumber' => 'nullable|integer|min:1',
        'period' => 'required|string|max:2',
        'dose' => 'required|string|max:255',
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
            'drug' => $this->drug,
            'eye' => $this->eye,
            'forNumber' => $this->forNumber,
            'period' => $this->period,
            'dose' => $this->dose,
            'remarks' => $this->remarks,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            mdtreatmenthistory::find($this->editingId)->update($data);
        } else {
            mdtreatmenthistory::create($data);
        }

        $this->resetInput();
    }

    public function resetInput()
    {
        $this->drug = '';
        $this->eye = 'OD';
        $this->forNumber = null;
        $this->period = 'D';
        $this->dose = '';
        $this->remarks = '';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.treatment-history', [
            'treatments' => mdtreatmenthistory::where('drug', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
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