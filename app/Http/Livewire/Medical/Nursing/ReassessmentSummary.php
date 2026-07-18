<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use App\Models\NursingAssessmentSummary as SummaryModel;

class NursingAssessmentSummary extends Component
{
    public $summaries;

    public $self_care = 'نعم';
    public $instructions_clear = 'نعم';
    public $assistance_means;
    public $doctor_notified = 'لا';

    public $socioeconomic_status = 'لم يتغير';
    public $socioeconomic_comment;
    public $special_needs_status = 'لم يتغير';
    public $special_needs_comment;
    public $infectious_disease = 'لا';
    public $suspected_disease;
    public $functional_status = 'لم يتغير';
    public $functional_comment;
    public $pain_status = 'لا يوجد';
    public $pain_level;
    public $allergy_status = 'لم يتغير';
    public $allergy_comment;
    public $critical_results = 'لا';
    public $nutritional_status = 'لم يتغير';
    public $nutritional_comment;

    public $nurse_name;
    public $nurse_signature;
    public $nurse_datetime;
    public $doctor_name;
    public $doctor_signature;
    public $doctor_datetime;

    protected $rules = [
        'self_care'              => 'required|in:نعم,لا',
        'instructions_clear'     => 'required|in:نعم,لا',
        'assistance_means'       => 'nullable|string|max:255',
        'doctor_notified'        => 'required|in:نعم,لا',

        'socioeconomic_status'   => 'required|in:لم يتغير,تغير',
        'socioeconomic_comment'  => 'required_if:socioeconomic_status,تغير|string|max:255',
        'special_needs_status'   => 'required|in:لم يتغير,تغير',
        'special_needs_comment'  => 'required_if:special_needs_status,تغير|string|max:255',
        'infectious_disease'     => 'required|in:لا,نعم',
        'suspected_disease'      => 'required_if:infectious_disease,نعم|string|max:255',
        'functional_status'      => 'required|in:لم يتغير,تغير',
        'functional_comment'     => 'required_if:functional_status,تغير|string|max:255',
        'pain_status'            => 'required|in:لا يوجد,يوجد',
        'pain_level'             => 'required_if:pain_status,يوجد|numeric|min:0|max:10',
        'allergy_status'         => 'required|in:لم يتغير,تغير',
        'allergy_comment'        => 'required_if:allergy_status,تغير|string|max:255',
        'critical_results'       => 'required|in:لا,نعم',
        'nutritional_status'     => 'required|in:لم يتغير,تغير',
        'nutritional_comment'    => 'required_if:nutritional_status,تغير|string|max:255',

        'nurse_name'             => 'required|string|max:255',
        'nurse_signature'        => 'nullable|string|max:255',
        'nurse_datetime'         => 'required|date',
        'doctor_name'            => 'required|string|max:255',
        'doctor_signature'       => 'nullable|string|max:255',
        'doctor_datetime'        => 'required|date',
    ];

    public function mount()
    {
        $this->loadSummaries();
    }

    public function loadSummaries()
    {
        $this->summaries = SummaryModel::latest()->get();
    }

    public function saveSummary()
    {
        $data = $this->validate();
        SummaryModel::create($data);
        session()->flash('message', 'تم حفظ ملخص التقييم التمريضي بنجاح');
        $this->resetForm();
        $this->loadSummaries();
    }

    public function resetForm()
    {
        $this->reset([
            'self_care','instructions_clear','assistance_means','doctor_notified',
            'socioeconomic_status','socioeconomic_comment','special_needs_status','special_needs_comment',
            'infectious_disease','suspected_disease','functional_status','functional_comment',
            'pain_status','pain_level','allergy_status','allergy_comment','critical_results',
            'nutritional_status','nutritional_comment','nurse_name','nurse_signature',
            'nurse_datetime','doctor_name','doctor_signature','doctor_datetime'
        ]);
        $this->self_care = 'نعم';
        $this->instructions_clear = 'نعم';
        $this->doctor_notified = 'لا';
        $this->socioeconomic_status = 'لم يتغير';
        $this->special_needs_status = 'لم يتغير';
        $this->infectious_disease = 'لا';
        $this->functional_status = 'لم يتغير';
        $this->pain_status = 'لا يوجد';
        $this->allergy_status = 'لم يتغير';
        $this->critical_results = 'لا';
        $this->nutritional_status = 'لم يتغير';
    }

    public function render()
    {
        return view('livewire.medical.nursing.nursing-assessment-summary');
    }
}