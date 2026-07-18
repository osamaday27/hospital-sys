<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;
use App\Models\Medical\OphthalmicSurgicalReport;

class PatientReports extends Component
{
    public $patient_id;
    public $reports = [];
    public $selectedReport = null;

    protected $listeners = ['refreshReports' => '$refresh'];

    public function mount($patient_id)
    {
        $this->patient_id = $patient_id;
        $this->loadReports();
    }

    public function loadReports()
    {
        $this->reports = OphthalmicSurgicalReport::where('patient_id', $this->patient_id)
            ->latest('surgery_date')
            ->get();
    }

    public function viewReport($id)
    {
        $this->selectedReport = OphthalmicSurgicalReport::find($id);
    }

    public function closeReport()
    {
        $this->selectedReport = null;
    }

    public function render()
    {
        return view('livewire.medical.sheets.patient-reports');
    }
}