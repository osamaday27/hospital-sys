<?php
// app/Http/Livewire/Medical/Nursing/PatientAssessmentTabs.php
namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;

class PatientAssessmentTabs extends Component
{
public $patientId;
public $visitId;
public $reservationId;
public $currentTab = 'social_economic';
public $completed = [
'social_economic' => false,
'special_needs' => false,
'infectious' => false,
'motor_functional' => false,
'pain' => false,
'rights_duties' => false,
'allergies' => false,
'vital_signs' => false,
'nutrition_evaluation'=> false,
'fall_risk' => false,
'discharge_planning' => false,
];


public function mount($patientId, $visitId = null, $reservationId = null)
{
$this->patientId = $patientId;
$this->visitId = $visitId;
$this->reservationId = $reservationId;
}

public function setTab($tab)
{
$this->currentTab = $tab;
}

public function nextTab()
{
$keys = array_keys($this->completed);
$index = array_search($this->currentTab, $keys);
if ($index !== false && isset($keys[$index + 1])) {
$this->currentTab = $keys[$index + 1];
}
}

public function canAccessTab($tab)
{
$keys = array_keys($this->completed);
$index = array_search($tab, $keys);
$currentIndex = array_search($this->currentTab, $keys);
return $index <= $currentIndex; } public function render() { return
    view('livewire.medical.nursing.patient-assessment-tabs'); } }