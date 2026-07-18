<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\mbdiseaseshispat;

class OphthalmologyHistory extends Component
{
    use WithPagination;

    public $diagnosis;
    public $eye = 'OD';
    public $for_number = 1;
    public $for_period = 'D';
    public $remarks;
    public $editingId = null;
    public $id_patient;
    public $id_reservation;
    public $id_visit;
    public $type_visit;
    protected $rules = [
        'diagnosis' => 'required|string|max:255',
        'eye' => 'required|string|max:10',
        'for_number' => 'required|integer|min:1',
        'for_period' => 'required|string|max:2',
        'remarks' => 'nullable|string',
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

        if ($this->editingId) {
            $record = mbdiseaseshispat::find($this->editingId);
            if ($record) {
                $record->update($this->getData());
            }
            $this->editingId = null;
        } else {
            mbdiseaseshispat::create($this->getData());
        }

        $this->resetInput();
        $this->dispatchBrowserEvent('historyUpdated');
    }

    public function edit($id)
    {
        $record = mbdiseaseshispat::find($id);
        if ($record) {
            $this->editingId = $id;
            $this->diagnosis = $record->diseases;
            $this->eye = $record->eye;
            $this->for_number = $record->for_number;
            $this->for_period = $record->for_period;
            $this->remarks = $record->remarks;
        }
    }

    public function delete($id)
    {
        mbdiseaseshispat::find($id)?->delete();
        $this->dispatchBrowserEvent('historyUpdated');
    }

    private function getData()
    {
        return [
            'diseases' => $this->diagnosis,
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,  
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'eye' => $this->eye,
            'for_number' => $this->for_number,
            'for_period' => $this->for_period,
            'remarks' => $this->remarks,
            'user_id' => Auth::id(),
        ];
    }

    public function resetInput()
    {
        $this->diagnosis = '';
        $this->eye = 'OD';
        $this->for_number = 1;
        $this->for_period = 'D';
        $this->remarks = '';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.ophthalmology-history', [
            'histories' => mbdiseaseshispat::where('id_patient', $this->id_patient)
                ->orderBy('created_at', 'desc')
                ->
            latest()->paginate(10),
        ]);
    }
}