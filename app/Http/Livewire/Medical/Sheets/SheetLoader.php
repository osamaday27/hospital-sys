<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;

class SheetLoader extends Component
{
    public $patient_id;
    public $reservationId; // ✅ أضف هذا السطر
    public $component = null;

    public function loadSheet($type)
    {
        $map = [
            'icl' => 'medical.sheets.icl',
            'corneal-cross-linking' => 'medical.sheets.corneal-cross-linking',
            'keratoplasty' => 'medical.sheets.keratoplasty',
            'glaucoma-surgery' => 'medical.sheets.glaucoma-surgery',
            'pars-plana-vitrectomy' => 'medical.sheets.pars-plana-vitrectomy',
            'phakoemulsification' => 'medical.sheets.phakoemulsification',
            'squint-surgery' => 'medical.sheets.squint-surgery',
            'evisceration-enucleation' => 'medical.sheets.evisceration-enucleation',
            'oct-nomenclature' => 'medical.sheets.oct-nomenclature',
                'patient-reports' => 'medical.sheets.patient-reports', // ✅ الزر الجديد
        ];

        $this->component = $map[$type] ?? null;
    }

    public function render()
    {
        return view('livewire.medical.sheets.sheet-loader', [
            'reservationId' => $this->reservationId, // ✅ أرسلها للفيو
            'patient_id' => $this->patient_id,
        ]);
    }
}