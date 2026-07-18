<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Medical\poperative_plan;
use App\Models\Medical\PostOperativeMedication;
use App\Models\Medical\PostOperativeFluid;
use Illuminate\Support\Facades\Auth;

class PostOperativePlan extends Component
{
    public $patient_id;
    public $visit_id;
    public $booking_id;

    public $surgery_procedure;
    public $transfer_to;

    public $positioning;
    public $positioning_other;
    public $mobility;
    public $mobility_other;
    public $feeding;
    public $feeding_other;
    public $required_monitoring = false;

    public $wound;
    public $urine_output;

    public $instructions_post_anesthesia;
    public $investigations;

    public $general_instructions;
    public $follow_up;

    public $doctor_name;
    public $date;
    public $time;

    public $medications = [];
    public $fluids = [];

    public $currentPlanId = null;

    public function mount($patient_id, $visit_id = null, $booking_id = null)
    {
        $this->patient_id = $patient_id;
        $this->visit_id = $visit_id;
        $this->booking_id = $booking_id;

        $this->date = now()->toDateString();
        $this->time = now()->format('H:i');

        // تحميل آخر سجل
        $latestPlan = poperative_plan::with(['medications', 'fluids'])
            ->where('patient_id', $this->patient_id)
            ->when($this->visit_id, fn($q) => $q->where('visit_id', $this->visit_id))
            ->orderByDesc('created_at')
            ->first();

        if ($latestPlan) {
            $this->currentPlanId = $latestPlan->id;

            $this->surgery_procedure = $latestPlan->surgery_procedure;
            $this->transfer_to = $latestPlan->transfer_to;
            $this->positioning = $latestPlan->positioning;
            $this->positioning_other = $latestPlan->positioning_other;
            $this->mobility = $latestPlan->mobility;
            $this->mobility_other = $latestPlan->mobility_other;
            $this->feeding = $latestPlan->feeding;
            $this->feeding_other = $latestPlan->feeding_other;
            $this->required_monitoring = $latestPlan->required_monitoring;
            $this->wound = $latestPlan->wound;
            $this->urine_output = $latestPlan->urine_output;
            $this->instructions_post_anesthesia = $latestPlan->instructions_post_anesthesia;
            $this->investigations = $latestPlan->investigations;
            $this->general_instructions = $latestPlan->general_instructions;
            $this->follow_up = $latestPlan->follow_up;
            $this->doctor_name = $latestPlan->doctor_name;
            $this->date = $latestPlan->date;
            $this->time = $latestPlan->time;

            $this->medications = [];
            foreach ($latestPlan->medications as $med) {
                $this->medications[] = [
                    'medication' => $med->medication,
                    'dose' => $med->dose,
                    'route' => $med->route,
                    'frequency' => $med->frequency,
                    'duration' => $med->duration,
                ];
            }

            $this->fluids = [];
            foreach ($latestPlan->fluids as $fluid) {
                $this->fluids[] = [
                    'fluid_name' => $fluid->fluid_name,
                    'volume' => $fluid->volume,
                    'additive_name' => $fluid->additive_name,
                    'infusion_rate' => $fluid->infusion_rate,
                ];
            }
        }
    }

    public function addMedication()
    {
        $this->medications[] = [
            'medication' => '',
            'dose' => '',
            'route' => '',
            'frequency' => '',
            'duration' => '',
        ];
    }

    public function removeMedication($index)
    {
        unset($this->medications[$index]);
        $this->medications = array_values($this->medications);
    }

    public function addFluid()
    {
        $this->fluids[] = [
            'fluid_name' => '',
            'volume' => '',
            'additive_name' => '',
            'infusion_rate' => '',
        ];
    }

    public function removeFluid($index)
    {
        unset($this->fluids[$index]);
        $this->fluids = array_values($this->fluids);
    }

    public function save()
    {
        if ($this->currentPlanId) {
            // تحديث
            $plan = poperative_plan::find($this->currentPlanId);
            $plan->update($this->getPlanData());
            $plan->medications()->delete();
            $plan->fluids()->delete();
        } else {
            // إنشاء
            $plan = poperative_plan::create(array_merge($this->getPlanData(), [
                'patient_id' => $this->patient_id,
                'visit_id' => $this->visit_id,
                'booking_id' => $this->booking_id,
                'created_by' => Auth::id(),
            ]));

            $this->currentPlanId = $plan->id;
        }

        // إعادة إضافة الأدوية
        foreach ($this->medications as $med) {
            $plan->medications()->create($med);
        }

        // إعادة إضافة السوائل
        foreach ($this->fluids as $fluid) {
            $plan->fluids()->create($fluid);
        }

        session()->flash('success', 'Post-Operative Plan saved successfully.');
    }

    private function getPlanData()
    {
        return [
            'surgery_procedure' => $this->surgery_procedure,
            'transfer_to' => $this->transfer_to,
            'positioning' => $this->positioning,
            'positioning_other' => $this->positioning_other,
            'mobility' => $this->mobility,
            'mobility_other' => $this->mobility_other,
            'feeding' => $this->feeding,
            'feeding_other' => $this->feeding_other,
            'required_monitoring' => $this->required_monitoring,
            'wound' => $this->wound,
            'urine_output' => $this->urine_output,
            'instructions_post_anesthesia' => $this->instructions_post_anesthesia,
            'investigations' => $this->investigations,
            'general_instructions' => $this->general_instructions,
            'follow_up' => $this->follow_up,
            'doctor_name' => $this->doctor_name,
            'date' => $this->date,
            'time' => $this->time,
        ];
    }

    public function render()
    {
        return view('livewire.surgical.post-operative-plan');
    }
}