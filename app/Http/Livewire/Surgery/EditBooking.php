<?php

namespace App\Http\Livewire\Surgery;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Administrative\medical_record;
use App\Models\Administrative\patient_visits;
use App\Models\Finance\service;
use App\Models\Administrative\surgicalnote;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Crypt;
class EditBooking extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $bookingId;

    // بيانات الحجز
    public $booking; // الكائن الأصلي
    public $selectedPatient; // كائن المريض (ثابت للعرض فقط)
    public $visits = [];

    // حقول قابلة للتعديل
    public $surgery_ids = []; // العمليات المختارة
    public $surgery_sides = []; // الجانب لكل عملية [service_id => side]
    public $procedure_side; // لو بتستخدمه كحقل عام
    public $booking_date; // yyyy-mm-dd
    public $scheduled_from; // HH:MM
    public $scheduled_to; // HH:MM

    public $note; // ملاحظة جديدة اختيارية
    public $surgerySearch = '';
    public $case_type = 'hospital';

    public function mount($id)
    {
        $this->bookingId = Crypt::decryptString($id);

        $this->booking = adm_b_surgery_bookings::with([
            'patient',
            'room',
            'services', // pivot: side
        ])->findOrFail($this->bookingId);
        $this->case_type = $this->booking->case_type ?? 'hospital';

        // تعبئة البيانات الأولية
        $this->selectedPatient = $this->booking->patient;

        $this->booking_date = $this->booking->booking_date ? Carbon::parse($this->booking->booking_date)->format('Y-m-d') : null;

        // نخلي الأوقات كساعات/دقائق لو موجودة
        $this->scheduled_from = $this->booking->scheduled_from ? Carbon::parse($this->booking->scheduled_from)->format('H:i') : null;

        $this->scheduled_to = $this->booking->scheduled_to ? Carbon::parse($this->booking->scheduled_to)->format('H:i') : null;

        $this->procedure_side = $this->booking->procedure_side;

        // آخر 5 زيارات
        $this->visits = $this->selectedPatient ? patient_visits::where('patient_id', $this->selectedPatient->id)->latest('date_visit')->take(5)->get() : [];

        // العمليات المختارة + الجوانب من الـ pivot
        $this->surgery_ids = $this->booking->services->pluck('id')->toArray();
        foreach ($this->booking->services as $srv) {
            $this->surgery_sides[$srv->id] = $srv->pivot?->side;
        }
    }

    public function updatingSurgerySearch()
    {
        $this->resetPage();
    }

    /**
     * فحص التداخل الزمني في نفس الغرفة (لو الحجز مرتبط بغرفة)
     */
    protected function hasTimeConflict(?int $roomId, ?string $targetDate, ?string $from, ?string $to): bool
    {
        if (!$roomId || !$targetDate || !$from || !$to) {
            // لو مفيش بيانات كاملة، منعتبرها بلا تضارب (لأننا مش هنحدد غرفة من الأساس)
            return false;
        }

        // نبني تاريخ/وقت كامل
        $newFrom = Carbon::parse($targetDate . ' ' . $from . ':00');
        $newTo = Carbon::parse($targetDate . ' ' . $to . ':00');

        if ($newFrom >= $newTo) {
            throw ValidationException::withMessages([
                'scheduled_to' => ['وقت النهاية يجب أن يكون بعد وقت البداية.'],
            ]);
        }

        return adm_b_surgery_bookings::query()
            ->where('id', '!=', $this->booking->id)
            ->where('room_id', $roomId)
            ->whereDate('booking_date', $targetDate)
            ->whereNotIn('status', ['cancelled', 'failed', 'postponed', 'transferred', 'room_canceled'])
            ->where(function ($q) use ($newFrom, $newTo) {
                // existing_from < new_to AND existing_to > new_from
                $q->where(function ($qq) use ($newFrom, $newTo) {
                    $qq->where('scheduled_from', '<', $newTo)->where('scheduled_to', '>', $newFrom);
                });
            })
            ->exists();
    }

    public function save()
    {
        // قواعد عامة
        $this->validate(
            [
                'booking_date' => 'required|date',
                'scheduled_from' => 'nullable|date_format:H:i',
                'scheduled_to' => 'nullable|date_format:H:i',
                'surgery_ids' => 'required|array|min:1',
                'note' => 'nullable|string|max:500',
                'case_type' => 'required|in:doctor,hospital',

            ],
            [
                'booking_date.required' => __('surgical_trans.booking_date_required'),
                'booking_date.date' => __('surgical_trans.booking_date_invalid'),
                'surgery_ids.required' => __('surgical_trans.surgery_required'),
                'surgery_ids.array' => __('surgical_trans.surgery_array_required'),
                'note.max' => __('surgical_trans.note_max'),
            ],
        );

        // التحقق من sides لكل عملية
        $hasError = false;
        $generalErrors = [];

        foreach ($this->surgery_ids as $surgeryId) {
            if (empty($this->surgery_sides[$surgeryId])) {
                $surgery = service::find($surgeryId);
                $surgeryName = $surgery ? $surgery->getTranslation('name', app()->getLocale()) : '';
                $errorMsg = __('surgical_trans.side_required_for', ['surgery' => $surgeryName]);

                $this->addError("surgery_sides.$surgeryId", $errorMsg);
                $generalErrors[] = $errorMsg;
                $hasError = true;
            }
        }

        if ($hasError) {
            session()->flash('validation_errors', $generalErrors);
            return;
        }

        // لو الحجز مرتبط بغرفة وأوقات → افحص التداخل قبل النقل
        $roomId = $this->booking->room_id; // لا نسمح بتغييره من الواجهة
        $from = $this->scheduled_from;
        $to = $this->scheduled_to;

        if ($roomId && $from && $to) {
            $conflict = $this->hasTimeConflict($roomId, $this->booking_date, $from, $to);

            if ($conflict) {
                // يوجد جدول لنفس اليوم والميعاد → التنبية ومنع النقل
                session()->flash('error', '⚠️ يوجد تضارب في نفس الغرفة والميعاد لهذا التاريخ. لم يتم حفظ التعديل.');
                return;
            }
        } else {
            // تنبيه فقط: لا يتم اختيار غرفة إطلاقًا في هذه الشاشة
            // ولو مفيش أوقات كاملة، هنمشي الحفظ من غير فحص تضارب
        }

        // تحديث الحجز (بدون تغيير الغرفة)
        $updateData = [
            'procedure_side' => $this->procedure_side,
            'booking_date' => $this->booking_date,
            'case_type' => $this->case_type,
        ];

        // تحديث أوقات الجدولة إن وُجدت
        if ($from && $to) {
            $updateData['scheduled_from'] = Carbon::parse($this->booking_date . ' ' . $from . ':00');
            $updateData['scheduled_to'] = Carbon::parse($this->booking_date . ' ' . $to . ':00');
        } else {
            // لو حابب تسيبهم كما هم لا تعمل شيء
            // أو تفضيهم لو المستخدم شال القيم:
            // $updateData['scheduled_from'] = null;
            // $updateData['scheduled_to']   = null;
        }

        $this->booking->update($updateData);

        // مزامنة العمليات والجوانب
        $syncData = [];
        foreach ($this->surgery_ids as $surgeryId) {
            $syncData[$surgeryId] = ['side' => $this->surgery_sides[$surgeryId] ?? null];
        }
        $this->booking->services()->sync($syncData);

        if ($this->note) {
            surgicalnote::create([
                'note' => $this->note,
                'user_id' => Auth::id(),
                'surgery_booking_id' => $this->booking->id,
            ]);
        }
        session()->flash('success', 'تم حفظ التعديلات بنجاح.');
        return redirect()->route('surgical_booking.index');
    }

    public function render()
    {
        // قائمة العمليات (Operations)
        $services = service::whereHas('sublevel.level1', function ($query) {
            $query->where('level_type', 'operations');
        })
            ->when($this->surgerySearch, function ($q) {
                $term = "%{$this->surgerySearch}%";
                $q->where('name->ar', 'like', $term)->orWhere('name->en', 'like', $term);
            })
            ->paginate(3);

        return view('livewire.surgery.edit-booking', [
            'surgeries' => $services,
            'latestVisits' => $this->visits,
        ]);
    }
}