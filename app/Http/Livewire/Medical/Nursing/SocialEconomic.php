<?php
// app/Http/Livewire/Medical/Nursing/SocialEconomic.php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\SocialEconomicAssessment;

class SocialEconomic extends Component
{
    use WithPagination;

    // حقول التعريف
    public $patient_id;
    public $visit_id;
    public $reservation_id;

    // خصائص الفحص
    public $living_alone = false;
    public $living_with_family = false;
    public $living_with_others = false;
    public $living_other_details;

    public $special_needs_none = false;
    public $hearing = false;
    public $speech = false;
    public $language_barrier = false;
    public $other_needs_details;

    public $single = false;
    public $married = false;
    public $has_children = false;
    public $number_of_children;

    public $smoking_no = false;
    public $smoking_yes = false;
    public $smoking_previous = false;
    public $cigarettes_per_day;

    public $no_psychological_issues = false;
    public $anxiety = false;
    public $anger = false;
    public $depression = false;

    public $can_afford = false;
    public $insured = false;
    public $cannot_afford = false;

    public $no_violence = false;
    public $violence_present = false;
    public $violence_details;

    public $not_working = false;
    public $working = false;
    public $occupation;

    public $notify_doctor_yes = false;
    public $notify_doctor_no = false;

    // للسجل المختار أثناء التحرير
    public $selectedRecord;

    protected $rules = [
        'living_other_details'  => 'nullable|string|max:255',
        'other_needs_details'   => 'nullable|string|max:255',
        'number_of_children'    => 'nullable|integer|min:0',
        'cigarettes_per_day'    => 'nullable|integer|min:0',
        'violence_details'      => 'nullable|string|max:255',
        'occupation'            => 'nullable|string|max:255',
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

        SocialEconomicAssessment::create([
            'patient_id'             => $this->patient_id,
            'visit_id'               => $this->visit_id,
            'reservation_id'         => $this->reservation_id,
            'user_add'               => Auth::id(),
            'living_alone'           => $this->living_alone,
            'living_with_family'     => $this->living_with_family,
            'living_with_others'     => $this->living_with_others,
            'living_other_details'   => $this->living_other_details,
            'special_needs_none'     => $this->special_needs_none,
            'hearing'                => $this->hearing,
            'speech'                 => $this->speech,
            'language_barrier'       => $this->language_barrier,
            'other_needs_details'    => $this->other_needs_details,
            'single'                 => $this->single,
            'married'                => $this->married,
            'has_children'           => $this->has_children,
            'number_of_children'     => $this->number_of_children,
            'smoking_no'             => $this->smoking_no,
            'smoking_yes'            => $this->smoking_yes,
            'smoking_previous'       => $this->smoking_previous,
            'cigarettes_per_day'     => $this->cigarettes_per_day,
            'no_psychological_issues'=> $this->no_psychological_issues,
            'anxiety'                => $this->anxiety,
            'anger'                  => $this->anger,
            'depression'             => $this->depression,
            'can_afford'             => $this->can_afford,
            'insured'                => $this->insured,
            'cannot_afford'          => $this->cannot_afford,
            'no_violence'            => $this->no_violence,
            'violence_present'       => $this->violence_present,
            'violence_details'       => $this->violence_details,
            'not_working'            => $this->not_working,
            'working'                => $this->working,
            'occupation'             => $this->occupation,
            'notify_doctor_yes'      => $this->notify_doctor_yes,
            'notify_doctor_no'       => $this->notify_doctor_no,
        ]);

        session()->flash('message', 'تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $record = SocialEconomicAssessment::findOrFail($id);
        $this->selectedRecord = $record;

        // ملء الحقول بقيم السجل لتحريرها
        foreach ([
            'living_alone', 'living_with_family', 'living_with_others', 'living_other_details',
            'special_needs_none', 'hearing', 'speech', 'language_barrier', 'other_needs_details',
            'single', 'married', 'has_children', 'number_of_children',
            'smoking_no', 'smoking_yes', 'smoking_previous', 'cigarettes_per_day',
            'no_psychological_issues', 'anxiety', 'anger', 'depression',
            'can_afford', 'insured', 'cannot_afford',
            'no_violence', 'violence_present', 'violence_details',
            'not_working', 'working', 'occupation',
            'notify_doctor_yes', 'notify_doctor_no'
        ] as $field) {
            $this->{$field} = $record->{$field};
        }
    }

    public function update()
    {
        $this->validate();

        $this->selectedRecord->update([
            'living_alone'           => $this->living_alone,
            'living_with_family'     => $this->living_with_family,
            'living_with_others'     => $this->living_with_others,
            'living_other_details'   => $this->living_other_details,
            'special_needs_none'     => $this->special_needs_none,
            'hearing'                => $this->hearing,
            'speech'                 => $this->speech,
            'language_barrier'       => $this->language_barrier,
            'other_needs_details'    => $this->other_needs_details,
            'single'                 => $this->single,
            'married'                => $this->married,
            'has_children'           => $this->has_children,
            'number_of_children'     => $this->number_of_children,
            'smoking_no'             => $this->smoking_no,
            'smoking_yes'            => $this->smoking_yes,
            'smoking_previous'       => $this->smoking_previous,
            'cigarettes_per_day'     => $this->cigarettes_per_day,
            'no_psychological_issues'=> $this->no_psychological_issues,
            'anxiety'                => $this->anxiety,
            'anger'                  => $this->anger,
            'depression'             => $this->depression,
            'can_afford'             => $this->can_afford,
            'insured'                => $this->insured,
            'cannot_afford'          => $this->cannot_afford,
            'no_violence'            => $this->no_violence,
            'violence_present'       => $this->violence_present,
            'violence_details'       => $this->violence_details,
            'not_working'            => $this->not_working,
            'working'                => $this->working,
            'occupation'             => $this->occupation,
            'notify_doctor_yes'      => $this->notify_doctor_yes,
            'notify_doctor_no'       => $this->notify_doctor_no,
        ]);

        session()->flash('message', 'تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord = null;
        $this->reset([
            'living_alone', 'living_with_family', 'living_with_others',
            'living_other_details', 'special_needs_none', 'hearing',
            'speech', 'language_barrier', 'other_needs_details', 'single',
            'married', 'has_children', 'number_of_children', 'smoking_no',
            'smoking_yes', 'smoking_previous', 'cigarettes_per_day',
            'no_psychological_issues', 'anxiety', 'anger', 'depression',
            'can_afford', 'insured', 'cannot_afford', 'no_violence',
            'violence_present', 'violence_details', 'not_working', 'working',
            'occupation', 'notify_doctor_yes', 'notify_doctor_no',
        ]);
    }

    public function render()
    {
        $records = SocialEconomicAssessment::with('user')
                    ->latest()
                    ->paginate(10);

        return view('livewire.medical.nursing.social-economic', compact('records'));
    }
}