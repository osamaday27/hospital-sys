<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_kreading;

class KReadings extends Component
{
    use WithPagination;

    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $od_k1_number, $od_k1_axis= '0', $od_k2_number, $od_k2_axis= '0', $od_comment;
    public $os_k1_number, $os_k1_axis= '0', $os_k2_number, $os_k2_axis= '0', $os_comment;
     public $axis_options = [];
    public $editingId = null,
        $search = '',
        $sortColumn = 'created_at',
        $sortDirection = 'desc';

    public $showDetailsModal = false,
        $showDeleteModal = false,
        $selectedRecord;
    protected $rules = [
        'id_patient' => 'required',
        'id_reservation' => 'required',
        'id_visit' => 'required',
        'type_visit' => 'required',
        'od_k1_number' => 'required|numeric',
        'od_k1_axis' => 'required|numeric',
        'od_k2_number' => 'required|numeric',
        'od_k2_axis' => 'required|numeric',
        'os_k1_number' => 'required|numeric',
        'os_k1_axis' => 'required|numeric',
        'os_k2_number' => 'required|numeric',
        'os_k2_axis' => 'required|numeric',
        'od_comment' => 'nullable|string|max:255',
        'os_comment' => 'nullable|string|max:255',
    ];

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
        $this->generateAxisOptions();
    }
    public function generateAxisOptions()
    {
        $values = [];

        for ($i = 0; $i <= 180; $i += 5) {
            $values[] = $i;
        }

        $this->axis_options = array_map(function ($val) {
            return str_pad($val, 3, '0', STR_PAD_LEFT); // مثل: 005 - 010 - 180
        }, $values);
    }
    public function save()
    {
        $this->validate();

        $data = [
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'od_k1_number' => $this->od_k1_number,
            'od_k1_axis' => $this->od_k1_axis,
            'od_k2_number' => $this->od_k2_number,
            'od_k2_axis' => $this->od_k2_axis,
            'os_k1_number' => $this->os_k1_number,
            'os_k1_axis' => $this->os_k1_axis,
            'os_k2_number' => $this->os_k2_number,
            'os_k2_axis' => $this->os_k2_axis,
            'od_comment' => $this->od_comment,
            'os_comment' => $this->os_comment,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_kreading::find($this->editingId)->update($data);
        } else {
            m_d_kreading::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_kreading::find($id);
        $this->editingId = $id;
        $this->fill($record->toArray());
    }

    public function resetInput()
    {
        $this->reset(['od_k1_number', 'od_k1_axis', 'od_k2_number', 'od_k2_axis', 'os_k1_number', 'os_k1_axis', 'os_k2_number', 'os_k2_axis', 'od_comment', 'os_comment', 'editingId']);
    }
    public function viewDetails($id)
    {
        $this->selectedRecord = m_d_kreading::find($id);
        $this->showDetailsModal = true;
    }
    public function confirmDelete($id)
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRecord()
    {
        m_d_kreading::find($this->editingId)->delete();
        $this->showDeleteModal = false;
    }

    public function render()
    {
        return view('livewire.k-readings', [
            'records' => m_d_kreading::where('id_patient',  $this->id_patient)
                            ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}