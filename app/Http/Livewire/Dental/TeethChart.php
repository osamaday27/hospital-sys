<?php

namespace App\Http\Livewire\Dental;

use Livewire\Component;
use App\models\medical\DentalIssue;
use App\models\administrative\medical_record;

class TeethChart extends Component
{
    public $id_patient;
    public $id_reservation;
    public $type_visit;
    public $id_visit;
    
    public $viewType = 'all';
    public $selectedTooth = null;
    public $loadingTooth = false;
    
    // بيانات السن
    public $complaint = '';
    public $diagnosis = '';
    public $treatment_plan = '';
    public $history = '';
    
    // حالات السن
    public $actions = [
        'missing' => false,
        'filled' => false,
        'crowned' => false,
        'abscess' => false,
        'implant' => false,
        'extracted' => false,
    ];
    
    public $toothHistory = [];
    public $patient;

    // تحميل البيانات الأولية عند التحميل
    public function mount($id_patient, $id_reservation = null, $type_visit = null, $id_visit = null)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
        
        // يمكنك تحميل بيانات المريض هنا إذا كنت بحاجة إليها في العرض
        $this->patient = medical_record::find($id_patient);
    }

    // تحديد سن معين
    public function selectTooth($tooth)
    {
        $this->loadingTooth = true;
        $this->selectedTooth = $tooth;
        $this->resetToothData();

        // تحميل أحدث بيانات للسن المحدد
        $data = DentalIssue::where([
            'patient_id' => $this->id_patient,
            'tooth' => $tooth,
        ])
        ->when($this->id_reservation, function($query) {
            $query->where('id_reservation', $this->id_reservation);
        })
        ->when($this->type_visit, function($query) {
            $query->where('type_visit', $this->type_visit);
        })
        ->when($this->id_visit, function($query) {
            $query->where('id_visit', $this->id_visit);
        })
        ->latest()
        ->first();

        if ($data) {
            $this->fillToothData($data);
        }

        // تحميل سجل التعديلات للسن
        $this->loadToothHistory();
        $this->loadingTooth = false;
    }

    // حفظ بيانات السن
    public function save()
    {
        $this->validate([
            'selectedTooth' => 'required',
            'complaint' => 'nullable|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment_plan' => 'nullable|string|max:1000',
            'history' => 'nullable|string|max:1000',
        ]);

        try {
            // حفظ البيانات أو تحديثها
            $dentalIssue = DentalIssue::updateOrCreate(
                [
                    'patient_id' => $this->id_patient,
                    'id_reservation' => $this->id_reservation,
                    'type_visit' => $this->type_visit,
                    'id_visit' => $this->id_visit,
                    'tooth' => $this->selectedTooth,
                ],
                [
                    'complaint' => $this->complaint,
                    'diagnosis' => $this->diagnosis,
                    'treatment_plan' => $this->treatment_plan,
                    'history' => $this->history,
                    'missing' => $this->actions['missing'],
                    'filled' => $this->actions['filled'],
                    'crowned' => $this->actions['crowned'],
                    'abscess' => $this->actions['abscess'],
                    'implant' => $this->actions['implant'],
                    'extracted' => $this->actions['extracted'],
                    'user_id' => auth()->id(),
                ]
            );

            // إعادة تحميل البيانات بعد الحفظ
            $this->selectTooth($this->selectedTooth);
            $this->dispatchBrowserEvent('tooth-saved', ['message' => 'تم حفظ بيانات السن بنجاح']);
        } catch (\Exception $e) {
            // التعامل مع الخطأ عند الفشل في الحفظ
            $this->dispatchBrowserEvent('tooth-saved', ['message' => 'حدث خطأ أثناء حفظ البيانات.']);
        }
    }

    // تبديل حالة السن
    public function toggleAction($action)
    {
        if (array_key_exists($action, $this->actions)) {
            $this->actions[$action] = !$this->actions[$action];
            
            // يمكنك إضافة منطق إضافي هنا إذا لزم الأمر
            // مثلاً: لا يمكن أن تكون السن مفقودة وموجودة في نفس الوقت
            if ($action === 'missing' && $this->actions['missing']) {
                $this->actions['filled'] = false;
                $this->actions['crowned'] = false;
                $this->actions['abscess'] = false;
                $this->actions['implant'] = false;
                $this->actions['extracted'] = false;
            }
        }
    }

    // مسح التحديد الحالي
    public function clearSelection()
    {
        $this->selectedTooth = null;
        $this->resetToothData();
        $this->toothHistory = [];
    }

    // تغيير نوع العرض (جميع الأسنان، الدائمة، اللبنية)
    public function setViewType($type)
    {
        $this->viewType = $type;
    }

    // طباعة تقرير السن
    public function printToothReport()
    {
        $this->validate([
            'selectedTooth' => 'required',
        ]);

        $this->dispatchBrowserEvent('show-print-modal');
    }

    // تحميل سجل التعديلات للسن
    protected function loadToothHistory()
    {
        $this->toothHistory = DentalIssue::where([
            'patient_id' => $this->id_patient,
            'tooth' => $this->selectedTooth,
        ])
        ->when($this->id_reservation, function($query) {
            $query->where('id_reservation', $this->id_reservation);
        })
        ->when($this->type_visit, function($query) {
            $query->where('type_visit', $this->type_visit);
        })
        ->when($this->id_visit, function($query) {
            $query->where('id_visit', $this->id_visit);
        })
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    // تعبئة بيانات السن من النموذج
    protected function fillToothData($data)
    {
        $this->complaint = $data->complaint ?? '';
        $this->diagnosis = $data->diagnosis ?? '';
        $this->treatment_plan = $data->treatment_plan ?? '';
        $this->history = $data->history ?? '';
        
        $this->actions = [
            'missing' => (bool) ($data->missing ?? false),
            'filled' => (bool) ($data->filled ?? false),
            'crowned' => (bool) ($data->crowned ?? false),
            'abscess' => (bool) ($data->abscess ?? false),
            'implant' => (bool) ($data->implant ?? false),
            'extracted' => (bool) ($data->extracted ?? false),
        ];
    }

    // إعادة تعيين بيانات السن
    protected function resetToothData()
    {
        $this->complaint = '';
        $this->diagnosis = '';
        $this->treatment_plan = '';
        $this->history = '';
        
        $this->actions = [
            'missing' => false,
            'filled' => false,
            'crowned' => false,
            'abscess' => false,
            'implant' => false,
            'extracted' => false,
        ];
    }

    // دالة العرض
    public function render()
    {
        // الحصول على جميع الأسنان التي لها بيانات
        $teethWithData = DentalIssue::where('patient_id', $this->id_patient)
            ->when($this->id_reservation, function($query) {
                $query->where('id_reservation', $this->id_reservation);
            })
            ->when($this->type_visit, function($query) {
                $query->where('type_visit', $this->type_visit);
            })
            ->when($this->id_visit, function($query) {
                $query->where('id_visit', $this->id_visit);
            })
            ->pluck('tooth')
            ->toArray();

        // الحصول على حالات جميع الأسنان
        $toothStatuses = DentalIssue::where('patient_id', $this->id_patient)
            ->when($this->id_reservation, function($query) {
                $query->where('id_reservation', $this->id_reservation);
            })
            ->when($this->type_visit, function($query) {
                $query->where('type_visit', $this->type_visit);
            })
            ->when($this->id_visit, function($query) {
                $query->where('id_visit', $this->id_visit);
            })
            ->get()
            ->keyBy('tooth')
            ->map(function ($item) {
                return [
                    'missing' => (bool) $item->missing,
                    'filled' => (bool) $item->filled,
                    'crowned' => (bool) $item->crowned,
                    'abscess' => (bool) $item->abscess,
                    'implant' => (bool) $item->implant,
                    'extracted' => (bool) $item->extracted,
                ];
            })
            ->toArray();

        return view('livewire.dental.teeth-chart', [
            'teethWithData' => $teethWithData,
            'toothStatuses' => $toothStatuses,
            'patient' => $this->patient,
        ]);
    }
}