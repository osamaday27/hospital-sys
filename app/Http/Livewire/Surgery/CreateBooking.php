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

// ✅ موديلات الفريق الطبي
use App\models\hr\employee; // حسب مسارك الحالي
use App\models\administrative\surgery_medical_team; // غيّر المسار لو موديلك في مكان آخر

class CreateBooking extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $patients = [];
    public $selectedPatient;
    public $visits = [];

    public $surgery_ids = [];
    public $surgery_sides = [];
    public $procedure_side;
    public $booking_date;
    public $room_id;
    public $scheduled_from;
    public $scheduled_to;
    public $note;
    public $surgerySearch = '';

    // ✅ الجرّاح
    public $surgeonSearch = '';
    public $surgeons = [];
    public $selectedSurgeon = null;
    public $selectedSurgeonId = null;
    public $minBookingDate;
    public $contract_id;
    public $case_type = 'hospital';
    protected $listeners = [
        'patientCreated' => 'onPatientCreated', // في حال استخدمته مستقبلاً
    ];

    // ✅ التحديد التلقائي من الـ query أو من السيشن لو حبيت
    public function mount()
    {
        $patientId = request()->query('patient_id');
        if (!$patientId && session()->has('selected_patient_id')) {
            $patientId = session('selected_patient_id');
        }

        if ($patientId) {
            $this->selectPatient($patientId);
            // مزامنة اسم البحث (اختياري)
            $this->search = $this->selectedPatient?->full_name ?? '';
        }
        $this->minBookingDate = Carbon::today('Africa/Cairo')->toDateString();
    }

    public function onPatientCreated($patientId)
    {
        $this->selectPatient($patientId);
        $this->patients = [];
        $this->search = '';
        session()->flash('message', __('surgical_trans.patient_selected_automatically'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSurgerySearch()
    {
        $this->resetPage();
    }

    public function updatedSearch($value = null)
    {
        // تفريغ القائمة عند كل تغيير
        $this->patients = [];

        $term = is_null($value) ? (string) $this->search : (string) $value;
        $term = trim($term);

        if (mb_strlen($term) < 2) {
            return; // لن نبحث عن أقل من حرفين
        }

        // لو عندك حصر على الحساب الحالي (مثلاً)
        $accountId = auth()->user()->id_account ?? null;

        $query = \App\models\administrative\medical_record::query()
            ->when($accountId, fn($q) => $q->where('account_id', $accountId))
            // اختيار أعمدة مطلوبة فقط
            ->select(['id', 'full_name', 'national_id', 'phone_number', 'phone_number2', 'whatsapp', 'medical_number', 'passport'])
            // استخدام سكوب البحث الذكي من الموديل
            ->search($term)
            // ترتيب: المطابقات الأقرب أولاً (اختياري)
            ->orderByRaw(
                "CASE
        WHEN medical_number = ? THEN 0
        WHEN REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(IFNULL(medical_number,''),' ',''),'-',''),'(',''),')',''),'+',''),'.','') = ? THEN 1
        WHEN full_name = ? THEN 2
        WHEN full_name LIKE ? THEN 3
        WHEN national_id = ? THEN 4
        ELSE 5
    END, id DESC",
                [$term, preg_replace('/\D+/', '', $term), $term, '%' . str_replace('%', '\%', $term) . '%', $term],
            )

            ->limit(12);

        $this->patients = $query->get();
    }

    public function selectPatient($id)
    {
        $this->selectedPatient = medical_record::with([
            'visits' => function ($q) {
                $q->latest('date_visit')->take(5);
            },
            'contract',
        ])->find($id);

        $this->visits = $this->selectedPatient?->visits ?? [];
        $this->patients = [];
        $this->search = '';

        // ✅ Snapshot العقد الحالي من ملف المريض
        $this->contract_id = $this->selectedPatient?->insurance_contracting;
    }

    // =========================
    // 🔎 بحث/اختيار الجرّاح
    // =========================
    public function updatedSurgeonSearch()
    {
        $this->surgeons = [];

        if (strlen($this->surgeonSearch) > 1) {
            // ✳️ عدّل شروط البحث بما يناسب جدول الموظفين عندك
            $this->surgeons = employee::query()
                ->where(function ($q) {
                    $q->where('employee_type', 'physician')
                        ->where('full_name', 'like', "%{$this->surgeonSearch}%")
                        ->orWhere('employee_code', 'like', "%{$this->surgeonSearch}%");
                })
                // ->where('job_title', 'surgeon') // لو عندك عنوان وظيفي
                // ->where('status', 'active')     // لو عندك حالة
                ->limit(10)
                ->get();
        }
    }

    public function selectSurgeon($id)
    {
        $this->selectedSurgeon = employee::find($id);
        $this->selectedSurgeonId = $this->selectedSurgeon?->id;
        $this->surgeonSearch = $this->selectedSurgeon?->full_name ?? '';
        $this->surgeons = [];
    }

    public function clearSelectedSurgeon()
    {
        $this->selectedSurgeon = null;
        $this->selectedSurgeonId = null;
        $this->surgeonSearch = '';
    }

    public function save()
    {
        $this->validate(
            [
                'selectedPatient' => 'required',
                'surgery_ids' => 'required|array|min:1',
                // ✅ جعل الجرّاح إلزامي (عدّله لو عايزه اختياري)
                'selectedSurgeonId' => 'required|integer',
                'note' => 'nullable|string|max:500',
                'case_type' => 'required|in:doctor,hospital',
                'booking_date' => ['required', 'date', 'after_or_equal:' . Carbon::today('Africa/Cairo')->toDateString()],
            ],
            [
                'selectedPatient.required' => __('surgical_trans.patient_required'),
                'surgery_ids.required' => __('surgical_trans.surgery_required'),
                'surgery_ids.array' => __('surgical_trans.surgery_array_required'),
                'booking_date.required' => __('surgical_trans.booking_date_required'),
                'booking_date.date' => __('surgical_trans.booking_date_invalid'),
                'selectedSurgeonId.required' => __('surgical_trans.surgeon_required'),
                'note.max' => __('surgical_trans.note_max'),
                'case_type.required' => __('surgical_trans.case_type_required'),
                'booking_date.after_or_equal' => 'غير مسموح باختيار تاريخ في الماضي.',
            ],
        );

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

        $booking = adm_b_surgery_bookings::create([
            'patient_id' => $this->selectedPatient['id'],
            'procedure_side' => $this->procedure_side,
            'booking_date' => $this->booking_date,
            'room_id' => $this->room_id,
            'scheduled_from' => $this->scheduled_from,
            'scheduled_to' => $this->scheduled_to,
            'status' => 'booked',
            'created_by' => Auth::id(),
            // ✅ العقد snapshot وقت الحجز
            'contract_id' => $this->contract_id,
            'case_type' => $this->case_type, // ✅
        ]);

        $syncData = [];
        foreach ($this->surgery_ids as $surgeryId) {
            $syncData[$surgeryId] = ['side' => $this->surgery_sides[$surgeryId] ?? null];
        }
        $booking->services()->sync($syncData);

        // ==========================================
        // 👨‍⚕️ ربط الجرّاح مع الحجز في الفريق الطبي
        // ==========================================
        if ($this->selectedSurgeonId) {
            $roleName = 'surgeon';
            $userId = $this->selectedSurgeonId;

            surgery_medical_team::create([
                'surgery_bookings_id' => $booking->id,
                'medical_team_id' => $userId,
                'user_id' => Auth::user()->id,
                'role' => $roleName,
                'status' => 'assigned',
            ]);
        }

        if ($this->note) {
            surgicalnote::create([
                'note' => $this->note,
                'user_id' => Auth::id(),
                'surgery_booking_id' => $booking->id,
            ]);
        }

        session()->flash('success', __('surgical_trans.booking_created'));
        return redirect()->route('surgical_booking.index');
    }

    public function render()
    {
        $services = service::whereHas('sublevel.level1', function ($query) {
            $query->where('level_type', 'operations');
        })
            ->when($this->surgerySearch, function ($q) {
                $q->where('name->ar', 'like', "%{$this->surgerySearch}%")->orWhere('name->en', 'like', "%{$this->surgerySearch}%");
            })
            ->paginate(3);

        $latestVisits = $this->selectedPatient ? patient_visits::where('patient_id', $this->selectedPatient->id)->latest('date_visit')->take(5)->get() : [];

        return view('livewire.surgery.create-booking', [
            'surgeries' => $services,
            'latestVisits' => $latestVisits,
        ]);
    }
}