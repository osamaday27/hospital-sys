<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_allergy;

class Allergy extends Component
{
    use WithPagination;

    public $drug_allergy, $food_allergy;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $showTrashed = false; // عرض السجلات المحذوفة

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'drug_allergy'    => 'nullable|string|max:255',
        'food_allergy'    => 'nullable|string|max:255',
        'id_patient'      => 'required',
        'id_reservation'  => 'required',
        'type_visit'      => 'required',
        'id_visit'        => 'required',
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
            'id_patient'     => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit'       => $this->id_visit,
            'type_visit'     => $this->type_visit,
            'drug_allergy'   => $this->drug_allergy,
            'food_allergy'   => $this->food_allergy,
            'created_by'     => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_allergy::withTrashed()->find($this->editingId)?->update($data);
        } else {
            m_d_allergy::create($data);
        }

        $this->resetInput();
        session()->flash('message', __('Saved successfully'));
    }

    public function edit($id)
    {
        $record = m_d_allergy::withTrashed()->find($id);
        if (!$record) return;

        $this->editingId    = $record->id;
        $this->drug_allergy = $record->drug_allergy;
        $this->food_allergy = $record->food_allergy;
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetInput()
    {
        $this->drug_allergy = '';
        $this->food_allergy = '';
        $this->editingId = null;
    }

    // ====== أوامر مباشرة بدون مودال ======
    public function deleteRecord($id)
    {
        m_d_allergy::find($id)?->delete(); // Soft delete
        session()->flash('message', __('Deleted successfully'));
        $this->resetPage();
    }

    public function restoreRecord($id)
    {
        m_d_allergy::withTrashed()->find($id)?->restore();
        session()->flash('message', __('Restored successfully'));
        $this->resetPage();
    }

    public function forceDeleteRecord($id)
    {
        m_d_allergy::withTrashed()->find($id)?->forceDelete();
        session()->flash('message', __('Permanently deleted'));
        $this->resetPage();
    }

    public function render()
    {
        $query = m_d_allergy::query()
            ->when($this->showTrashed, fn($q) => $q->onlyTrashed())
            ->where('id_patient', $this->id_patient)
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('drug_allergy', 'like', "%{$this->search}%")
                       ->orWhere('food_allergy', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortColumn, $this->sortDirection);

        return view('livewire.allergy', [
            'allergy_records' => $query->paginate(10),
        ]);
    }
}