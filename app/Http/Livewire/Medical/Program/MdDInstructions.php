<?php

namespace App\Http\Livewire\Medical\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\medical\mddinstruction;
use Illuminate\Support\Facades\Auth;

class MdDInstructions extends Component
{
    use WithPagination;

    // حقول النموذج
    public $patient_id;
    public $visit_id;
    public $reservation_id;
    public $instruction;
    public $importance = 'medium';
    public $time_specific = false;
    public $scheduled_at;
    public $repeat_count = 1;
    public $repeat_interval;
    public $on_repeat_action = 'duplicate';

    // للتعديل / الملاحظات
    public $selectedInstruction;
    public $notes;

    protected $rules = [
        'patient_id'       => 'required|exists:patients,id',
        'visit_id'         => 'nullable|exists:visits,id',
        'reservation_id'   => 'nullable|exists:reservations,id',
        'instruction'      => 'required|string',
        'importance'       => 'required|in:low,medium,high',
        'time_specific'    => 'boolean',
        'scheduled_at'     => 'nullable|date',
        'repeat_count'     => 'required|integer|min:1',
        'repeat_interval'  => 'nullable|string',
        'on_repeat_action' => 'required|in:duplicate,remind,skip',
        'notes'            => 'nullable|string',
    ];

    protected $listeners = ['refreshList' => '$refresh'];

    public function submit()
    {
        $data = $this->validate();
        $data['doctor_id'] = Auth::id();

        mddinstruction::create($data);

        session()->flash('message', 'تم إنشاء التعليمة بنجاح');
        $this->reset(['instruction','importance','time_specific','scheduled_at','repeat_count','repeat_interval','on_repeat_action']);
        $this->emit('refreshList');
    }

    public function markInProgress($id)
    {
        $ins = mddinstruction::findOrFail($id);
        $ins->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);
        session()->flash('message', 'تم بدء التنفيذ');
        $this->emit('refreshList');
    }

    public function markFinished($id)
    {
        $ins = mddinstruction::findOrFail($id);
        $ins->update([
            'status'       => 'finished',
            'completed_at' => now(),
        ]);
        session()->flash('message', 'تمت العملية بنجاح');
        $this->emit('refreshList');
    }

    public function openNotes($id)
    {
        $this->selectedInstruction = mddinstruction::findOrFail($id);
        $this->notes = $this->selectedInstruction->notes;
        $this->dispatchBrowserEvent('show-notes-modal');
    }

    public function saveNotes()
    {
        $this->validateOnly('notes');
        $this->selectedInstruction->update(['notes' => $this->notes]);
        session()->flash('message', 'تم حفظ الملاحظات');
        $this->dispatchBrowserEvent('hide-notes-modal');
        $this->emit('refreshList');
    }

    public function render()
    {
        $instructions = mddinstruction::where('id_patient', $this->id_patient)->
        orderBy('created_at','desc')->paginate(10);
        return view('livewire.medical.program.md-d-instructions', compact('instructions'));
    }
}