<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Livewire\WithPagination;
use App\models\administrative\adm_b_surgery_bookings;
use App\models\administrative\surgerystatushistory;
use App\Traits\web\get_contract;
use App\Models\Finance\advancepayment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SurgicalAnesthesia extends Component
{
    use WithPagination, get_contract;

    public $search = '';
    public $room_id = '';
    public $surgical_id = '';
    public $booking_date = '';
    protected $paginationTheme = 'bootstrap';

    // Cancel
    public $cancelReason = '';
    public $cancelBookingId;

    // Status change
    public $statusBookingId = null;
    public $nextStatus = null;
    public $statusChangeTime; // datetime-local
    public $statusNote = '';

    // Revert
    public $revertBookingId = null;
    public $previousStatus = null;
    public $currentStatusLabel = null;
    public $previousStatusLabel = null;
    public $revertNote = '';
    public $revertTime = '';

    // Notes
    public $historyItems = [];

    protected $listeners = [
        'showCancelModal' => 'showCancelModal',
        'hideCancelModal' => 'hideCancelModal',
        'showStatusModal' => 'showStatusModal',
        'hideStatusModal' => 'hideStatusModal',
        'showRevertModal' => 'showRevertModal',
        'hideRevertModal' => 'hideRevertModal',
        'showNotesModal' => 'showNotesModal',
    ];
    public function mount()
    {
        $this->booking_date = Carbon::today()->toDateString();
    }
    /*==========================
      Filters
    ==========================*/
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingRoomId()
    {
        $this->resetPage();
    }
    public function updatingSurgicalId()
    {
        $this->resetPage();
    }
    public function updatingBookingDate()
    {
        $this->resetPage();
    }

    /*==========================
      Helpers
    ==========================*/
    private function isPaid($bookingId): bool
    {
        return advancepayment::where('booking_id', $bookingId)->where('status', 'confirmed')->exists();
    }

    /*==========================
      Details require advance payment
    ==========================*/
    public function openAnesthesiaDetails($id)
    {
        if (!$this->isPaid($id)) {
            session()->flash('error', 'لا يمكن عرض تفاصيل العملية قبل دفع دفعة مقدّمة.');
            return;
        }
        return redirect()->route('surgical_anesthesia.details', $id);
    }

    /*==========================
      Change status (prompt -> save)
    ==========================*/
    public function changeStatusPrompt($bookingId, $targetStatus)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);

        // جميع إجراءات التخدير هنا تتطلب دفع
        if (!$this->isPaid($booking->id)) {
            session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة.');
            return;
        }

        $this->statusBookingId = $bookingId;
        $this->nextStatus = $targetStatus;
        $this->statusChangeTime = Carbon::now()->format('Y-m-d\TH:i'); // default now
        $this->statusNote = '';

        $this->emit('showStatusModal');
    }

    public function saveStatusChange()
    {
        $this->validate([
            'statusBookingId' => 'required|integer|exists:adm_b_surgery_bookings,id',
            'nextStatus' => 'required|string',
            'statusChangeTime' => 'required|date',
            'statusNote' => 'nullable|string',
        ]);

        $booking = adm_b_surgery_bookings::findOrFail($this->statusBookingId);

        if (!$this->isPaid($booking->id)) {
            session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة.');
            return;
        }

        $booking->status = $this->nextStatus;
        $booking->save();

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status' => $this->nextStatus,
            'note' => $this->statusNote,
            'status_changed_at' => Carbon::parse($this->statusChangeTime),
            'changed_by' => Auth::id(),
        ]);

        $this->resetStatusModalState();
        session()->flash('success', 'تم تحديث الحالة وتسجيلها في السجل.');
        $this->emit('hideStatusModal');
    }

    private function resetStatusModalState()
    {
        $this->statusBookingId = null;
        $this->nextStatus = null;
        $this->statusChangeTime = null;
        $this->statusNote = '';
    }

    /*==========================
      Revert to previous status
    ==========================*/
    public function revertPrompt($bookingId)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);

        $last = surgerystatushistory::where('surgery_booking_id', $booking->id)->orderByDesc('id')->first();

        $prev = surgerystatushistory::where('surgery_booking_id', $booking->id)->orderByDesc('id')->skip(1)->first();

        if (!$prev && !$last) {
            session()->flash('error', 'لا يوجد تاريخ حالات كافٍ للرجوع.');
            return;
        }

        $target = $prev ?: $last;

        $this->revertBookingId = $booking->id;
        $this->previousStatus = $target->status;
        $this->currentStatusLabel = $booking->status;
        $this->previousStatusLabel = $target->status;
        $this->revertNote = '';
        $this->revertTime = Carbon::now()->format('Y-m-d\TH:i');

        $this->emit('showRevertModal');
    }

    public function confirmRevert()
    {
        $this->validate([
            'revertBookingId' => 'required|integer|exists:adm_b_surgery_bookings,id',
            'previousStatus' => 'required|string',
            'revertTime' => 'required|date',
            'revertNote' => 'required|string|min:2',
        ]);

        $booking = adm_b_surgery_bookings::findOrFail($this->revertBookingId);

        $booking->status = $this->previousStatus;
        $booking->save();

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status' => $this->previousStatus,
            'note' => $this->revertNote,
            'status_changed_at' => Carbon::parse($this->revertTime),
            'changed_by' => Auth::id(),
        ]);

        $this->resetRevertModalState();

        session()->flash('success', 'تم الرجوع للحالة السابقة وتسجيل العملية.');
        $this->emit('hideRevertModal');
    }

    private function resetRevertModalState()
    {
        $this->revertBookingId = null;
        $this->previousStatus = null;
        $this->currentStatusLabel = null;
        $this->previousStatusLabel = null;
        $this->revertNote = '';
        $this->revertTime = '';
    }

    /*==========================
      Notes (history)
    ==========================*/
    public function openNotes($bookingId)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);

        $history = surgerystatushistory::where('surgery_booking_id', $booking->id)->orderByDesc('status_changed_at')->get();

        $this->historyItems = $history
            ->map(function ($h) {
                return [
                    'status_changed_at' => optional($h->status_changed_at)->format('Y-m-d H:i'),
                    'status' => $h->status,
                    'note' => $h->note,
                    'user_name' => optional($h->changedByUser)->name,
                ];
            })
            ->toArray();

        $this->emit('showNotesModal');
    }

    /*==========================
      Cancel booking (with history)
    ==========================*/
    public function confirmCancel($id)
    {
        $this->cancelBookingId = $id;
        $this->cancelReason = '';
        $this->resetErrorBag();
        $this->resetValidation();
        $this->emit('showCancelModal');
    }

    public function cancelBookingConfirmed()
    {
        $this->validate([
            'cancelReason' => 'required|string|min:3',
        ]);

        $booking = adm_b_surgery_bookings::findOrFail($this->cancelBookingId);
        $booking->update([
            'status' => 'cancelled',
            'cancel_reason' => $this->cancelReason,
        ]);

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status' => 'cancelled',
            'note' => $this->cancelReason,
            'status_changed_at' => Carbon::now(),
            'changed_by' => Auth::id(),
        ]);

        $this->cancelReason = '';
        $this->cancelBookingId = null;

        session()->flash('success', 'تم إلغاء الحجز بنجاح.');
        $this->emit('hideCancelModal');
    }

    /*==========================
      (قديم) تحديث مباشر — أبقيته لو محتاجه
      ومع حارس الدفع لغير الإلغاء
    ==========================*/
    private function updateBookingStatus($id, $status, $message)
    {
        $booking = adm_b_surgery_bookings::findOrFail($id);

        // حارس دفع — يمنع أي تغيير غير الإلغاء بدون دفع
        if ($status !== 'cancelled' && !$this->isPaid($booking->id)) {
            session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة.');
            return;
        }

        $booking->status = $status;
        $booking->save();

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status' => $status,
            'note' => null,
            'status_changed_at' => Carbon::now(),
            'changed_by' => Auth::id(),
        ]);

        session()->flash('success', $message);
    }

    /*==========================
      Render
    ==========================*/
    public function render()
    {
        $bookings = adm_b_surgery_bookings::with(['patient', 'room', 'services', 'user', 'medicalTeam.medical_team', 'diagnoses'])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')->orWhere('medical_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->room_id, function ($query) {
                $query->where('room_id', $this->room_id);
            })
            ->when($this->surgical_id, function ($q) {
                $sid = $this->surgical_id;
                $q->whereHas('services', function ($qq) use ($sid) {
                    $qq->where('fin_services.id', $sid); // ← التعديل هنا
                });
            })
            ->when($this->booking_date, fn($q) => $q->whereDate('booking_date', $this->booking_date))
            ->orderBy('id', 'desc')
            ->paginate(10);

        $rooms = \App\models\general\adm_s_rooms_surgical::all();
        $surgicals = \App\Models\Finance\service::where('sublevel_id', '7')->where('status', 'active')->get();

        foreach ($bookings as $booking) {
            $contractId = $booking->patient->insurance_contracting ?? null;
            $booking->contract_full_name = $contractId ? $this->getcontractname($contractId) : '-';
        }

        return view('livewire.surgical.surgical-anesthesia', [
            'bookings' => $bookings,
            'rooms' => $rooms,
            'surgicals' => $surgicals,
        ]);
    }

    // handlers required by JS listeners
    public function showCancelModal() {}
    public function hideCancelModal() {}
    public function showStatusModal() {}
    public function hideStatusModal() {}
    public function showRevertModal() {}
    public function hideRevertModal() {}
    public function showNotesModal() {}
}