<?php

// ✅ Livewire Component
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\mbdiagnoseshispat;
use App\models\medical\ms_diseases_list;

class DiagnosisHistory extends Component
{
    use WithPagination;

    public $diagnosis, $eye = 'OD', $remarks;
    // ✅ flag للتشخيص
    public $diag_flag = 'initial';

    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '', $sortColumn = 'created_at', $sortDirection = 'desc';
    public $suggestions = [];

    protected $rules = [
        'diagnosis' => 'required|string|max:255',
        'eye' => 'required|string|max:10',
        'remarks' => 'nullable|string',
        'diag_flag' => 'required|in:initial,final', // ✅ التحقق من قيمة الفلاج
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

    public function updatedDiagnosis()
    {
        if (!empty($this->diagnosis)) {
            $this->suggestions = ms_diseases_list::where('name', 'like', "%{$this->diagnosis}%")
                ->pluck('name')
                ->unique()
                ->take(10)
                ->toArray();
        } else {
            $this->suggestions = [];
        }
    }

    public function selectDiagnosis($value)
    {
        $this->diagnosis = $value;
        $this->suggestions = [];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'diagnosis' => $this->diagnosis,
            'eye' => $this->eye,
            'remarks' => $this->remarks,
            'diag_flag' => $this->diag_flag, // ✅ تخزين الفلاج
            'user_id' => Auth::id(),
        ];

        if ($this->editingId) {
            mbdiagnoseshispat::find($this->editingId)->update($data);
        } else {
            mbdiagnoseshispat::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = mbdiagnoseshispat::find($id);
        $this->editingId = $id;
        $this->diagnosis = $record->diagnosis;
        $this->eye = $record->eye;
        $this->remarks = $record->remarks;
        $this->diag_flag = $record->diag_flag ?: 'initial'; // ✅ تحميل الفلاج مع السجل
    }

    public function delete($id)
    {
        $record = mbdiagnoseshispat::find($id);

        if ($record) {
            $record->delete();
            session()->flash('message', 'Diagnosis deleted successfully.');
        } else {
            session()->flash('error', 'Diagnosis not found.');
        }
    }

    public function resetInput()
    {
        $this->diagnosis = '';
        $this->eye = 'OD';
        $this->remarks = '';
        $this->diag_flag = 'initial'; // ✅ رجّع الافتراضي
        $this->editingId = null;
        $this->suggestions = [];
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
        return view('livewire.diagnosis-history', [
            'diagnoses' => mbdiagnoseshispat::where('diagnosis', 'like', "%{$this->search}%")
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}