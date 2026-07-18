<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use App\Models\medical\PreAnesthesiaAssessment;
use App\Models\administrative\adm_b_surgery_bookings;
use Illuminate\Support\Facades\Auth;

class PreAnesthesiaAssessmentForm extends Component
{
    public $surgery_booking_id;

    public $weight, $height, $age, $gender, $diagnosis, $surgeon_unit, $proposed_operation;
    public $history = [];
    public $drug_therapy = [];
    public $physical_exam = [];
    public $investigations = [];
    public $investigations_comments;
    public $lab_works = [];
    public $asa, $consent, $fit_for_operation, $fit_for_operation_specify;
    public $prepare_fresh_blood, $prepare_rbc, $prepare_ffp, $prepare_platelets;
    public $please_give_1, $please_give_2, $anesthesia_plan;
    public $form_date, $form_time, $physician_signature;
    public $assessment_id;
    public function mount($surgery_booking_id)
    {
        $this->surgery_booking_id = $surgery_booking_id;

        $booking = adm_b_surgery_bookings::findOrFail($surgery_booking_id);
        $this->age = $booking->age;
        $this->gender = $booking->gender;

        $assessment = PreAnesthesiaAssessment::where('surgery_booking_id', $surgery_booking_id)->first();

        if ($assessment) {
            $this->assessment_id = $assessment->id;
            $this->weight = $assessment->weight;
            $this->height = $assessment->height;
            $this->diagnosis = $assessment->diagnosis;
            $this->surgeon_unit = $assessment->surgeon_unit;
            $this->proposed_operation = $assessment->proposed_operation;
            $this->history = $assessment->history;
            $this->drug_therapy = $assessment->drug_therapy;
            $this->physical_exam = $assessment->physical_exam;
            $this->investigations = $assessment->investigations;
            $this->investigations_comments = $assessment->investigations_comments;
            $this->lab_works = $assessment->lab_works;
            $this->asa = $assessment->asa;
            $this->consent = $assessment->consent;
            $this->fit_for_operation = $assessment->fit_for_operation;
            $this->fit_for_operation_specify = $assessment->fit_for_operation_specify;
            $this->prepare_fresh_blood = $assessment->prepare_fresh_blood;
            $this->prepare_rbc = $assessment->prepare_rbc;
            $this->prepare_ffp = $assessment->prepare_ffp;
            $this->prepare_platelets = $assessment->prepare_platelets;
            $this->please_give_1 = $assessment->please_give_1;
            $this->please_give_2 = $assessment->please_give_2;
            $this->anesthesia_plan = $assessment->anesthesia_plan;
            $this->physician_signature = $assessment->physician_signature;
            $this->form_date = $assessment->form_date;
            $this->form_time = $assessment->form_time;
        } else {
            $this->drug_therapy = array_fill(0, 3, ['drug' => '', 'dose' => '', 'duration' => '']);

            $this->physical_exam = [
                'general_condition' => '',
                'airway_assessment' => '',
                'blood_pressure' => '',
                'heart_rate' => '',
                'temperature' => '',
                'heart' => '',
                'chest' => '',
                'abdomen' => '',
            ];

            $this->investigations = [
                'ecg' => '',
                'chest_xray' => '',
                'abd_ultrasound' => '',
                'echocardiography' => '',
            ];

            $labKeys = ['hb', 'urea', 'fbs', 'blood_group', 'creatinine', 'bilirubin_dt', 'esr', 'ast', 'alt', 'na', 'k', 'platelets', 'hbs_ag', 'hcv_ab', 'hiv_ab', 'pt', 'pc', 'inr', 'ptt'];

            foreach ($labKeys as $key) {
                $this->lab_works[$key] = ['checked' => false, 'value' => ''];
            }
            // ✅ تعبئة التاريخ والوقت والتوقيع تلقائياً
            $this->physician_signature = Auth::user()->name ?? 'غير محدد';
            $this->form_date = now()->format('Y-m-d');
            $this->form_time = now()->format('H:i');
        }
    }

    public function addDrugTherapy()
    {
        $this->drug_therapy[] = ['drug' => '', 'dose' => '', 'duration' => ''];
    }

    public function removeDrugTherapy($index)
    {
        unset($this->drug_therapy[$index]);
        $this->drug_therapy = array_values($this->drug_therapy);
    }

    public function save()
    {
        // ✅ في حال لم يملأ الطبيب البيانات، نملأها تلقائيًا
        if (empty($this->physician_signature)) {
            $this->physician_signature = Auth::user()->name ?? 'غير محدد';
        }
        if (empty($this->form_date)) {
            $this->form_date = now()->format('Y-m-d');
        }
        if (empty($this->form_time)) {
            $this->form_time = now()->format('H:i');
        }

        $data = [
            'surgery_booking_id' => $this->surgery_booking_id,
            'weight' => $this->weight,
            'height' => $this->height,
            'age' => $this->age,
            'gender' => $this->gender,
            'diagnosis' => $this->diagnosis,
            'surgeon_unit' => $this->surgeon_unit,
            'proposed_operation' => $this->proposed_operation,
            'history' => $this->history,
            'drug_therapy' => $this->drug_therapy,
            'physical_exam' => $this->physical_exam,
            'investigations' => $this->investigations,
            'investigations_comments' => $this->investigations_comments,
            'lab_works' => $this->lab_works,
            'asa' => $this->asa,
            'consent' => $this->consent,
            'fit_for_operation' => $this->fit_for_operation,
            'fit_for_operation_specify' => $this->fit_for_operation_specify,
            'prepare_fresh_blood' => $this->prepare_fresh_blood,
            'prepare_rbc' => $this->prepare_rbc,
            'prepare_ffp' => $this->prepare_ffp,
            'prepare_platelets' => $this->prepare_platelets,
            'please_give_1' => $this->please_give_1,
            'please_give_2' => $this->please_give_2,
            'anesthesia_plan' => $this->anesthesia_plan,
            'physician_signature' => $this->physician_signature,
            'form_date' => $this->form_date,
            'form_time' => $this->form_time,
            'user_id' => Auth::id(),
        ];

        if ($this->assessment_id) {
            PreAnesthesiaAssessment::where('id', $this->assessment_id)->update($data);
        } else {
            PreAnesthesiaAssessment::create($data);
        }

        session()->flash('success', __('surgical_trans.success_message'));
        // ✅ عرض Toast فوري بعد الحفظ
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => __('surgical_trans.success_message'),
        ]);
    }

    public function render()
    {
        return view('livewire.surgical.pre-anesthesia-assessment-form');
    }
}