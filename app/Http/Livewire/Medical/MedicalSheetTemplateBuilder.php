<?php

namespace App\Http\Livewire\Medical;

use Livewire\Component;
use App\Models\Medical\MsMedicalSheetTemplate;
use Illuminate\Support\Str;

class MedicalSheetTemplateBuilder extends Component
{
    public $template_id;
    public $template;
    public $sections = [];

    // Basic Info
    public $template_name = '';
    public $template_category = '';
    public $template_description = '';
    public $template_tags = [];

    // Section Management
    public $newSectionName = '';
    public $newSectionDescription = '';
    public $sectionIcon = 'ri-folder-2-line';
    public $newSectionColor = 'primary';
    
    // Field Management
    public $newField = [
        'label' => '',
        'type' => 'text',
        'options' => '',
        'required' => false,
        'default_value' => '',
        'source' => '',
        'readonly' => false,
        'repeatable' => false,
        'placeholder' => '',
        'help_text' => '',
        'min' => null,
        'max' => null,
        'step' => null,
    ];

    // Mixed Text Field
    public $mixedTextField = [
        'content' => '',
    ];

    // Settings
    public $is_printable = false;
    public $is_public = false;
    public $auto_save = true;
    
    // UI State
    public $newStaticText = '';
    public $narrative_text = '';
    public $preview_mode = false;
    public $fullscreen_mode = false;
    public $selectedPreset = '';
    public $active_tab = 'builder';
    public $version_history = [];

    // Field Icons
    public $fieldIcons = [
        'text' => 'ri-text',
        'number' => 'ri-hashtag',
        'date' => 'ri-calendar-line',
        'time' => 'ri-time-line',
        'datetime' => 'ri-calendar-event-line',
        'dropdown' => 'ri-arrow-down-s-line',
        'radio' => 'ri-radio-button-line',
        'checkbox' => 'ri-checkbox-line',
        'textarea' => 'ri-file-text-line',
        'file' => 'ri-file-upload-line',
        'signature' => 'ri-pen-nib-line',
        'table' => 'ri-table-line',
        'divider' => 'ri-separator',
        'heading' => 'ri-heading',
        'static' => 'ri-text',
        'mixed_text' => 'ri-file-list-3-line',
    ];

    // Enhanced Presets with Bootstrap Colors
    public $availableSectionPresets = [
        'Patient Information' => [
            'icon' => 'ri-user-heart-line',
            'color' => 'primary',
            'fields' => [
                ['label' => 'Patient Name', 'type' => 'text', 'required' => true, 'source' => 'patient.name', 'icon' => 'ri-user-3-line'],
                ['label' => 'Patient ID', 'type' => 'text', 'required' => true, 'source' => 'patient.id', 'readonly' => true],
                ['label' => 'Date of Birth', 'type' => 'date', 'required' => true, 'source' => 'patient.dob'],
                ['label' => 'Age', 'type' => 'number', 'source' => 'patient.age', 'readonly' => true],
                ['label' => 'Gender', 'type' => 'radio', 'options' => ['Male', 'Female'], 'required' => true],
                ['label' => 'Eye', 'type' => 'dropdown', 'options' => ['OD', 'OS', 'OU'], 'required' => true, 'help_text' => 'Right/Left/Both'],
            ]
        ],
        'Preoperative Diagnosis' => [
            'icon' => 'ri-stethoscope-line',
            'color' => 'danger',
            'fields' => [
                ['label' => 'Primary Diagnosis', 'type' => 'dropdown', 'options' => ['Cataract', 'Glaucoma', 'Retinal Detachment', 'Keratoconus'], 'required' => true],
                ['label' => 'Secondary Diagnosis', 'type' => 'dropdown', 'options' => ['None', 'Diabetes', 'Hypertension', 'Other']],
                ['label' => 'ICD-10 Code', 'type' => 'text', 'placeholder' => 'H25.9'],
                ['label' => 'Severity', 'type' => 'radio', 'options' => ['Mild', 'Moderate', 'Severe', 'Critical']],
            ]
        ],
        'Anesthesia Details' => [
            'icon' => 'ri-syringe-line',
            'color' => 'warning',
            'fields' => [
                ['label' => 'Anesthesia Type', 'type' => 'dropdown', 'options' => ['General', 'Local', 'Peribulbar', 'Topical', 'Monitored Care'], 'required' => true],
                ['label' => 'Anesthetist Name', 'type' => 'text', 'source' => 'anesthetist.name'],
                ['label' => 'Start Time', 'type' => 'time', 'required' => true],
                ['label' => 'Medications Used', 'type' => 'textarea', 'placeholder' => 'List medications...'],
            ]
        ],
        'Surgical Procedure' => [
            'icon' => 'ri-surgical-mask-line',
            'color' => 'success',
            'fields' => [
                ['label' => 'Procedure Name', 'type' => 'text', 'required' => true],
                ['label' => 'CPT Code', 'type' => 'text', 'placeholder' => '66984'],
                ['label' => 'Incision Size (mm)', 'type' => 'dropdown', 'options' => ['2.2', '2.4', '2.5', '2.8', '3.0', '3.2']],
                ['label' => 'Technique Used', 'type' => 'dropdown', 'options' => ['Phacoemulsification', 'ECCE', 'Manual SICS']],
                ['label' => 'IOL Type', 'type' => 'dropdown', 'options' => ['Monofocal', 'Multifocal', 'Toric', 'EDOF']],
                ['label' => 'IOL Power', 'type' => 'number', 'min' => 0, 'max' => 40, 'step' => 0.5],
            ]
        ],
        'Measurements & Findings' => [
            'icon' => 'ri-ruler-line',
            'color' => 'info',
            'fields' => [
                ['label' => 'IOP (mmHg)', 'type' => 'number', 'min' => 0, 'max' => 100, 'placeholder' => '15'],
                ['label' => 'Axial Length (mm)', 'type' => 'number', 'step' => 0.01],
                ['label' => 'Keratometry K1', 'type' => 'number', 'step' => 0.01],
                ['label' => 'Keratometry K2', 'type' => 'number', 'step' => 0.01],
                ['label' => 'Anterior Chamber Depth', 'type' => 'number', 'step' => 0.01],
            ]
        ],
        'Complications' => [
            'icon' => 'ri-alert-line',
            'color' => 'danger',
            'fields' => [
                ['label' => 'Complications Occurred', 'type' => 'radio', 'options' => ['None', 'Yes'], 'required' => true],
                ['label' => 'Type of Complication', 'type' => 'checkbox', 'options' => ['Bleeding', 'Capsular Rupture', 'Corneal Edema', 'Vitreous Loss', 'Other']],
                ['label' => 'Description', 'type' => 'textarea', 'placeholder' => 'Describe complication...'],
                ['label' => 'Management', 'type' => 'textarea', 'placeholder' => 'How was it managed...'],
            ]
        ],
        'Post-Operative Care' => [
            'icon' => 'ri-heart-pulse-line',
            'color' => 'secondary',
            'fields' => [
                ['label' => 'Eye Patch Applied', 'type' => 'radio', 'options' => ['Yes', 'No']],
                ['label' => 'Medications Prescribed', 'type' => 'textarea'],
                ['label' => 'Follow-up Date', 'type' => 'date', 'required' => true],
                ['label' => 'Special Instructions', 'type' => 'textarea'],
            ]
        ],
        'Surgeon Details' => [
            'icon' => 'ri-user-star-line',
            'color' => 'primary',
            'fields' => [
                ['label' => 'Primary Surgeon', 'type' => 'text', 'required' => true, 'source' => 'surgeon.name'],
                ['label' => 'Assistant Surgeon', 'type' => 'text'],
                ['label' => 'Surgical Residents', 'type' => 'text'],
                ['label' => 'Signature', 'type' => 'signature'],
            ]
        ],
    ];

    // Field Type Options
    public $fieldTypes = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'number' => 'Number',
        'date' => 'Date',
        'time' => 'Time',
        'datetime' => 'Date & Time',
        'dropdown' => 'Dropdown List',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkboxes',
        'file' => 'File Upload',
        'signature' => 'Signature',
        'table' => 'Data Table',
        'divider' => 'Section Divider',
        'heading' => 'Heading',
        'static' => 'Static Text',
        'mixed_text' => 'Mixed Text (Text + Options)',
    ];

    protected $listeners = [
        'updateNarrative' => 'updateNarrative',
        'reorderSections' => 'reorderSections',
        'duplicateSection' => 'duplicateSection',
    ];

    public function mount($id)
    {
        $this->template_id = $id;
        $this->template = MsMedicalSheetTemplate::findOrFail($id);

        $structure = $this->template->structure ?? [];

        $this->template_name = $this->template->template_name;
        $this->template_category = $this->template->category ?? '';
        $this->template_description = $this->template->description ?? '';
        $this->sections = $structure['sections'] ?? [];
        $this->is_printable = $structure['is_printable'] ?? false;
        $this->is_public = $structure['is_public'] ?? false;
        $this->narrative_text = $structure['narrative'] ?? '';
        $this->version_history = $structure['versions'] ?? [];
    }

    public function toggleFullscreen()
    {
        $this->fullscreen_mode = !$this->fullscreen_mode;
    }

    public function togglePreview()
    {
        $this->preview_mode = !$this->preview_mode;
    }

    public function switchTab($tab)
    {
        $this->active_tab = $tab;
    }

    public function addPresetSection()
    {
        if (!$this->selectedPreset || !isset($this->availableSectionPresets[$this->selectedPreset])) return;

        $preset = $this->availableSectionPresets[$this->selectedPreset];

        $this->sections[] = [
            'section_id' => 'section_' . uniqid(),
            'section_name' => $this->selectedPreset,
            'section_description' => '',
            'icon' => $preset['icon'] ?? 'ri-folder-2-line',
            'color' => $preset['color'] ?? 'primary',
            'collapsible' => true,
            'collapsed' => false,
            'fields' => $preset['fields'],
        ];

        $this->selectedPreset = '';
        session()->flash('success', '✅ تم إضافة القسم بنجاح');
    }

    public function addSection()
    {
        if ($this->newSectionName) {
            $this->sections[] = [
                'section_id' => 'section_' . uniqid(),
                'section_name' => $this->newSectionName,
                'section_description' => $this->newSectionDescription,
                'icon' => $this->sectionIcon,
                'color' => $this->newSectionColor,
                'collapsible' => true,
                'collapsed' => false,
                'fields' => [],
            ];
            $this->reset(['newSectionName', 'newSectionDescription', 'sectionIcon', 'newSectionColor']);
            session()->flash('success', '✅ تم إنشاء قسم جديد');
        }
    }

    public function addStaticText($sectionIndex)
    {
        if (!$this->newStaticText) return;
        
        $this->sections[$sectionIndex]['fields'][] = [
            'field_id' => 'static_' . uniqid(),
            'label' => $this->newStaticText,
            'type' => 'static',
            'icon' => 'ri-text',
        ];
        $this->newStaticText = '';
    }

    // Mixed Text Field Implementation
    public function addMixedTextField($sectionIndex)
    {
        if (empty($this->mixedTextField['content'])) return;
        
        $this->sections[$sectionIndex]['fields'][] = [
            'field_id' => 'mixed_' . uniqid(),
            'type' => 'mixed_text',
            'content' => $this->mixedTextField['content'],
            'inline_fields' => $this->parseMixedText($this->mixedTextField['content']),
            'icon' => 'ri-file-list-3-line',
        ];
        
        $this->reset('mixedTextField');
        session()->flash('success', '✅ تم إضافة النص المختلط بنجاح');
    }

    private function parseMixedText($content)
    {
        $inlineFields = [];
        $fieldCounter = 0;
        
        // Parse dropdown patterns: {{dropdown:option1,option2,option3}}
        preg_match_all('/\{\{dropdown:(.*?)\}\}/', $content, $dropdowns);
        foreach ($dropdowns[0] as $index => $fullMatch) {
            $fieldId = 'dropdown_' . $fieldCounter++;
            $options = array_map('trim', explode(',', $dropdowns[1][$index]));
            $inlineFields[$fieldId] = [
                'type' => 'dropdown',
                'options' => $options,
                'placeholder' => $fullMatch,
            ];
        }
        
        // Parse radio patterns: {{radio:Right,Left}}
        preg_match_all('/\{\{radio:(.*?)\}\}/', $content, $radios);
        foreach ($radios[0] as $index => $fullMatch) {
            $fieldId = 'radio_' . $fieldCounter++;
            $options = array_map('trim', explode(',', $radios[1][$index]));
            $inlineFields[$fieldId] = [
                'type' => 'radio',
                'options' => $options,
                'placeholder' => $fullMatch,
            ];
        }
        
        // Parse input patterns: {{input:placeholder text}}
        preg_match_all('/\{\{input:(.*?)\}\}/', $content, $inputs);
        foreach ($inputs[0] as $index => $fullMatch) {
            $fieldId = 'input_' . $fieldCounter++;
            $inlineFields[$fieldId] = [
                'type' => 'input',
                'placeholder' => trim($inputs[1][$index]),
                'original' => $fullMatch,
            ];
        }
        
        return $inlineFields;
    }

    public function addField($sectionIndex)
    {
        if (!$this->newField['label']) return;

        $field = [
            'field_id' => Str::slug($this->newField['label']) . '_' . uniqid(),
            'label' => $this->newField['label'],
            'type' => $this->newField['type'],
            'required' => $this->newField['required'],
            'default_value' => $this->newField['default_value'],
            'readonly' => $this->newField['readonly'],
            'repeatable' => $this->newField['repeatable'],
            'source' => $this->newField['source'],
            'placeholder' => $this->newField['placeholder'],
            'help_text' => $this->newField['help_text'],
            'icon' => $this->fieldIcons[$this->newField['type']] ?? 'ri-input-field',
        ];

        if (in_array($this->newField['type'], ['dropdown', 'radio', 'checkbox'])) {
            $field['options'] = array_filter(array_map('trim', explode(',', $this->newField['options'])));
        }

        if ($this->newField['type'] === 'number') {
            $field['min'] = $this->newField['min'];
            $field['max'] = $this->newField['max'];
            $field['step'] = $this->newField['step'];
        }

        $this->sections[$sectionIndex]['fields'][] = $field;
        $this->reset('newField');
        session()->flash('success', '✅ تم إضافة الحقل بنجاح');
    }

    public function duplicateField($sIndex, $fIndex)
    {
        $field = $this->sections[$sIndex]['fields'][$fIndex];
        $field['field_id'] = $field['field_id'] . '_copy_' . uniqid();
        $field['label'] = $field['label'] . ' (Copy)';
        $this->sections[$sIndex]['fields'][] = $field;
    }

    public function removeField($sIndex, $fIndex)
    {
        unset($this->sections[$sIndex]['fields'][$fIndex]);
        $this->sections[$sIndex]['fields'] = array_values($this->sections[$sIndex]['fields']);
    }

    public function duplicateSection($index)
    {
        $section = $this->sections[$index];
        $section['section_id'] = 'section_' . uniqid();
        $section['section_name'] .= ' (Copy)';
        $this->sections[] = $section;
    }

    public function removeSection($index)
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    public function toggleSectionCollapse($index)
    {
        $this->sections[$index]['collapsed'] = !($this->sections[$index]['collapsed'] ?? false);
    }

    public function reorderSections($orderedIds)
    {
        // Implement drag-drop reordering logic
    }

    public function updateNarrative($data)
    {
        $this->narrative_text = $data;
    }

    public function exportTemplate()
    {
        $json = json_encode([
            'template_name' => $this->template_name,
            'category' => $this->template_category,
            'sections' => $this->sections,
            'narrative' => $this->narrative_text,
        ], JSON_PRETTY_PRINT);

        return response()->streamDownload(function() use ($json) {
            echo $json;
        }, Str::slug($this->template_name) . '.json');
    }

    public function save()
    {
        $this->validate([
            'template_name' => 'required|string|max:255',
        ]);

        // Save version history
        $newVersion = [
            'version' => count($this->version_history) + 1,
            'saved_at' => now(),
            'saved_by' => auth()->user()->name ?? 'System',
            'changes' => 'Manual save',
        ];

        $this->version_history[] = $newVersion;

        $this->template->update([
            'template_name' => $this->template_name,
            'category' => $this->template_category,
            'description' => $this->template_description,
            'structure' => [
                'is_printable' => $this->is_printable,
                'is_public' => $this->is_public,
                'sections' => $this->sections,
                'narrative' => $this->narrative_text,
                'versions' => $this->version_history,
            ]
        ]);

        session()->flash('success', '✅ تم حفظ التصميم بنجاح');
    }

    public function render()
    {
        return view('livewire.medical.medical-sheet-template-builder');
    }
}