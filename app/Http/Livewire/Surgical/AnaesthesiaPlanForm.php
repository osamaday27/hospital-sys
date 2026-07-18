<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use App\Models\medical\AnaesthesiaPlan;
use App\Models\medical\Medication;
use Illuminate\Support\Facades\Auth;

class AnaesthesiaPlanForm extends Component
{
    public $surgery_booking_id;

    public $npo_hours, $sedation_score, $pending_investigations;
    public $anaesthesia_type = [];
    public $delivery_method, $anaesthetic_name;
    public $fluid_management, $blood_booking = [];

    // 🩺 Premedication
    public $premedication_none = false;
    public $premedication = [];
    public $searchMedications = [];
    public $medicationResults = [];

    public $special_monitoring, $post_outcome, $discharge_to;
    public $post_analgesia_plan;
    public $doctor_name, $assistant_name, $form_date, $form_time;

    public function mount($surgery_booking_id)
    {
        $this->surgery_booking_id = $surgery_booking_id;

        $plan = AnaesthesiaPlan::where('surgery_booking_id', $surgery_booking_id)->first();

        if ($plan) {
            // تعبئة باقي البيانات
            $this->fill($plan->toArray());

            // ✅ تحويل premedication من JSON إلى array
            $decoded = json_decode($plan->premedication, true);
            $this->premedication = is_array($decoded) ? $decoded : [];

            // ✅ إعداد البحث لكل دواء محمّل
            foreach ($this->premedication as $i => $med) {
                $this->searchMedications[$i] = $med['drug'] ?? '';
                $this->medicationResults[$i] = [];
            }
        } else {
            // 🆕 خطة جديدة
            $this->premedication = [
                ['drug' => '', 'dose' => '', 'time' => '', 'route' => '']
            ];
            $this->searchMedications[0] = '';
            $this->medicationResults[0] = [];
        }

        // تعبئة الطبيب والتاريخ والوقت تلقائيًا
        $this->doctor_name = Auth::user()->name ?? '';
        $this->form_date = now()->format('Y-m-d');
        $this->form_time = now()->format('H:i');
    }

    // ➕ إضافة دواء جديد
    public function addPremedication()
    {
        $index = count($this->premedication);
        $this->premedication[$index] = ['drug' => '', 'dose' => '', 'time' => '', 'route' => ''];
        $this->searchMedications[$index] = '';
        $this->medicationResults[$index] = [];
    }

    // ❌ حذف صف
    public function removePremedication($index)
    {
        unset($this->premedication[$index], $this->searchMedications[$index], $this->medicationResults[$index]);
        $this->premedication = array_values($this->premedication);
        $this->searchMedications = array_values($this->searchMedications);
        $this->medicationResults = array_values($this->medicationResults);
    }

   

// ✅ البحث في الأدوية
public function updatedSearchMedications($value, $index)
{
    // لو المستخدم كتب أقل من حرفين → فضي النتائج
    if (strlen($value) < 2) {
        $this->medicationResults[$index] = [];
        return;
    }

    // جلب نتائج من جدول الأدوية
    $this->medicationResults[$index] = Medication::query()
        ->where('name->ar', 'like', "%$value%")
        ->orWhere('name->en', 'like', "%$value%")
        ->limit(10)
        ->get()
        ->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->getTranslation('name', app()->getLocale()),
        ])
        ->toArray();
}

// ✅ اختيار الدواء من القائمة أو حفظ اسم يدوي
public function selectMedication($index, $id = null)
{
    if ($id) {
        // ✅ في حالة اختيار من القائمة
        $med = Medication::find($id);
        if ($med) {
            $this->premedication[$index]['drug'] = $med->getTranslation('name', app()->getLocale());
            $this->searchMedications[$index] = $med->getTranslation('name', app()->getLocale());
        }
    } else {
        // ✅ في حالة كتابة اسم غير موجود
        $typedName = trim($this->searchMedications[$index] ?? '');
        if (!empty($typedName)) {
            $this->premedication[$index]['drug'] = $typedName;
        }
    }

    // في الحالتين نفرغ قائمة النتائج
    $this->medicationResults[$index] = [];
}


    // 💾 الحفظ
    public function save()
    {
        // ✅ تنظيف الـ JSON قبل الحفظ
        $cleanPremedication = [];
        foreach ($this->premedication as $item) {
            if (!empty($item['drug']) || !empty($item['dose']) || !empty($item['time']) || !empty($item['route'])) {
                $cleanPremedication[] = $item;
            }
        }

        AnaesthesiaPlan::updateOrCreate(
            ['surgery_booking_id' => $this->surgery_booking_id],
            [
                'npo_hours' => $this->npo_hours,
                'sedation_score' => $this->sedation_score,
                'pending_investigations' => $this->pending_investigations,
                'anaesthesia_type' => $this->anaesthesia_type,
                'delivery_method' => $this->delivery_method,
                'anaesthetic_name' => $this->anaesthetic_name,
                'fluid_management' => $this->fluid_management,
                'blood_booking' => $this->blood_booking,
                'premedication_none' => $this->premedication_none,
                'premedication' => json_encode($cleanPremedication, JSON_UNESCAPED_UNICODE),
                'special_monitoring' => $this->special_monitoring,
                'post_outcome' => $this->post_outcome,
                'discharge_to' => $this->discharge_to,
                'post_analgesia_plan' => $this->post_analgesia_plan,
                'doctor_name' => $this->doctor_name,
                'assistant_name' => $this->assistant_name,
                'form_date' => $this->form_date,
                'form_time' => $this->form_time,
            ]
        );

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => '✅ تم حفظ خطة التخدير بنجاح',
        ]);
    }

    public function render()
    {
        return view('livewire.surgical.anaesthesia-plan-form');
    }
}