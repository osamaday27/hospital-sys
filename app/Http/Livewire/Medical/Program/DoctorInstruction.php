<?php

namespace App\Http\Livewire\Medical\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Medical\MddInstruction;
use Illuminate\Support\Facades\Auth;

class DoctorInstruction extends Component
{
    use WithPagination;

    // حقول الإضافة / التعديل
    public $editId;
    public $patient_id;
    public $visit_id;
    public $reservation_id;
    public $instruction;
    public $importance       = 'medium';
    public $time_specific    = false;
    public $scheduled_at;
    public $repeat_count     = 1;
    public $repeat_interval;
    public $on_repeat_action = 'duplicate';
    public $notes;

    // فلترة بحث وفلاتر العرض
    public $searchTerm        = '';
    public $filterStatus      = '';
    public $filterImportance  = '';

    protected $rules = [
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

    // إعادة الترقيم عند تغيير الفلاتر أو البحث
    public function updatingSearchTerm()        { $this->resetPage(); }
    public function updatingFilterStatus()      { $this->resetPage(); }
    public function updatingFilterImportance()  { $this->resetPage(); }

    public function mount($patientId = null, $visitId = null, $reservationId = null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }
    

    public function submit()
    {
        $this->validate();

        $data = [
            'patient_id'      => $this->patient_id,
            'visit_id'        => $this->visit_id,
            'reservation_id'  => $this->reservation_id,
            'doctor_id'       => Auth::id(),
            'nurse_id'        => Auth::id(),
            'instruction'     => $this->instruction,
            'importance'      => $this->importance,
            'status'          => 'pending',
            'time_specific'   => $this->time_specific,
            'scheduled_at'    => $this->scheduled_at,
            'repeat_count'    => $this->repeat_count,
            'repeat_interval' => $this->repeat_interval,
            'on_repeat_action'=> $this->on_repeat_action,
            'notes'           => $this->notes,
        ];

        if ($this->editId) {
            MddInstruction::findOrFail($this->editId)->update($data);
            session()->flash('message', 'تم تعديل التعليمة بنجاح');
            $this->editId = null;
        } else {
            MddInstruction::create($data);
            session()->flash('message', 'تم إنشاء التعليمة بنجاح');
        }

        $this->reset([
            'instruction',
            'importance',
            'time_specific',
            'scheduled_at',
            'repeat_count',
            'repeat_interval',
            'on_repeat_action',
            'notes'
        ]);

        $this->emit('refreshList');
    }

    public function edit($id)
    {
        $ins = MddInstruction::findOrFail($id);

        if ($ins->status !== 'pending') {
            session()->flash('error', 'لا يمكن تعديل هذه التعليمة');
            return;
        }

        $this->editId           = $ins->id;
        $this->instruction      = $ins->instruction;
        $this->importance       = $ins->importance;
        $this->time_specific    = $ins->time_specific;
        $this->scheduled_at     = $ins->scheduled_at;
        $this->repeat_count     = $ins->repeat_count;
        $this->repeat_interval  = $ins->repeat_interval;
        $this->on_repeat_action = $ins->on_repeat_action;
        $this->notes            = $ins->notes;
    }

    public function delete($id)
    {
        $ins = MddInstruction::findOrFail($id);

        if ($ins->status !== 'pending') {
            session()->flash('error', 'لا يمكن حذف هذه التعليمة');
            return;
        }

        $ins->delete();
        session()->flash('message', 'تم حذف التعليمة');
        $this->emit('refreshList');
    }

    public function render()
    {
        $query = MddInstruction::query();

        // بحث نصي في التعليمة
        if ($this->searchTerm) {
            $query->where('instruction', 'like', '%' . $this->searchTerm . '%');
        }

        // فلترة الحالة
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // فلترة الأهمية
        if ($this->filterImportance) {
            $query->where('importance', $this->filterImportance);
        }

        $instructions = $query
            ->orderBy('created_at', 'desc')
            -> where('patient_id', $this->patient_id)
            ->paginate(10);

        return view('livewire.medical.program.doctor-instruction', [
            'instructions' => $instructions,
        ]);
    }
}