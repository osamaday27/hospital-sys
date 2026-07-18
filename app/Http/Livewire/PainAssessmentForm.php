<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\models\PainAssessment; // حسب اسم الموديل
use Illuminate\Support\Facades\Auth;

class PainAssessmentForm extends Component
{
    public $assessment = [''];
    public $degree = [''];
    public $pain_location = [''];
    public $continuous_intermittent = [''];
    public $pain_type_t = [''];
    public $increases_during = [''];
    public $decreases_during = [''];
    public $treatment = [''];
    public $reassessment = [''];

    public $pain_typef;
    public $digital_assessment;
    public $face_assessment;
    public $child_assessment;
    public $notes;
    public $status = "pending";

    public $id_patient;
    public $id_visit;
    public $id_reservation;
    public $type_visit;
    public $user_id;

    public function mount()
    {
        $this->user_id = Auth::id();
        // أو تمرر القيم من controller عند النداء
    }

    public function addRow()
    {
        $this->assessment[] = '';
        $this->degree[] = '';
        $this->pain_location[] = '';
        $this->continuous_intermittent[] = '';
        $this->pain_type_t[] = '';
        $this->increases_during[] = '';
        $this->decreases_during[] = '';
        $this->treatment[] = '';
        $this->reassessment[] = '';
    }

    public function submit()
    {
        $this->validate([
            'assessment.*' => 'nullable|string',
            'degree.*' => 'nullable|numeric',
            'pain_location.*' => 'nullable|string',
            'notes' => 'nullable|string',
            // ... validation rules حسب الحاجة
        ]);

        // حفظ البيانات
        foreach ($this->assessment as $index => $val) {
            if ($val || $this->degree[$index]) {
                PainAssessment::create([
                    'assessment' => $val,
                    'degree' => $this->degree[$index],
                    'pain_location' => $this->pain_location[$index],
                    'continuous_intermittent' => $this->continuous_intermittent[$index],
                    'pain_type_t' => $this->pain_type_t[$index],
                    'increases_during' => $this->increases_during[$index],
                    'decreases_during' => $this->decreases_during[$index],
                    'treatment' => $this->treatment[$index],
                    'reassessment' => $this->reassessment[$index],
                    'pain_typef' => $this->pain_typef,
                    'digital_assessment' => $this->digital_assessment,
                    'face_assessment' => $this->face_assessment,
                    'child_assessment' => $this->child_assessment,
                    'notes' => $this->notes,
                    'status' => $this->status,
                    'id_patient' => $this->id_patient,
                    'id_visit' => $this->id_visit,
                    'id_reservation' => $this->id_reservation,
                    'type_visit' => $this->type_visit,
                    'user_id' => $this->user_id,
                ]);
            }
        }

        session()->flash('success', 'تم حفظ التقييمات بنجاح!');
        $this->reset(); // أو إعادة ملء القيم الافتراضية حسب الحاجة
    }

    public function render()
    {
        return view('livewire.pain-assessment-form');
    }
}