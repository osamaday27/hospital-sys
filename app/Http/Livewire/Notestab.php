<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\models\medical\medical_notes_patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class Notestab extends Component
{
    public $notes = [];
    public $newNote = '';
    public $editIndex = null;
    public $editNote = '';
    public $id_patient, $id_reservation, $id_visit, $type_visit;

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
        $this->loadNotes();
    }

    public function loadNotes()
    {
        $this->notes = medical_notes_patient::where('id_patient', $this->id_patient)
           
        
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'notes' => $note->notes,
                    'date' => $note->date ?? $note->created_at->format('Y-m-d H:i'),
                    'user' => optional($note->user)->name,
                    'user_id' => $note->user_id
                ];
            })
            ->toArray();
    }

    public function addNote()
    {
        $this->validate([
            'newNote' => 'required|string|max:1000',
        ]);

        medical_notes_patient::create([
            'id_patient' => $this->id_patient,
            'id_visit' => $this->id_visit,
            'id_reservation' => $this->id_reservation,
            'type_visit' => $this->type_visit,
            'user_id' => Auth::id(),
            'notes' => $this->newNote,
            'date' => now(),
            'status' => 'active',
        ]);

        $this->newNote = '';
        $this->loadNotes();
    }

    public function editNote($index)
    {
        $this->editIndex = $index;
        $this->editNote = $this->notes[$index]['notes'] ?? '';
    }

    public function updateNote()
    {
        $noteId = $this->notes[$this->editIndex]['id'] ?? null;

        if ($noteId) {
            $note = medical_notes_patient::find($noteId);
            if ($note && $note->user_id == Auth::id()) {
                $note->notes = $this->editNote;
                $note->save();
                $this->editIndex = null;
                $this->editNote = '';
                $this->loadNotes();
            }
        }
    }

    public function deleteNote($index)
    {
        $noteId = $this->notes[$index]['id'] ?? null;

        if ($noteId) {
            $note = medical_notes_patient::find($noteId);
            if ($note && $note->user_id == Auth::id()) {
                $note->delete();
                $this->loadNotes();
            }
        }
    }

    public function render()
    {
        return view('livewire.notestab');
    }
}