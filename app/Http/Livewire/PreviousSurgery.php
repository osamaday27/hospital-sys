<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\mdprevioussurgery;
use App\models\Finance\Service;

class PreviousSurgery extends Component
{
    use WithPagination;

    public $surgery,
        $eye = 'OD',
        $forNumber,
        $period = 'D',
        $remarks;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $surgery_options = [];
    protected $rules = [
        'surgery' => 'required|string|max:255',
        'eye' => 'required|string|max:10',
        'forNumber' => 'nullable|integer|min:1',
        'period' => 'required|string|max:2',
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
        $this->loadSurgeryOptions();
    }
    public function loadSurgeryOptions()
    {
        $locale = app()->getLocale(); // أو ثبّت 'en' مثلاً
        $this->surgery_options = service::whereHas('sublevel.level1', function ($query) {
            $query->where('level_type', 'operations');
        })
            ->pluck("name") // استخراج الاسم حسب اللغة
            ->filter() // حذف null
            ->unique()
            ->values()
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'surgery' => $this->surgery,
            'eye' => $this->eye,
            'forNumber' => $this->forNumber,
            'period' => $this->period,
            'remarks' => $this->remarks,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            mdprevioussurgery::find($this->editingId)->update($data);
        } else {
            mdprevioussurgery::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = mdprevioussurgery::find($id);
        $this->editingId = $id;
        $this->surgery = $record->surgery;
        $this->eye = $record->eye;
        $this->forNumber = $record->forNumber;
        $this->period = $record->period;
        $this->remarks = $record->remarks;
    }

    public function resetInput()
    {
        $this->surgery = '';
        $this->eye = 'OD';
        $this->forNumber = null;
        $this->period = 'D';
        $this->remarks = '';
        $this->editingId = null;
    }

    public function delete($id)
    {
        mdprevioussurgery::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.previous-surgery', [
            'histories' => mdprevioussurgery::where('surgery', 'like', '%' . $this->search . '%')
                ->where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}