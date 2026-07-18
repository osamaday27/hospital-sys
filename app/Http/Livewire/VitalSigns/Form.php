<?php

namespace App\Http\Livewire\VitalSigns;

use Livewire\Component;
use Livewire\WithPagination;
use App\models\medical\vitalsign;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $patient_id;
    public $patient_visits_id;
    public $age;

    // حقول الإدخال
    public $weight, $height, $bmi;
    public $temperature, $heart_rate, $respiratory_rate;
    public $systolic_bp, $diastolic_bp;
    public $oxygen_saturation, $blood_sugar;
    public $pain_scale = 0;
    public $notes;

    // آخر قراءة (اختياري للتهيئة)
    public $latestVitalSign;

    public function mount($patient_id, $patient_visits_id, $age, $latestVitalSign = null)
    {
        $this->patient_id       = (int) $patient_id;
        $this->patient_visits_id= (int) $patient_visits_id;
        $this->age              = (int) $age;

        if ($latestVitalSign) {
            $this->latestVitalSign = $latestVitalSign instanceof vitalsign
                ? $latestVitalSign
                : vitalsign::find($latestVitalSign);

            if ($this->latestVitalSign) {
                $this->weight = $this->latestVitalSign->weight;
                $this->height = $this->latestVitalSign->height;
                $this->bmi    = $this->latestVitalSign->bmi;
            }
        }
    }

    public function render()
    {
        $vitals = vitalsign::where('id_patient', $this->patient_id)
            ->where('id_visit', $this->patient_visits_id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.vital-signs.form', [
            'vitals' => $vitals,
            'ranges' => $this->normalRanges(), // نبعث النطاقات للفيو
        ]);
    }

    protected function rules()
    {
        return [
            'weight'            => ['nullable','numeric','min:0','max:300'],
            'height'            => ['nullable','numeric','min:0','max:250'],
            'bmi'               => ['nullable','numeric','min:5','max:80'],
            'temperature'       => ['nullable','numeric','min:25','max:45'],
            'heart_rate'        => ['nullable','integer','min:30','max:250'],
            'respiratory_rate'  => ['nullable','integer','min:5','max:60'],
            'systolic_bp'       => ['nullable','integer','min:50','max:250'],
            'diastolic_bp'      => ['nullable','integer','min:30','max:150'],
            'oxygen_saturation' => ['nullable','integer','min:50','max:100'],
            'blood_sugar'       => ['nullable','integer','min:0','max:500'],
            'pain_scale'        => ['required','numeric','min:0','max:10'],
            'notes'             => ['nullable','string','max:2000'],
        ];
    }

    protected $messages = [
        'weight.numeric'            => 'قيمة الوزن غير صحيحة.',
        'height.numeric'            => 'قيمة الطول غير صحيحة.',
        'bmi.numeric'               => 'قيمة BMI غير صحيحة.',
        'temperature.numeric'       => 'درجة الحرارة يجب أن تكون رقمية.',
        'heart_rate.integer'        => 'النبض يجب أن يكون رقم صحيح.',
        'respiratory_rate.integer'  => 'معدل التنفس يجب أن يكون رقم صحيح.',
        'systolic_bp.integer'       => 'الضغط الانقباضي يجب أن يكون رقم صحيح.',
        'diastolic_bp.integer'      => 'الضغط الانبساطي يجب أن يكون رقم صحيح.',
        'oxygen_saturation.integer' => 'تشبع الأكسجين يجب أن يكون رقم صحيح.',
        'blood_sugar.integer'       => 'سكر الدم يجب أن يكون رقم صحيح.',
        'pain_scale.required'       => 'الرجاء تحديد مقياس الألم.',
        'pain_scale.max'            => 'أقصى قيمة لمقياس الألم 10.',
    ];

    public function updated($property)
    {
        $this->validateOnly($property);

        if (in_array($property, ['weight','height'])) {
            $this->recalculateBMI();
        }
    }

    public function recalculateBMI()
    {
        $w = (float) $this->weight;
        $h = (float) $this->height / 100.0;

        if ($w > 0 && $h > 0) {
            $this->bmi = round($w / ($h * $h), 1);
        } else {
            $this->bmi = null;
        }
    }

    public function save()
    {
        $data = $this->validate();

        $data['id_patient']        = $this->patient_id;
        $data['id_visit'] = $this->patient_visits_id;
        $data['created_by']        = Auth::id();

        vitalsign::create($data);

        // نحافظ على الوزن/الطول/BMI ونفرغ باقي الحقول
        foreach ([
            'temperature','heart_rate','respiratory_rate',
            'systolic_bp','diastolic_bp','oxygen_saturation',
            'blood_sugar','notes'
        ] as $f) {
            $this->{$f} = null;
        }
        $this->pain_scale = 0;

        session()->flash('message', __('تمت الإضافة بنجاح'));
        $this->resetPage();
    }

    public function normalRanges()
    {
        $isChild   = $this->age < 12;
        $isInfant  = $this->age < 2;
        $isElderly = $this->age > 65;

        return [
            'temp'   => $isInfant ? [36.5, 37.5] : [36.0, 37.2],
            'hr'     => $isInfant ? [120,160] : ($isChild ? [80,120] : [60,100]),
            'rr'     => $isInfant ? [30,60] : ($isChild ? [20,30] : [12,20]),
            // نحدد لكل من SYS و DIA نطاق مستقل
            'bp_sys' => $isChild ? [90 + ($this->age * 2), 120 + ($this->age)] : ($isElderly ? [90,140] : [90,120]),
            'bp_dia' => $isChild ? [60 + $this->age, 90 + ($this->age)]       : ($isElderly ? [60,100] : [60,90]),
            'oxy'    => $isElderly ? [92,100] : [95,100],
            'sugar'  => [70,140],
        ];
    }
}