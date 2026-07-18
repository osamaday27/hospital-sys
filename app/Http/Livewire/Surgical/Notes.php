<?php
namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\models\administrative\surgicalnote;

class Notes extends Component
{
    public $surgery_booking_id;
    public $note;

    public function save()
    {
        $this->validate([
            'note' => 'required|string|max:1000'
        ]);

        surgicalnote::create([
            'surgery_booking_id' => $this->surgery_booking_id,
            'note' => $this->note,
            'user_id' => Auth::id(),
        ]);

        $this->reset('note');
        session()->flash('message', 'تمت إضافة الملاحظة بنجاح.');
    }

    public function delete($id)
    {
        $note = surgicalnote::findOrFail($id);
        $note->delete();
    }

    public function render()
    {
        $notes = surgicalnote::where('surgery_booking_id', $this->surgery_booking_id)
                    ->latest()
                    ->get();

        return view('livewire.surgical.notes', compact('notes'));
    }
}