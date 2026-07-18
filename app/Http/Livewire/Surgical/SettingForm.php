<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Administrative\Surgicaldatasetting;
use App\Models\Finance\Service;
use App\Models\Finance\FinAsset;
use App\Models\medical\Medication;
use Illuminate\Support\Facades\Crypt;

class SettingForm extends Component
{
    public $service_id;
    public $service;
    public $surgicaldatasetting;

    public $name_ar, $name_en, $description_ar, $description_en, $possible_complications_ar, $possible_complications_en, $preparation_instructions_ar, $preparation_instructions_en;

    public $category, $technique, $complexity, $surgical_method, $anesthesia_type, $ventilation_method;
    public $expected_duration_hours = 0, $expected_duration_minutes = 0;
    public $anesthesia_duration_hours = 0, $anesthesia_duration_minutes = 0;
    public $blood_compatibility_test = false;

    public $selected_tests = [], $selected_medications = [], $selected_tools = [];
    public $testSearch = '', $medicationSearch = '', $toolSearch = '';

   public function mount($id)
{
    $this->service_id = Crypt::decryptString($id);
    $this->service = Service::findOrFail($this->service_id);
    $this->surgicaldatasetting = Surgicaldatasetting::where('service_id', $this->service_id)->first();

    // 🔹 جهّز القيم الأساسية عشان Livewire يربطها بالـ input
    $this->name_ar = $this->service->getTranslation('name', 'ar') ?? '';
    $this->name_en = $this->service->getTranslation('name', 'en') ?? '';

    if ($this->surgicaldatasetting) {
        foreach ([
            'description_ar','description_en',
            'possible_complications_ar','possible_complications_en',
            'preparation_instructions_ar','preparation_instructions_en',
            'category','technique','expected_duration','complexity',
            'surgical_method','anesthesia_type','ventilation_method',
            'anesthesia_duration','blood_compatibility_test',
            'pre_op_tests','pre_op_medications',
            'anesthesia_medications','surgical_tools'
        ] as $field) {
            if (in_array($field, ['expected_duration', 'anesthesia_duration'])) {
                $value = $this->surgicaldatasetting->{$field};
                $this->{$field . '_hours'} = explode(':', $value)[0] ?? 0;
                $this->{$field . '_minutes'} = explode(':', $value)[1] ?? 0;
            } elseif ($field === 'surgical_tools') {
                $this->selected_tools = explode(',', $this->surgicaldatasetting->surgical_tools ?? '');
            } elseif ($field === 'pre_op_tests') {
                $this->selected_tests = explode(',', $this->surgicaldatasetting->pre_op_tests ?? '');
            } elseif ($field === 'pre_op_medications') {
                $this->selected_medications = explode(',', $this->surgicaldatasetting->pre_op_medications ?? '');
            } else {
                $this->{$field} = $this->surgicaldatasetting->{$field};
            }
        }
    }
}


    public function save()
    {
        $this->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
         
        ], [
            'name_ar.required' => trans('validation.custom.name_ar.required'),
            'name_en.required' => trans('validation.custom.name_en.required'),
            'category.required' => trans('validation.custom.category.required'),
            'category.in' => trans('validation.custom.category.in'),
            'technique.required' => trans('validation.custom.technique.required'),
            'technique.in' => trans('validation.custom.technique.in'),
            'complexity.required' => trans('validation.custom.complexity.required'),
            'complexity.in' => trans('validation.custom.complexity.in'),
            'surgical_method.required' => trans('validation.custom.surgical_method.required'),
        ]);

        $expected = sprintf('%02d:%02d:00', $this->expected_duration_hours, $this->expected_duration_minutes);
        $anesthesia = sprintf('%02d:%02d:00', $this->anesthesia_duration_hours, $this->anesthesia_duration_minutes);

        Surgicaldatasetting::updateOrCreate(
            ['service_id' => $this->service_id],
            [
                'name_ar' => $this->name_ar,
                'name_en' => $this->name_en,
                'description_ar' => $this->description_ar,
                'description_en' => $this->description_en,
                'possible_complications_ar' => $this->possible_complications_ar,
                'possible_complications_en' => $this->possible_complications_en,
                'preparation_instructions_ar' => $this->preparation_instructions_ar,
                'preparation_instructions_en' => $this->preparation_instructions_en,
                'category' => $this->category?:'',
                'technique' => $this->technique,
                'expected_duration' => $expected,
                'complexity' => $this->complexity,
                'surgical_method' => $this->surgical_method,
                'anesthesia_type' => $this->anesthesia_type,
                'ventilation_method' => $this->ventilation_method,
                'anesthesia_duration' => $anesthesia,
                'blood_compatibility_test' => $this->blood_compatibility_test,
                'pre_op_tests' => implode(',', $this->selected_tests),
                'pre_op_medications' => implode(',', $this->selected_medications),
                'anesthesia_medications' => null,
                'surgical_tools' => implode(',', $this->selected_tools),
            ],
        );

        session()->flash('success', trans('surgical_trans.setting_saved'));
        return redirect()->route('surgicaldatasettings.index');
    }

    public function toggleSelectAll($type)
    {
        if ($type === 'tests') {
            $items = Service::when($this->testSearch, fn($q) => $q->where('name', 'like', '%' . $this->testSearch . '%'))->pluck('id')->toArray();
            $this->selected_tests = array_diff($items, $this->selected_tests) === [] ? [] : array_unique(array_merge($this->selected_tests, $items));
        }

        if ($type === 'medications') {
            $items = Medication::when($this->medicationSearch, fn($q) => $q->where('name', 'like', '%' . $this->medicationSearch . '%'))->pluck('id')->toArray();
            $this->selected_medications = array_diff($items, $this->selected_medications) === [] ? [] : array_unique(array_merge($this->selected_medications, $items));
        }

        if ($type === 'tools') {
            $items = FinAsset::when($this->toolSearch, fn($q) => $q->where('name_ar', 'like', '%' . $this->toolSearch . '%'))->pluck('id')->toArray();
            $this->selected_tools = array_diff($items, $this->selected_tools) === [] ? [] : array_unique(array_merge($this->selected_tools, $items));
        }
    }

    public function toggleMedication($id)
    {
        if (in_array($id, $this->selected_medications)) {
            $this->selected_medications = array_diff($this->selected_medications, [$id]);
        } else {
            $this->selected_medications[] = $id;
        }
    }

    public function toggleTool($id)
    {
        if (in_array($id, $this->selected_tools)) {
            $this->selected_tools = array_diff($this->selected_tools, [$id]);
        } else {
            $this->selected_tools[] = $id;
        }
    }

    public function toggleTest($id)
    {
        if (in_array($id, $this->selected_tests)) {
            $this->selected_tests = array_diff($this->selected_tests, [$id]);
        } else {
            $this->selected_tests[] = $id;
        }
    }

    public function render()
    {
        $filteredTests = Service::when($this->testSearch, fn($q) => $q->where('name', 'like', '%' . $this->testSearch . '%'))->pluck('name', 'id');
        $filteredMedications = Medication::when($this->medicationSearch, fn($q) => $q->where('name', 'like', '%' . $this->medicationSearch . '%'))->pluck('name', 'id');
        $filteredTools = FinAsset::when($this->toolSearch, fn($q) => $q->where('name_ar', 'like', '%' . $this->toolSearch . '%'))->pluck('name_ar', 'id');

        return view('livewire.surgical.setting-form', [
            'filteredTests' => $filteredTests,
            'filteredMedications' => $filteredMedications,
            'filteredTools' => $filteredTools,
            'service' => $this->service,
        ]);
    }
}