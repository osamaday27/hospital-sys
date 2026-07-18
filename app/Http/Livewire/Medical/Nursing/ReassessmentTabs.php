<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;

class ReassessmentTabs extends Component
{
    use WithPagination;

    public $patientId;
    public $visitId;
    public $reservationId;
    public $currentTab = 'infectious';

    protected $tabs = [
        'infectious',
        'motor_functional',
        'pain',
        'vital_signs',
        'fall_risk',
        'summary',
    ];

    public function mount($patientId, $visitId = null, $reservationId = null)
    {
        $this->patientId     = $patientId;
        $this->visitId       = $visitId;
        $this->reservationId = $reservationId;
    }

    public function setTab($tab)
    {
        if (in_array($tab, $this->tabs)) {
            $this->currentTab = $tab;
        }
    }

    public function nextTab()
    {
        $index = array_search($this->currentTab, $this->tabs);
        if ($index !== false && isset($this->tabs[$index + 1])) {
            $this->currentTab = $this->tabs[$index + 1];
        }
    }

    public function previousTab()
    {
        $index = array_search($this->currentTab, $this->tabs);
        if ($index > 0) {
            $this->currentTab = $this->tabs[$index - 1];
        }
    }

    public function getIsFirstTabProperty()
    {
        return $this->currentTab === $this->tabs[0];
    }

    public function getIsLastTabProperty()
    {
        return $this->currentTab === end($this->tabs);
    }

    public function render()
    {
        return view('livewire.medical.nursing.reassessment-tabs');
    }
}