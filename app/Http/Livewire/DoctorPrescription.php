<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Medical\{Medication, Prescription, PrescriptionItem, MedicationUnit, MedicationDuration, MedicationTaken, MedicationFrequency};

class DoctorPrescription extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🧠 تعريف المتغيرات
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $selectedMedications = [],
        $searchMedications = [],
        $medicationResults = [];
    public $selectedQuantities = [],
        $selectedUnits = [],
        $searchUnits = [],
        $unitResults = [];
    public $selectedDurations = [],
        $searchDurations = [],
        $durationResults = [];
    public $selectedFrequencies = [],
        $searchFrequencies = [],
        $frequencyResults = [];
    public $selectedTakens = [],
        $searchTakens = [],
        $takenResults = [];
    public $selectedEyes = [],
        $notes = [];

    public $editingPrescriptionId = null;
    public $pendingPrescription = null;
    public $actionType = null;

    protected $listeners = ['confirmPrescriptionAction', 'showPrescriptionChoice'];
    protected $paginationQueryString = ['page'];

    public function mount($patientId, $reservationId, $id_visit, $type_visit)
    {
        $this->id_patient = $patientId;
        $this->id_reservation = $reservationId;
        $this->id_visit = $id_visit;
        $this->type_visit = $type_visit;
        $this->addMedication();
    }

    // ➕ إضافة صف جديد
    public function addMedication()
    {
        $index = count($this->selectedMedications);
        $this->selectedMedications[$index] = null;
        $this->searchMedications[$index] = '';
        $this->medicationResults[$index] = [];

        $this->selectedQuantities[$index] = 1;
        $this->selectedUnits[$index] = '';
        $this->searchUnits[$index] = '';
        $this->unitResults[$index] = [];

        $this->selectedDurations[$index] = '';
        $this->searchDurations[$index] = '';
        $this->durationResults[$index] = [];

        $this->selectedFrequencies[$index] = '';
        $this->searchFrequencies[$index] = '';
        $this->frequencyResults[$index] = [];

        $this->selectedTakens[$index] = '';
        $this->searchTakens[$index] = '';
        $this->takenResults[$index] = [];

        $this->selectedEyes[$index] = 'both';
        $this->notes[$index] = '';
    }

    // ❌ حذف دواء
    public function removeMedication($index)
    {
        $fields = ['selectedMedications', 'searchMedications', 'medicationResults', 'selectedQuantities', 'selectedUnits', 'searchUnits', 'unitResults', 'selectedDurations', 'searchDurations', 'durationResults', 'selectedFrequencies', 'searchFrequencies', 'frequencyResults', 'selectedTakens', 'searchTakens', 'takenResults', 'selectedEyes', 'notes'];

        foreach ($fields as $field) {
            unset($this->{$field}[$index]);
            $this->{$field} = array_values($this->{$field});
        }

        $this->emit('show-toast', ['type' => 'info', 'message' => '🗑️ تم حذف الصف']);
    }

    // 🔁 تكرار صف
    public function duplicateRow($index)
    {
        if (!isset($this->selectedMedications[$index])) {
            return;
        }

        $newIndex = count($this->selectedMedications);
        foreach ($this->selectedMedications as $key => $value) {
            if ($value == $this->selectedMedications[$index]) {
                $this->emit('show-toast', ['type' => 'warning', 'message' => '⚠️ لا يمكن تكرار نفس الدواء مرتين!']);
                return;
            }
        }

        $fields = ['selectedMedications', 'searchMedications', 'selectedQuantities', 'selectedUnits', 'searchUnits', 'selectedDurations', 'searchDurations', 'selectedFrequencies', 'searchFrequencies', 'selectedTakens', 'searchTakens', 'selectedEyes', 'notes'];

        foreach ($fields as $field) {
            $this->{$field}[$newIndex] = $this->{$field}[$index] ?? null;
        }

        $this->emit('show-toast', ['type' => 'success', 'message' => '✅ تم تكرار الصف بنجاح']);
    }

    // 🔍 البحث عن دواء
    public function updatedSearchMedications($val, $i)
    {
        $this->medicationResults[$i] =
            strlen($val) < 2
                ? []
                : Medication::where('name->ar', 'like', "%$val%")
                    ->orWhere('name->en', 'like', "%$val%")
                    ->limit(10)
                    ->get();
    }

    public function selectMedication($i, $id)
    {
        $m = Medication::find($id);
        if (!$m) {
            return;
        }

        // منع التكرار
        if (in_array($id, $this->selectedMedications)) {
            $this->emit('show-toast', ['type' => 'warning', 'message' => '⚠️ الدواء مضاف بالفعل!']);
            return;
        }

        $this->selectedMedications[$i] = $id;
        $this->searchMedications[$i] = $m->getTranslation('name', app()->getLocale());
        $this->medicationResults[$i] = [];

        if ($m->default_unit_id) {
            $u = MedicationUnit::find($m->default_unit_id);
            $this->searchUnits[$i] = $u?->getTranslation('name', app()->getLocale());
        }

        if ($m->default_duration_id) {
            $d = MedicationDuration::find($m->default_duration_id);
            $this->searchDurations[$i] = $d?->getTranslation('name', app()->getLocale());
        }

        if ($m->default_taken_id) {
            $t = MedicationTaken::find($m->default_taken_id);
            $this->searchTakens[$i] = $t?->getTranslation('name', app()->getLocale());
        }

        if ($m->default_frequency_id) {
            $f = MedicationFrequency::find($m->default_frequency_id);
            $this->searchFrequencies[$i] = $f?->getTranslation('name', app()->getLocale());
        }
    }

    // 🧩 البحث في الوحدات
    public function updatedSearchUnits($val, $i)
    {
        $this->unitResults[$i] =
            strlen($val) < 1
                ? []
                : MedicationUnit::where('name->ar', 'like', "%$val%")
                    ->orWhere('name->en', 'like', "%$val%")
                    ->limit(10)
                    ->get();
    }

    public function selectUnit($i, $id)
    {
        $unit = MedicationUnit::find($id);
        if ($unit) {
            $this->searchUnits[$i] = $unit->getTranslation('name', app()->getLocale());
            $this->unitResults[$i] = [];
        }
    }

    // 🧩 البحث في المدة
    public function updatedSearchDurations($val, $i)
    {
        $this->durationResults[$i] =
            strlen($val) < 1
                ? []
                : MedicationDuration::where('name->ar', 'like', "%$val%")
                    ->orWhere('name->en', 'like', "%$val%")
                    ->limit(10)
                    ->get();
    }

    public function selectDuration($i, $id)
    {
        $duration = MedicationDuration::find($id);
        if ($duration) {
            $this->searchDurations[$i] = $duration->getTranslation('name', app()->getLocale());
            $this->durationResults[$i] = [];
        }
    }

    // 🧩 البحث في التكرار
    public function updatedSearchFrequencies($val, $i)
    {
        $this->frequencyResults[$i] =
            strlen($val) < 1
                ? []
                : MedicationFrequency::where('name->ar', 'like', "%$val%")
                    ->orWhere('name->en', 'like', "%$val%")
                    ->limit(10)
                    ->get();
    }

    public function selectFrequency($i, $id)
    {
        $frequency = MedicationFrequency::find($id);
        if ($frequency) {
            $this->searchFrequencies[$i] = $frequency->getTranslation('name', app()->getLocale());
            $this->frequencyResults[$i] = [];
        }
    }

    // 🧩 البحث في طرق التناول
    public function updatedSearchTakens($val, $i)
    {
        $this->takenResults[$i] =
            strlen($val) < 1
                ? []
                : MedicationTaken::where('name->ar', 'like', "%$val%")
                    ->orWhere('name->en', 'like', "%$val%")
                    ->limit(10)
                    ->get();
    }

    public function selectTaken($i, $id)
    {
        $taken = MedicationTaken::find($id);
        if ($taken) {
            $this->searchTakens[$i] = $taken->getTranslation('name', app()->getLocale());
            $this->takenResults[$i] = [];
        }
    }

    // ✅ حفظ الروشتة
    public function save()
    {
        if (empty(array_filter($this->selectedMedications))) {
            $this->emit('show-toast', ['type' => 'danger', 'message' => '❌ أضف دواء واحد على الأقل']);
            return;
        }

        DB::beginTransaction();
        try {
            $pres = Prescription::create([
                'id_patient' => $this->id_patient,
                'id_reservation' => $this->id_reservation,
                'id_visit' => $this->id_visit,
                'type_visit' => $this->type_visit,
                'doctor_id' => Auth::id(),
                'prescription_date' => now(),
                'status' => 'pending',
            ]);

            foreach ($this->selectedMedications as $i => $medicationId) {
                if (!$medicationId) {
                    continue;
                }

                PrescriptionItem::create([
                    'prescription_id' => $pres->id,
                    'medication_id' => $medicationId,
                    'quantity' => $this->selectedQuantities[$i] ?? 1,
                    'unit_text' => $this->searchUnits[$i] ?? '',
                    'duration_text' => $this->searchDurations[$i] ?? '',
                    'timer' => $this->searchFrequencies[$i] ?? '',
                    'taken_text' => $this->searchTakens[$i] ?? '',
                    'eye' => $this->selectedEyes[$i] ?? 'both',
                    'notes' => $this->notes[$i] ?? null,
                ]);
            }

            DB::commit();
         $this->dispatchBrowserEvent('show-toast', [
    'type' => 'success',
    'message' => '✅ تم الحفظ بنجاح',
]);


            $this->resetPrescriptionFields();
            $this->addMedication();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('show-toast', ['type' => 'danger', 'message' => $e->getMessage()]);
        }
    }
    // 🟡 عرض مودال اختيار الروشتة (قديمة / جديدة)
    public function showPrescriptionChoice()
    {
        $this->dispatchBrowserEvent('confirm-prescription-choice');
    }

    // ⚠️ التحقق من وجود روشتة بنفس اليوم
    public function checkExistingPrescription()
    {
        $exists = Prescription::where('id_patient', $this->id_patient)
            ->where('id_reservation', $this->id_reservation)
            ->where('id_visit', $this->id_visit)
            ->whereDate('prescription_date', now()->toDateString())
            ->first();

        if ($exists && !$this->editingPrescriptionId) {
            $this->pendingPrescription = $exists->id;
            $this->emit('showPrescriptionChoice');
        } else {
            $this->confirmPrescriptionAction($this->editingPrescriptionId ? 'update' : 'new');
        }
    }

    // ✅ تنفيذ الإجراء بعد الاختيار (إضافة / جديد)
    public function confirmPrescriptionAction($action)
    {
        $this->actionType = $action;
        $this->save();
    }

    private function resetPrescriptionFields()
    {
        $this->reset(['selectedMedications', 'searchMedications', 'medicationResults', 'selectedQuantities', 'selectedUnits', 'searchUnits', 'unitResults', 'selectedDurations', 'searchDurations', 'durationResults', 'selectedFrequencies', 'searchFrequencies', 'frequencyResults', 'selectedTakens', 'searchTakens', 'takenResults', 'selectedEyes', 'notes']);
    }

    public function render()
    {
        return view('livewire.doctor-prescription', [
            'previousPrescriptions' => Prescription::where('id_patient', $this->id_patient)->with('items')->latest()->paginate(10),
        ]);
    }
}