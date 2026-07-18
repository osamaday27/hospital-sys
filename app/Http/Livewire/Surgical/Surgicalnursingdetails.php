<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use App\models\administrative\surgerystatushistory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Surgicalnursingdetails extends Component
{
    public $booking;
    public $statusHistory;
    public $selectedStatus;

    public $statusChangeDate;
    public $statusChangeTime;
    public $statusNote;
    public function mount($booking)
    {
        $this->booking = $booking;
        $this->statusHistory = surgerystatushistory::where('surgery_booking_id', $booking->id)->orderBy('status_changed_at', 'desc')->get();
    }

  public function changeStatus()
    {
        if ($this->selectedStatus) {
            $this->booking->status = $this->selectedStatus;
            $this->booking->save();

            $datetime = Carbon::now();

            if ($this->statusChangeDate && $this->statusChangeTime) {
                $datetime = Carbon::parse("{$this->statusChangeDate} {$this->statusChangeTime}");
            }

            SurgeryStatusHistory::create([
                'surgery_booking_id' => $this->booking->id,
                'status' => $this->selectedStatus,
                'note' => $this->statusNote ?? 'تم تعديل الحالة من الواجهة.',
                'status_changed_at' => $datetime,
                'changed_by' => auth()->id(),
            ]);

            $this->statusHistory = SurgeryStatusHistory::where('surgery_booking_id', $this->booking->id)
                ->orderBy('status_changed_at', 'desc')
                ->get();

            $this->statusChangeDate = null;
            $this->statusChangeTime = null;
            $this->statusNote = null;

            session()->flash('message', trans('surgical_trans.status_updated'));
        }
    }

    public function render()
    {
        return view('livewire.surgical.surgicalnursingdetails');
    }
}