<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Medical\MDischargePlanning;

class DischargePlanning extends Component
{
    use WithPagination;

    public $patient_id, $visit_id, $reservation_id;
    public $selectedRecord;

    // Form fields
    public $care_place;
    public $help_at_home;
    public $self_care;
    public $instructions_clear;
    public $need_assist_reminder = false;
    public $need_assist_medication = false;
    public $need_assist_stairs = false;
    public $need_assist_toilet = false;
    public $need_assist_bathing = false;
    public $doctor_alert;
    public $social_exam;
    public $social_exam_details;
    public $special_needs;
    public $special_needs_details;
    public $infectious;
    public $infectious_details;
    public $functional_exam;
    public $pain;
    public $pain_degree;
    public $allergy;
    public $allergy_details;
    public $critical_results;
    public $nutrition_exam;
    public $doctor_signature;
    public $doctor_id;
    public $doctor_date;
    public $doctor_time;

    protected $rules = [
        'care_place'           => 'nullable|string|max:255',
        'social_exam_details'  => 'nullable|string|max:255',
        'special_needs_details'=> 'nullable|string|max:255',
        'infectious_details'   => 'nullable|string|max:255',
        'pain_degree'          => 'nullable|string|max:50',
        'allergy_details'      => 'nullable|string|max:255',
        'doctor_signature'     => 'nullable|string|max:255',
        'doctor_id'            => 'nullable|string|max:50',
    ];

    public function mount($patientId, $visitId = null, $reservationId = null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }

    public function save()
    {
        $this->validate();

        // Map single fields to DB columns
        $data = [
            'patient_id'       => $this->patient_id,
            'visit_id'         => $this->visit_id,
            'reservation_id'   => $this->reservation_id,
            'user_add'         => Auth::id(),
            'nurse_signature'  => Auth::user()->name,
            'nurse_id'         => Auth::id(),
            'nurse_date'       => Carbon::now()->toDateString(),
            'nurse_time'       => Carbon::now()->format('H:i'),
            'care_place'       => $this->care_place,
            'help_at_home_yes' => $this->help_at_home == '1',
            'help_at_home_no'  => $this->help_at_home == '0',
            'self_care_yes'    => $this->self_care == '1',
            'self_care_no'     => $this->self_care == '0',
            'instructions_clear_yes' => $this->instructions_clear == '1',
            'instructions_clear_no'  => $this->instructions_clear == '0',

            'need_assist_reminder'=> $this->need_assist_reminder,
            'need_assist_medication'=> $this->need_assist_medication,
            'need_assist_stairs'=> $this->need_assist_stairs,
            'need_assist_toilet'=> $this->need_assist_toilet,
            'need_assist_bathing'=> $this->need_assist_bathing,

            'doctor_alert_yes' => $this->doctor_alert == '1',
            'doctor_alert_no'  => $this->doctor_alert == '0',

            'social_exam_normal'=> $this->social_exam == '1',
            'social_exam_abnormal'=> $this->social_exam == '0',
            'social_exam_details'=> $this->social_exam_details,

            'special_needs_yes'=> $this->special_needs == '1',
            'special_needs_no' => $this->special_needs == '0',
            'special_needs_details'=> $this->special_needs_details,

            'infectious_yes'=> $this->infectious == '1',
            'infectious_no' => $this->infectious == '0',
            'infectious_details'=> $this->infectious_details,

            'functional_normal'=> $this->functional_exam == '1',
            'functional_abnormal'=> $this->functional_exam == '0',

            'pain_no'  => $this->pain == '0',
            'pain_yes' => $this->pain == '1',
            'pain_degree'=> $this->pain_degree,

            'allergy_no'  => $this->allergy == '0',
            'allergy_yes' => $this->allergy == '1',
            'allergy_details'=> $this->allergy_details,

            'critical_results_yes'=> $this->critical_results == '1',
            'critical_results_no'=> $this->critical_results == '0',

            'nutrition_normal'=> $this->nutrition_exam == '1',
            'nutrition_abnormal'=> $this->nutrition_exam == '0',

            'doctor_signature'=> $this->doctor_signature,
            'doctor_id'       => $this->doctor_id,
            'doctor_date'     => $this->doctor_date,
            'doctor_time'     => $this->doctor_time,
        ];

        MDischargePlanning::create($data);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->resetForm();
    }

    public function viewRecord($id)
    {
        $this->selectedRecord = MDischargePlanning::findOrFail($id);
        $rec = $this->selectedRecord;

        $this->care_place = $rec->care_place;
        $this->help_at_home = $rec->help_at_home_yes ? '1' : '0';
        $this->self_care    = $rec->self_care_yes ? '1' : '0';
        $this->instructions_clear = $rec->instructions_clear_yes ? '1' : '0';

        $this->need_assist_reminder   = $rec->need_assist_reminder;
        $this->need_assist_medication = $rec->need_assist_medication;
        $this->need_assist_stairs     = $rec->need_assist_stairs;
        $this->need_assist_toilet     = $rec->need_assist_toilet;
        $this->need_assist_bathing    = $rec->need_assist_bathing;

        $this->doctor_alert = $rec->doctor_alert_yes ? '1' : '0';
        $this->social_exam  = $rec->social_exam_normal ? '1' : '0';
        $this->social_exam_details = $rec->social_exam_details;

        $this->special_needs = $rec->special_needs_yes ? '1' : '0';
        $this->special_needs_details = $rec->special_needs_details;

        $this->infectious = $rec->infectious_yes ? '1' : '0';
        $this->infectious_details = $rec->infectious_details;

        $this->functional_exam = $rec->functional_normal ? '1' : '0';
        $this->pain = $rec->pain_yes ? '1' : '0';
        $this->pain_degree = $rec->pain_degree;

        $this->allergy = $rec->allergy_yes ? '1' : '0';
        $this->allergy_details = $rec->allergy_details;

        $this->critical_results = $rec->critical_results_yes ? '1' : '0';
        $this->nutrition_exam = $rec->nutrition_normal ? '1' : '0';

        $this->doctor_signature = $rec->doctor_signature;
        $this->doctor_id        = $rec->doctor_id;
        $this->doctor_date      = $rec->doctor_date;
        $this->doctor_time      = $rec->doctor_time;
    }

    public function update()
    {
        $this->save(); // reuse save mapping for update
        session()->flash('message', 'تم التعديل بنجاح');
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'care_place','help_at_home','self_care','instructions_clear',
            'need_assist_reminder','need_assist_medication','need_assist_stairs',
            'need_assist_toilet','need_assist_bathing',
            'doctor_alert','social_exam','social_exam_details',
            'special_needs','special_needs_details','infectious','infectious_details',
            'functional_exam','pain','pain_degree','allergy','allergy_details',
            'critical_results','nutrition_exam','doctor_signature',
            'doctor_id','doctor_date','doctor_time'
        ]);
    }

    public function render()
    {
        $records = MDischargePlanning::where('patient_id', $this->patient_id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.discharge-planning', compact('records'));
    }
}