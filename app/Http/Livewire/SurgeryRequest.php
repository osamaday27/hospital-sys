<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\models\finance\service;
use App\models\administrative\adm_b_surgery_bookings;
use App\models\hr\employee;
use App\models\administrative\surgery_medical_team;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurgeryRequest extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $id_patient;
    public $booking_date;
    public $notes;

    // عمليات متعددة + جهة لكل عملية
    public $surgery_ids = [];
    public $surgery_sides = []; // [service_id => 'right'|'left'|'both']

    // للبحث عن العمليات
    public $surgerySearch = '';

    // عرض الطلبات السابقة
    public $requests = [];

    protected $rules = [
        'id_patient'    => 'required|integer',
        'booking_date'  => 'required|date',
        'notes'         => 'nullable|string|max:1000',
        'surgery_ids'   => 'required|array|min:1',
    ];

    protected $messages = [
        'id_patient.required'      => 'المريض مطلوب.',
        'booking_date.required'    => 'تاريخ الحجز مطلوب.',
        'booking_date.date'        => 'تاريخ الحجز غير صالح.',
        'surgery_ids.required'     => 'يجب اختيار عملية واحدة على الأقل.',
        'surgery_ids.array'        => 'صيغة العمليات غير صحيحة.',
        'surgery_ids.min'          => 'يجب اختيار عملية واحدة على الأقل.',
        'notes.max'                => 'حد أقصى 1000 حرف للملاحظات.',
    ];

    public function mount($id_patient)
    {
        $this->id_patient = $id_patient;
        $this->loadRequests();
    }

    public function updatingSurgerySearch()
    {
        $this->resetPage();
    }

    public function loadRequests()
    {
        // عرض الحجوزات السابقة للمريض الحالي + العمليات المرتبطة
        $this->requests = adm_b_surgery_bookings::with(['services', 'user'])
            ->where('patient_id', $this->id_patient)
            ->orderByDesc('booking_date')
            ->get();
    }

    public function submit()
    {
        // تحقق أساسي
        $this->validate();

        // تحقق من أن كل عملية مختارة لها جهة side
        $generalErrors = [];
        $hasSideError  = false;

        foreach ($this->surgery_ids as $sid) {
            if (empty($this->surgery_sides[$sid])) {
                $srv = service::find($sid);
                $name = $srv ? ($srv->getTranslation('name', app()->getLocale()) ?? $srv->name) : 'عملية';
                $msg  = "يجب اختيار الجهة للعملية: {$name}";
                $this->addError("surgery_sides.$sid", $msg);
                $generalErrors[] = $msg;
                $hasSideError = true;
            }
        }

        if ($hasSideError) {
            session()->flash('validation_errors', $generalErrors);
            return;
        }

        DB::beginTransaction();
        try {
            // إنشاء الحجز
            $booking = adm_b_surgery_bookings::create([
                'patient_id'     => $this->id_patient,
                'procedure_side' => null, // عام، عندك لكل خدمة side منفصل
                'booking_date'   => $this->booking_date,
                'room_id'        => null, // الاستقبال/التمريض لاحقًا
                'scheduled_from' => null, // الاستقبال/التمريض لاحقًا
                'scheduled_to'   => null, // الاستقبال/التمريض لاحقًا
                'status'         => 'booked',
                'created_by'     => Auth::id(),
                'notes'          => $this->notes,
            ]);

            // ربط العمليات + side على Pivot
            $syncData = [];
            foreach ($this->surgery_ids as $sid) {
                $syncData[$sid] = ['side' => $this->surgery_sides[$sid] ?? null];
            }
            $booking->services()->sync($syncData);

            // ربط الجرّاح تلقائيًا (الطبيب الحالي)
            $doctorEmployee = employee::where('id', Auth::user()->employee_id)->first();
            if ($doctorEmployee) {
                surgery_medical_team::create([
                    'surgery_bookings_id' => $booking->id,
                    'medical_team_id'     =>Auth::user()->employee_id , // يشير لـ employee
                    'user_id'             => Auth::id(),          // من أنشأ السجل
                    'role'                => 'surgeon',
                    'status'              => 'assigned',
                ]);
            } else {
                // ملاحظة داخلية: لو ما في Employee للطبيب الحالي
                // بإمكانك لاحقًا فرض وجود Employee بالـ Policy أو Middleware
                // session()->flash('warning', 'لم يتم ربط الجرّاح تلقائيًا لعدم العثور على سجل Employee للطبيب الحالي.');
            }

            DB::commit();

            session()->flash('message', 'تم إنشاء طلب العملية وحفظه بنجاح.');
            // إعادة التهيئة
            $this->reset(['booking_date', 'notes', 'surgery_ids', 'surgery_sides']);
            $this->loadRequests();
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('validation_errors', ['حدث خطأ غير متوقع أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        // جلب العمليات (المصنّفة كـ Operations عندك)
        $surgeries = service::whereHas('sublevel.level1', function ($q) {
                $q->where('level_type', 'operations');
            })
            ->when($this->surgerySearch, function ($q) {
                $q->where('name->ar', 'like', "%{$this->surgerySearch}%")
                  ->orWhere('name->en', 'like', "%{$this->surgerySearch}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.surgery-request', [
            'surgeries' => $surgeries,
        ]);
    }
}