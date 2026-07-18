<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Administrative\surgerystatushistory;
use App\Traits\web\get_contract;
use App\Models\Finance\advancepayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// ✅ موديل الزيارات
use App\models\administrative\patient_visits;

class SurgicalBooking extends Component
{
    use WithPagination, get_contract;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $room_id = '';
    public $surgical_id = ''; // فلترة عبر many-to-many (services)
    public $booking_date = ''; // هنضبطها افتراضيًا على اليوم داخل mount()

    // إلغاء
    public $cancelReason = '';
    public $cancelBookingId;

    // تغيير الحالة
    public $statusBookingId = null;
    public $nextStatus = null;
    public $statusChangeTime; // datetime-local
    public $statusNote = '';

    // رجوع للحالة السابقة
    public $revertBookingId = null;
    public $previousStatus = null;
    public $currentStatusLabel = null;
    public $previousStatusLabel = null;
    public $revertNote = '';
    public $revertTime = '';

    // ملاحظات
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

    /** ✅ افتراضيًا: عرض حجوزات اليوم **/
    public function mount()
    {
        $this->booking_date = Carbon::today()->toDateString();
    }

    /** ✅ تحسين تجربة البحث بالرجوع لأول صفحة */
    public function updatingSearch() { $this->resetPage(); }
    public function updatingRoomId() { $this->resetPage(); }
    public function updatingSurgicalId() { $this->resetPage(); }
    public function updatingBookingDate() { $this->resetPage(); }

    /** صلاحية الانتقال بين الحالات وفق الدفع */
    private function canProceedToStatus(adm_b_surgery_bookings $booking, string $target): bool
    {
        $allowedWithoutAdvance = ['arrived', 'cancelled'];
        if (in_array($target, $allowedWithoutAdvance, true)) {
            return true;
        }

        // لدينا advance_confirmed_count جاهزة عبر withCount، بس نأمن fallback
        if (isset($booking->advance_confirmed_count)) {
            return $booking->advance_confirmed_count > 0;
        }

        return advancepayment::where('booking_id', $booking->id)->where('status', 'confirmed')->exists();
    }

    /** فتح مودال تغيير الحالة */
    public function changeStatusPrompt($bookingId, $targetStatus)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);
        if ($targetStatus != 'confirmed') {
            if (!$this->canProceedToStatus($booking, $targetStatus)) {
                session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة.');
                return;
            }
        }

        $this->statusBookingId = $bookingId;
        $this->nextStatus = $targetStatus;
        $this->statusChangeTime = Carbon::now()->format('Y-m-d\TH:i');
        $this->statusNote = '';

        // افتح المودال فورًا (إحساس سريع)
        $this->emit('showStatusModal');
    }

    /** ✅ عند الوصول: فتح/إرجاع زيارة وتحديث visit_id في الحجز */
    protected function ensureVisitForArrival(adm_b_surgery_bookings $booking): patient_visits
    {
        // لو بالفعل مرتبط بزيارة، رجّعها
        if (!empty($booking->visit_id)) {
            $existing = patient_visits::find($booking->visit_id);
            if ($existing) {
                return $existing;
            }
        }

        // جرّب تلاقي زيارة لنفس الحجز (في حال كانت اتعملت قبل كده)
        $visit = patient_visits::where('reservation_id', $booking->id)->latest('id')->first();
        if ($visit) {
            // حدّث visit_id في الحجز لو ناقص
            if (empty($booking->visit_id)) {
                $booking->visit_id = $visit->id;
                $booking->save();
            }
            return $visit;
        }

        // إنشاء زيارة جديدة
        $user = Auth::user();
        $accountId = method_exists($user, 'getAttribute') ? $user->getAttribute('id_account') : null;

        $visit = patient_visits::create([
            'patient_id'    => $booking->patient_id ?? optional($booking->patient)->id,
            'reservation_id'=> $booking->id,
            'date_visit'    => Carbon::now(),          // تاريخ ووقت الزيارة
            'type'          => 'surgical',             // استخدم النوع المناسب لمشروعك
            'status'        => 'open',                 // مفتوحة عند الوصول
            'user_id'       => Auth::id(),
            'account_id'    => $accountId,
        ]);

        // اربط الزيارة بالحجز
        $booking->visit_id = $visit->id;
        $booking->save();

        return $visit;
    }

    public function saveStatusChange()
    {
        $this->validate([
            'statusBookingId' => 'required|integer|exists:adm_b_surgery_bookings,id',
            'nextStatus' => 'required|string',
            'statusChangeTime' => 'required|date',
            'statusNote' => 'nullable|string',
        ]);

        DB::transaction(function () {
            $booking = adm_b_surgery_bookings::lockForUpdate()->findOrFail($this->statusBookingId);

            if ($this->nextStatus != 'confirmed') {
                if (!$this->canProceedToStatus($booking, $this->nextStatus)) {
                    session()->flash('error', 'لا يمكن تغيير الحالة قبل دفع الدفعة المقدّمة.');
                    return;
                }
            }

            // ✅ لو الحالة Arrived: أنشئ/استرجع زيارة واربِطها بالحجز
            if ($this->nextStatus === 'arrived') {
                $this->ensureVisitForArrival($booking);
            }

            // حفظ الحالة
            $booking->status = $this->nextStatus;
            $booking->save();

            // سجّل في التاريخ
            surgerystatushistory::create([
                'surgery_booking_id' => $booking->id,
                'status' => $this->nextStatus,
                'note' => $this->statusNote,
                'status_changed_at' => Carbon::parse($this->statusChangeTime),
                'changed_by' => Auth::id(),
            ]);
        });

        $this->resetStatusModalState();

        // اقفل المودال فورًا لتحسين الإحساس بالسرعة
        $this->emit('hideStatusModal');
        session()->flash('success', 'تم تحديث الحالة وتسجيلها في السجل.');
    }

    private function resetStatusModalState()
    {
        $this->statusBookingId = null;
        $this->nextStatus = null;
        $this->statusChangeTime = null;
        $this->statusNote = '';
    }

    /** الرجوع للحالة السابقة */
    public function revertPrompt($bookingId)
    {
        $booking = adm_b_surgery_bookings::findOrFail($bookingId);

        $prev = surgerystatushistory::where('surgery_booking_id', $booking->id)->orderByDesc('id')->skip(1)->first();

        if (!$prev) {
            $prev = surgerystatushistory::where('surgery_booking_id', $booking->id)->orderByDesc('id')->first();
        }

        if (!$prev) {
            session()->flash('error', 'لا يوجد سجل سابق للرجوع إليه.');
            return;
        }

        $this->revertBookingId = $booking->id;
        $this->previousStatus = $prev->status;
        $this->currentStatusLabel = $booking->status;
        $this->previousStatusLabel = $prev->status;
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

        $this->emit('hideRevertModal');
        session()->flash('success', 'تم الرجوع للحالة السابقة وتسجيل العملية.');
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

    /** عرض سجل الملاحظات */
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
                    'user_name' => optional($h->changedByUser)->name ?? '-',
                ];
            })
            ->toArray();

        $this->emit('showNotesModal');
    }

    /** إلغاء الحجز */
    public function confirmCancel($id)
    {
        $this->cancelBookingId = $id;
        $this->cancelReason = '';
        $this->resetErrorBag();
        $this->resetValidation();

        // افتح المودال فورًا لتحسين إحساس السرعة
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

        $this->emit('hideCancelModal');
        session()->flash('success', 'تم إلغاء الحجز بنجاح.');
    }

    /** فتح التفاصيل (يشترط دفعة مقدّمة) */
    public function openBookingDetails($id)
    {
        // نعتمد على العدّ المسبق إن وُجد، وإلا fallback سريع
        $paid =
            adm_b_surgery_bookings::query()
                ->where('id', $id)
                ->withCount([
                    'advancePayments as advance_confirmed_count' => function ($q) {
                        $q->where('status', 'confirmed');
                    },
                ])
                ->firstOrFail()->advance_confirmed_count > 0;

        if (!$paid) {
            session()->flash('error', 'لا يمكن عرض تفاصيل العملية قبل دفع دفعة مقدّمة.');
            return;
        }

        return redirect()->route('surgical_booking.details', $id);
    }

    /** قائمة الحجوزات + البيانات المساعدة — مُحسّنة للأداء */
    public function render()
    {
        $bookings = adm_b_surgery_bookings::with([
            'patient',
            'room',
            'services', // many-to-many (pivot: side)
            'user',
            'medicalTeam.medical_team',
            'diagnoses',
        ])
            // ✅ نحسب مؤشرات بدل الاستعلام داخل الحلقة
            ->withCount([
                'advancePayments as advance_confirmed_count' => function ($q) {
                    $q->where('status', 'confirmed');
                },
                'statusHistory as history_count',
            ])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $like = '%' . $this->search . '%';
                    $q->where('full_name', 'like', $like)->orWhere('medical_number', 'like', $like);
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
            ->orderByDesc('id')
            ->paginate(10);

        $rooms = \App\Models\General\adm_s_rooms_surgical::all();

        $surgicals = \App\Models\Finance\service::where('sublevel_id', '7')->where('status', 'active')->get();

        foreach ($bookings as $booking) {
            $contractId = $booking->patient->insurance_contracting ?? null;
            $booking->contract_full_name = $contractId ? $this->getcontractname($contractId) : '-';
        }

        return view('livewire.surgical.surgical-booking', [
            'bookings' => $bookings,
            'rooms' => $rooms,
            'surgicals' => $surgicals,
        ]);
    }

    // متطلبات الـ listeners
    public function showCancelModal() {}
    public function hideCancelModal() {}
    public function showStatusModal() {}
    public function hideStatusModal() {}
    public function showRevertModal() {}
    public function hideRevertModal() {}
    public function showNotesModal() {}
}