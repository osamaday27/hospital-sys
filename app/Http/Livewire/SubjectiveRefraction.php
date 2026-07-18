<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_subjectiverefraction;

class SubjectiveRefraction extends Component
{
     use WithPagination;

    public $refraction_type, $od_ucva, $od_near_uava, $od_near_bcva;
    public $os_ucva, $os_near_uava, $os_near_bcva;
    public $ou_ucva, $ou_near_uava, $ou_bcva, $ou_near_bcva, $ipd, $od_comment, $os_comment, $created_by;
    public $editingId = null, $search = '', $sortColumn = 'created_at', $sortDirection = 'desc';
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $showDetailsModal = false, $showDeleteModal = false, $selectedRecord;
    public $os_sph = '0', $os_cyl = '0', $os_axis = '0',$os_near_add = '0', $os_bcva, $od_bcva;
    public $od_sph = '0'; // القيمة الافتراضية
    public $sph_options = [];
    public $od_cyl = '0'; // القيمة الافتراضية
    public $cyl_options = [];
    public $od_axis = '0'; // القيمة الافتراضية
    public $axis_options = [];
    public $od_near_add = '0'; // القيمة الافتراضية
    public $near_add_options = [];
    protected $rules = [
        'refraction_type' => 'required|string|max:255',
        'od_sph' => 'nullable|string|max:10',
        'os_sph' => 'nullable|string|max:10',
        'od_cyl' => 'nullable|string|max:10',
        'os_cyl' => 'nullable|string|max:10',
        'od_axis' => 'nullable|string|max:10',
        'os_axis' => 'nullable|string|max:10',
        'od_near_add' => 'nullable|string|max:10',
        'os_near_add' => 'nullable|string|max:10',
        'od_ucva' => 'nullable|string|max:10',
        'os_ucva' => 'nullable|string|max:10',
        'ou_ucva' => 'nullable|string|max:10',
        'od_bcva' => 'nullable|string|max:10',
        'os_bcva' => 'nullable|string|max:10',
        'ou_bcva' => 'nullable|string|max:10',
        'ipd' => 'nullable|string|max:10',
        'od_comment' => 'nullable|string',
        'os_comment' => 'nullable|string',
    ];

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }
  public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
       $this->od_sph = '0';
        // ← القيمة الافتراضية
        $this->generateCylOptions();
        $this->generateSphOptions();
        $this->generateAxisOptions();
        $this->generateNearAddOptions();
    }
    public function generateNearAddOptions()
    {
        $values = [];

        for ($i = 0; $i <= 5; $i += 0.25) {
            $values[] = number_format($i, 2);
        }

        $this->near_add_options = $values;
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
    public function generateSphOptions()
    {
        $negatives = [];
        $positives = [];

        for ($i = 0.25; $i <= 8; $i += 0.25) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 8.5; $i <= 20; $i += 0.5) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 21; $i <= 45; $i++) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        sort($positives); // ترتيب تنازلي
        sort($negatives); // ترتيب تصاعدي

        $values = array_merge(array_map(fn($val) => number_format($val, 2), $negatives), ['0'], array_map(fn($val) => '+' . number_format($val, 2), $positives));

        $this->sph_options = $values;
    }

    public function generateCylOptions()
    {
        $negatives = [];
        $positives = [];

        for ($i = 0.25; $i <= 6; $i += 0.25) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 6.5; $i <= 11; $i += 0.5) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 12; $i <= 15; $i++) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        sort($positives); // ترتيب تنازلي
        sort($negatives); // ترتيب تصاعدي

        $values = array_merge(array_map(fn($val) => number_format($val, 2), $negatives), ['0'], array_map(fn($val) => '+' . number_format($val, 2), $positives));

        $this->cyl_options = $values;
    }

    public function save()
    {
        $this->validate();

        $data = [
             'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'refraction_type' => $this->refraction_type,
            'od_sph' => $this->od_sph,
            'os_sph' => $this->os_sph,
            'od_cyl' => $this->od_cyl,
            'os_cyl' => $this->os_cyl,
            'od_axis' => $this->od_axis,
            'os_axis' => $this->os_axis,
            'od_near_add' => $this->od_near_add,
            'os_near_add' => $this->os_near_add,
            'ou_ucva' => $this->ou_ucva,
            'od_ucva' => $this->od_ucva,
            'os_ucva' => $this->os_ucva,
            'ou_bcva' => $this->ou_bcva,
            'od_bcva' => $this->od_bcva,
            'os_bcva' => $this->os_bcva,
            'ou_near_bcva' => $this->ou_near_bcva,
            'od_near_bcva' => $this->od_near_bcva,
            'os_near_bcva' => $this->os_near_bcva,
            'ou_near_uava' => $this->ou_near_uava,
            'od_near_uava' => $this->od_near_uava,
            'os_near_uava' => $this->os_near_uava,
            'ipd' => $this->ipd,
            'od_comment' => $this->od_comment,
            'os_comment' => $this->os_comment,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_subjectiverefraction::find($this->editingId)->update($data);
        } else {
            m_d_subjectiverefraction::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_subjectiverefraction::find($id);
        $this->editingId = $id;
        $this->fill($record->toArray());
    }

    public function resetInput()
    {
        $this->reset(['refraction_type', 'od_sph', 'os_sph', 'od_cyl', 'os_cyl', 'od_axis', 'os_axis',
            'od_near_add', 'os_near_add', 'od_ucva', 'os_ucva', 'ou_ucva', 'od_bcva', 'os_bcva', 'ou_bcva',
            'ipd', 'od_comment', 'os_comment', 'editingId']);
    }

    public function confirmDelete($id)
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRecord()
    {
        m_d_subjectiverefraction::find($this->editingId)->delete();
        $this->showDeleteModal = false;
    }

    public function viewDetails($id)
    {
        $this->selectedRecord = m_d_subjectiverefraction::find($id);
        $this->showDetailsModal = true;
    }

    public function render()
    {
        return view('livewire.subjective-refraction', [
            'records' => m_d_subjectiverefraction::where('refraction_type', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}