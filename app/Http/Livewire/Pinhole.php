<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_pinhole;

class Pinhole extends Component
{
    use WithPagination;

    public $id_patient, $id_visit, $id_reservation, $type_visit;
    public $od_scale, $os_scale, $od_comment, $os_comment;
    public $editingId = null, $search = '', $sortColumn = 'created_at', $sortDirection = 'desc';
    public $showDetailsModal = false, $showDeleteModal = false, $selectedRecord;

    protected $rules = [
        'id_patient' => 'required',
        'id_visit' => 'required',
        'id_reservation' => 'required',
        'type_visit' => 'required',
        'od_scale' => 'nullable|string|max:10',
        'os_scale' => 'nullable|string|max:10',
        'od_comment' => 'nullable|string',
        'os_comment' => 'nullable|string',
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
            'od_scale' => $this->od_scale,
            'os_scale' => $this->os_scale,
            'od_comment' => $this->od_comment,
            'os_comment' => $this->os_comment,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_pinhole::find($this->editingId)->update($data);
        } else {
            m_d_pinhole::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_pinhole::find($id);
        $this->editingId = $id;
        $this->fill($record->toArray());
    }

    public function resetInput()
    {
        $this->reset(['od_scale', 'os_scale', 'od_comment', 'os_comment', 'editingId']);
    }

    public function confirmDelete($id)
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRecord()
    {
        m_d_pinhole::find($this->editingId)->delete();
        $this->showDeleteModal = false;
    }

    public function viewDetails($id)
    {
        $this->selectedRecord = m_d_pinhole::find($id);
        $this->showDetailsModal = true;
    }

    public function render()
    {
        return view('livewire.pinhole', [
            'records' => m_d_pinhole::where('type_visit', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}