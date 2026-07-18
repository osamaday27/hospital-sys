<?php

namespace App\Http\Livewire\Administrative;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\reservations;
use App\Models\administrative\AbsenceReason;
use Carbon\Carbon;

class NoShowReservations extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $cancel_reason_id, $cancel_note, $search = '', $date_from, $date_to;
    public $tab = 'pending'; // تبويب افتراضي

    protected $rules = [
        'cancel_reason_id' => 'required|exists:adm_s_absence_reasons,id',
        'cancel_note' => 'nullable|string|max:500',
    ];

    public function saveReason($id)
    {
        $this->validate();

        $reservation = reservations::findOrFail($id);
        $reservation->cancel_reason_id = $this->cancel_reason_id;
        $reservation->cancel_note = $this->cancel_note;
        $reservation->status = 'no_show';
        $reservation->save();

        $this->reset(['cancel_reason_id', 'cancel_note']);
        session()->flash('success', __('call_center_trans.reason_saved_successfully'));
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        $query = reservations::with(['patient', 'doctor', 'clinic', 'absenceReason'])
            ->whereNull('arrival_confirmed_at');

        if ($this->tab === 'pending') {
            $query->whereIn('status', ['cancelled']);
        } elseif ($this->tab === 'recorded') {
            $query->where('status', 'no_show');
        }

        if ($this->date_from && $this->date_to) {
            $query->whereBetween('date_visit', [$this->date_from, $this->date_to]);
        }

        if ($this->search) {
            $query->whereHas('patient', function ($q) {
                $q->where('full_name', 'like', "%{$this->search}%")
                  ->orWhere('phone_number', 'like', "%{$this->search}%");
            });
        }

        $reservations = $query->orderBy('date_visit', 'desc')->paginate(10);
        $reasons = AbsenceReason::where('status', '1')->get();

        return view('livewire.Administrative.no-show-reservations', compact('reservations', 'reasons'))
            ->extends('layouts.masterliveware')
            ->section('content');
    }
}