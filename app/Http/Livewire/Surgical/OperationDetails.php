<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Administrative\SurgeryStatusHistory;
use App\models\administrative\adm_b_surgery_bookings; // نفس النيمسبيس اللي بتستخدمه
use Carbon\Carbon;

class OperationDetails extends Component
{
    public $booking;
    public $statusHistory;
    public $selectedStatus;

    public $statusChangeDate;
    public $statusChangeTime;
    public $statusNote;

    /**
     * يقبل إما كائن Booking جاهز أو رقم ID
     */
    public function mount($booking)
    {
        if ($booking instanceof adm_b_surgery_bookings) {
            $this->booking = $booking->load([
                'services',     // ✅ الأساس
                'patient',
                'room',
            ]);
        } else {
            $this->booking = adm_b_surgery_bookings::with([
                'services',
                'patient',
                'room',
            ])->findOrFail($booking);
        }

        $this->statusHistory = SurgeryStatusHistory::where('surgery_booking_id', $this->booking->id)
            ->orderBy('status_changed_at', 'desc')
            ->get();
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
                'status'             => $this->selectedStatus,
                'note'               => $this->statusNote ?? 'تم تعديل الحالة من الواجهة.',
                'status_changed_at'  => $datetime,
                'changed_by'         => auth()->id(),
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
        return view('livewire.surgical.operation-details');
    }
}