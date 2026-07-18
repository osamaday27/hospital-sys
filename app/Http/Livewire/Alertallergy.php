<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\models\medical\m_d_allergy;

class Alertallergy extends Component
{
    public $id_patient;

    public function mount($id_patient)
    {
        $this->id_patient = $id_patient;
    }

    public function render()
    {
        return view('livewire.alertallergy', [
            'latest_allergies' => m_d_allergy::where('id_patient', $this->id_patient)
                ->latest()
                ->take(3)
                ->get(),

            'allergy_food_count' => m_d_allergy::where('id_patient', $this->id_patient)->where('food_allergy','!=',null)->count(),
            'allergy_durg_count' => m_d_allergy::where('id_patient', $this->id_patient)->where('drug_allergy','!=',null)->count(),
            'allergy_count' => m_d_allergy::where('id_patient', $this->id_patient)->count(),

            'allergies' => m_d_allergy::where('id_patient', $this->id_patient)
                ->latest()
                ->get(),
        ]);
    }
}