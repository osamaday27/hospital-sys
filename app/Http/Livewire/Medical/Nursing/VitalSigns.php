<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\vitalsign;

class VitalSigns extends Component
{
    use WithPagination;

    public $patient_id, $visit_id, $reservation_id;
    public $temperature, $respiratory_rate, $blood_pressure, $pulse, $blood_sugar, $weight, $height;
    public $selectedRecord;

    protected $rules = [
        'temperature'       => 'required|numeric',
        'respiratory_rate'  => 'required|numeric',
        'blood_pressure'    => 'required|regex:/^\d+\/\d+$/',
        'pulse'             => 'required|numeric',
        'blood_sugar'       => 'required|numeric',
        'weight'            => 'required|numeric',
        'height'            => 'required|numeric',
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

        [$sys, $dia] = explode('/', $this->blood_pressure);
        $bmi = $this->height > 0 ? $this->weight / (($this->height/100)**2) : null;

        vitalsign::create([
            'id_patient'        => $this->patient_id,
            'id_visit'          => $this->visit_id,
            'id_reservation'    => $this->reservation_id,
            'type_visit'        => null,
            'temperature'       => $this->temperature,
            'heart_rate'        => $this->pulse,
            'respiratory_rate'  => $this->respiratory_rate,
            'systolic_bp'       => $sys,
            'diastolic_bp'      => $dia,
            'oxygen_saturation' => null,
            'blood_sugar'       => $this->blood_sugar,
            'weight'            => $this->weight,
            'height'            => $this->height,
            'bmi'               => $bmi,
            'pain_scale'        => null,
            'notes'             => null,
            'user_id'           => Auth::id(),
        ]);

        session()->flash('message','تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $r = vitalsign::findOrFail($id);
        $this->selectedRecord = $r;
        $this->temperature      = $r->temperature;
        $this->respiratory_rate = $r->respiratory_rate;
        $this->pulse            = $r->heart_rate;
        $this->blood_sugar      = $r->blood_sugar;
        $this->weight           = $r->weight;
        $this->height           = $r->height;
        $this->blood_pressure   = $r->systolic_bp . '/' . $r->diastolic_bp;
    }

    public function update()
    {
        $this->validate();

        [$sys, $dia] = explode('/', $this->blood_pressure);
        $bmi = $this->height > 0 ? $this->weight / (($this->height/100)**2) : null;

        $this->selectedRecord->update([
            'temperature'       => $this->temperature,
            'heart_rate'        => $this->pulse,
            'respiratory_rate'  => $this->respiratory_rate,
            'systolic_bp'       => $sys,
            'diastolic_bp'      => $dia,
            'blood_sugar'       => $this->blood_sugar,
            'weight'            => $this->weight,
            'height'            => $this->height,
            'bmi'               => $bmi,
        ]);

        session()->flash('message','تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->reset([
            'temperature','respiratory_rate','blood_pressure',
            'pulse','blood_sugar','weight','height','selectedRecord'
        ]);
    }

    public function render()
    {
        $records = vitalsign::with('user')
            ->latest()
            ->paginate(10);

        return view('livewire.medical.nursing.vital-signs', compact('records'));
    }
}