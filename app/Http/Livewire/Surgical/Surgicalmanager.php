<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Livewire\WithPagination;
use App\models\administrative\adm_b_surgery_bookings;
use App\models\administrative\surgerystatushistory;
use App\Traits\web\get_contract;
use App\Models\Finance\advancepayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Surgicalmanager extends Component
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

    /* ==========================
       تخصيص غرفة ووقت (مودال السلات)
    ========================== */
    public $assignBookingId = null;
    public $assignRoomId    = '';
    public $assignDate      = '';
    public $assignStart     = '';
    public $assignEnd       = '';
    public $timeSlots       = []; // مصفوفة الفتحات القادمة من الـ route

    protected $listeners = [
        'showCancelModal'  => 'showCancelModal',
        'hideCancelModal'  => 'hideCancelModal',
        'showStatusModal'  => 'showStatusModal',
        'hideStatusModal'  => 'hideStatusModal',
        'showRevertModal'  => 'showRevertModal',
        'hideRevertModal'  => 'hideRevertModal',
        'showNotesModal'   => 'showNotesModal',

        // تخصيص الغرفة والوقت
        'showAssignModal'  => 'showAssignModal',
        'hideAssignModal'  => 'hideAssignModal',
    ];

    /*==========================
      Filters
    ==========================*/
    public function updatingSearch()      { $this->resetPage(); }
    public function updatingRoomId()      { $this->resetPage(); }
    public function updatingSurgicalId()  { $this->resetPage(); }
    public function updatingBookingDate() { $this->resetPage(); }
 public function mount()
    {
        $this->booking_date = Carbon::today()->toDateString();
    }
    /*==========================
      Helpers
    ==========================*/
    private function isPaid($bookingId): bool
    {
        return advancepayment::where('booking_id', $bookingId)
            ->where('status', 'confirmed')
            ->exists();
    }

    private function canChangeTo($bookingId, $targetStatus): bool
    {
        // يُسمح بدون دفع فقط بتأكيد الوصول
        if (in_array($targetStatus, ['arrived'], true)) {
            return true;
        }
        return $this->isPaid($bookingId);
    }

    /*==========================
      Details require advance payment
    ==========================*/
    public function openManagerDetails($id)
    {
        if (!$this->isPaid($id)) {
            session()->flash('error', 'لا يمكن عرض تفاصيل العملية قبل دفع دفعة مقدّمة.');
            return;
        }
        return redirect()->route('surgical_manger.details', $id); // نفس الاسم المستخدم عندك
    }

    /*==========================
      Change status (prompt -> save)
    ==========================*/
    public function changeStatusPrompt($bookingId, $targetStatus)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);

        if (!$this->canChangeTo($booking->id, $targetStatus)) {
            session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة. يُسمح فقط بتأكيد الوصول.');
            return;
        }

        $this->statusBookingId  = $bookingId;
        $this->nextStatus       = $targetStatus;
        $this->statusChangeTime = Carbon::now()->format('Y-m-d\TH:i');
        $this->statusNote       = '';

        $this->emit('showStatusModal');
    }

    public function saveStatusChange()
    {
        $this->validate([
            'statusBookingId'  => 'required|integer|exists:adm_b_surgery_bookings,id',
            'nextStatus'       => 'required|string',
            'statusChangeTime' => 'required|date',
            'statusNote'       => 'nullable|string',
        ]);

        $booking = adm_b_surgery_bookings::findOrFail($this->statusBookingId);

        if (!$this->canChangeTo($booking->id, $this->nextStatus)) {
            session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة. يُسمح فقط بتأكيد الوصول.');
            return;
        }

        $booking->status = $this->nextStatus;
        $booking->save();

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status'             => $this->nextStatus,
            'note'               => $this->statusNote,
            'status_changed_at'  => Carbon::parse($this->statusChangeTime),
            'changed_by'         => Auth::id(),
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

        $prev = surgerystatushistory::where('surgery_booking_id', $booking->id)
            ->orderByDesc('id')->skip(1)->first();

        if (!$prev) {
            $prev = surgerystatushistory::where('surgery_booking_id', $booking->id)
                ->orderByDesc('id')->first();
        }

        if (!$prev) {
            session()->flash('error', 'لا يوجد سجل سابق للرجوع إليه.');
            return;
        }

        $this->revertBookingId     = $booking->id;
        $this->previousStatus      = $prev->status;
        $this->currentStatusLabel  = $booking->status;
        $this->previousStatusLabel = $prev->status;
        $this->revertNote          = '';
        $this->revertTime          = Carbon::now()->format('Y-m-d\TH:i');

        $this->emit('showRevertModal');
    }

    public function confirmRevert()
    {
        $this->validate([
            'revertBookingId' => 'required|integer|exists:adm_b_surgery_bookings,id',
            'previousStatus'  => 'required|string',
            'revertTime'      => 'required|date',
            'revertNote'      => 'required|string|min:2',
        ]);

        $booking = adm_b_surgery_bookings::findOrFail($this->revertBookingId);

        $booking->status = $this->previousStatus;
        $booking->save();

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status'             => $this->previousStatus,
            'note'               => $this->revertNote,
            'status_changed_at'  => Carbon::parse($this->revertTime),
            'changed_by'         => Auth::id(),
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

        $history = surgerystatushistory::where('surgery_booking_id', $booking->id)
            ->orderByDesc('status_changed_at')
            ->get();

        $this->historyItems = $history->map(function ($h) {
            return [
                'status_changed_at' => optional($h->status_changed_at)->format('Y-m-d H:i'),
                'status'            => $h->status,
                'note'              => $h->note,
                'user_name'         => optional($h->changedByUser)->name ?? null,
            ];
        })->toArray();

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
            'status'        => 'cancelled',
            'cancel_reason' => $this->cancelReason,
        ]);

        surgerystatushistory::create([
            'surgery_booking_id' => $booking->id,
            'status'             => 'cancelled',
            'note'               => $this->cancelReason,
            'status_changed_at'  => Carbon::now(),
            'changed_by'         => Auth::id(),
        ]);

        $this->cancelReason = '';
        $this->cancelBookingId = null;

        session()->flash('success', 'تم إلغاء الحجز بنجاح.');
        $this->emit('hideCancelModal');
    }

    /*==========================
      تخصيص الغرفة والوقت (مثل التمريض)
    ==========================*/
    public function openAssignModal($bookingId)
    {
        $booking = adm_b_surgery_bookings::with('room')->findOrFail($bookingId);

        $this->assignBookingId = $bookingId;
        $this->assignRoomId    = $booking->room_id ?: '';
        $this->assignDate      = $booking->booking_date
                                ? Carbon::parse($booking->booking_date)->format('Y-m-d')
                                : Carbon::today()->format('Y-m-d');
        $this->assignStart     = $booking->scheduled_from ?: '';
        $this->assignEnd       = $booking->scheduled_to   ?: '';
        $this->timeSlots       = [];

        $this->emit('showAssignModal');
    }

    public function setPickedSlot($start, $end)
    {
        $this->assignStart = $start;
        $this->assignEnd   = $end;
    }

    public function saveAssignRoomAndTime()
    {
        $data = [
            'assignBookingId' => $this->assignBookingId,
            'assignRoomId'    => $this->assignRoomId,
            'assignDate'      => $this->assignDate,
            'assignStart'     => $this->assignStart,
            'assignEnd'       => $this->assignEnd,
        ];

        Validator::make($data, [
            'assignBookingId' => 'required|integer|exists:adm_b_surgery_bookings,id',
            'assignRoomId'    => 'required|integer|exists:adm_s_rooms_surgical,id',
            'assignDate'      => 'required|date',
            'assignStart'     => 'required|date_format:H:i',
            'assignEnd'       => 'required|date_format:H:i|after:assignStart',
        ], [
            'assignRoomId.required' => 'من فضلك اختر الغرفة.',
            'assignDate.required'   => 'من فضلك اختر التاريخ.',
            'assignStart.required'  => 'من فضلك اختر وقت البداية.',
            'assignEnd.required'    => 'من فضلك اختر وقت النهاية.',
            'assignEnd.after'       => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        ])->validate();

        // تحقق التعارض مع حجوزات أخرى
        $overlap = adm_b_surgery_bookings::where('room_id', $this->assignRoomId)
            ->whereDate('booking_date', $this->assignDate)
            ->where('id', '!=', $this->assignBookingId)
            ->whereNot('status', 'cancelled')
            ->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->where('scheduled_from', '<', $this->assignEnd)
                       ->where('scheduled_to', '>', $this->assignStart);
                });
            })
            ->exists();

        if ($overlap) {
            session()->flash('error', 'هذه الفترة متداخلة مع حجز آخر لنفس الغرفة.');
            return;
        }

        $booking = adm_b_surgery_bookings::findOrFail($this->assignBookingId);
        $booking->room_id        = $this->assignRoomId;
        $booking->booking_date   = Carbon::parse($this->assignDate)->format('Y-m-d');
        $booking->scheduled_from = $this->assignStart;
        $booking->scheduled_to   = $this->assignEnd;
        $booking->save();

        session()->flash('success', 'تم تخصيص الغرفة وتحديد الوقت بنجاح.');
        $this->assignBookingId = null;
        $this->assignRoomId = '';
        $this->assignDate = '';
        $this->assignStart = '';
        $this->assignEnd = '';
        $this->timeSlots = [];

        $this->emit('hideAssignModal');
    }

    public function cancelSchedule($bookingId)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);
        $booking->room_id        = null;
        $booking->scheduled_from = null;
        $booking->scheduled_to   = null;
        $booking->save();

        session()->flash('success', 'تم إلغاء الجدولة (الغرفة والوقت) بنجاح.');
    }

    /*==========================
      Render
    ==========================*/
    public function render()
    {
        $bookings = adm_b_surgery_bookings::with([
                'patient',
                'room',
                'services',
                'user',
                'medicalTeam.medical_team',
                'diagnoses',
            ])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('medical_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->room_id, fn($q) => $q->where('room_id', $this->room_id))
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

        return view('livewire.surgical.surgicalmanager', [
            'bookings'  => $bookings,
            'rooms'     => $rooms,
            'surgicals' => $surgicals,
        ]);
    }

    // handlers required by JS listeners
    public function showCancelModal() { }
    public function hideCancelModal() { }
    public function showStatusModal() { }
    public function hideStatusModal() { }
    public function showRevertModal() { }
    public function hideRevertModal() { }
    public function showNotesModal() { }
    public function showAssignModal() { }
    public function hideAssignModal() { }
}