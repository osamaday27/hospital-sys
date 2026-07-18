<?php
// app/Http/Livewire/Notes/Create.php

namespace App\Http\Livewire\Notes;

use Livewire\Component;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $note;
    public $importance_level = 'medium';
    public $target_type;
    public $target_id;

    public $context_type;
    public $context_id;

    public $patient_id;
    public $reservation_id;
    public $visit_id;

    public $action_required = false;
    public $is_pinned = false;
    public $due_date;
    public $status = 'new';


public function mount(
    $target_type = null,
    $target_id = null,
    $context_type = null,
    $context_id = null,
    $patient_id = null,
    $reservation_id = null,
    $visit_id = null
) {
    $this->target_type    = $target_type;
    $this->target_id      = $target_id;
    $this->context_type   = $context_type;
    $this->context_id     = $context_id;
    $this->patient_id     = $patient_id;
    $this->reservation_id = $reservation_id;
    $this->visit_id       = $visit_id;
}

    protected $rules = [
        'note' => 'required|string',
        'importance_level' => 'required|in:low,medium,high',
        'target_type' => 'required|string',
        'target_id' => 'required|numeric',
        'context_type' => 'nullable|string',
        'context_id' => 'nullable|numeric',
        'patient_id' => 'nullable|exists:patients,id',
        'reservation_id' => 'nullable|exists:reservations,id',
        'visit_id' => 'nullable|exists:visits,id',
        'action_required' => 'boolean',
        'is_pinned' => 'boolean',
        'due_date' => 'nullable|date',
        'status' => 'required|in:new,done,ignored,rejected',
    ];

    public function save()
    {
        $this->validate();

        Note::create([
            'note' => $this->note,
            'importance_level' => $this->importance_level,
            'created_by' => Auth::id(),
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'context_type' => $this->context_type,
            'context_id' => $this->context_id,
            'patient_id' => $this->patient_id,
            'reservation_id' => $this->reservation_id,
            'visit_id' => $this->visit_id,
            'action_required' => $this->action_required,
            'is_pinned' => $this->is_pinned,
            'due_date' => $this->due_date,
            'status' => $this->status,
        ]);

        session()->flash('success', 'تم حفظ الملحوظة بنجاح');
        $this->reset();
        $this->dispatch('noteAdded');
    }

    public function render()
    {
        return view('livewire.notes.create');
    }
}