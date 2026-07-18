<?php

namespace App\Http\Livewire\Medical;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Medical\MsMedicalSheetTemplate;
use App\Models\general\specialties;
use App\Models\Hr\Employee;
use Illuminate\Support\Facades\Auth;

class MedicalSheetTemplateManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $template_id, $template_name, $category, $structure, $version = '1.0', $description;
    public $model_name, $speciality_id, $doctor_id, $display_order = 0;
    public $visibility = 'global', $status = 'active', $created_by;
    public $search = '';

    protected $rules = [
        'template_name' => 'required|string|max:255',
        'category' => 'nullable|string|max:255',
        'version' => 'nullable|string|max:10',
        'model_name' => 'nullable|string|max:255',
        'visibility' => 'required|in:global,private',
        'status' => 'required|in:active,inactive',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'template_name' => $this->template_name,
            'category' => $this->category,
            'structure' => $this->structure ?? [],
            'version' => $this->version,
            'description' => $this->description,
            'model_name' => $this->model_name,
            'speciality_id' => $this->speciality_id,
            'doctor_id' => $this->doctor_id,
            'display_order' => $this->display_order,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'created_by' => Auth::user()->name ?? 'System',
        ];

        if ($this->template_id) {
            MsMedicalSheetTemplate::findOrFail($this->template_id)->update($data);
            session()->flash('success', '✅ تم تحديث القالب بنجاح');
        } else {
            MsMedicalSheetTemplate::create($data);
            session()->flash('success', '✅ تم إنشاء القالب بنجاح');
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $template = MsMedicalSheetTemplate::findOrFail($id);
        $this->template_id = $template->id;
        $this->template_name = $template->template_name;
        $this->category = $template->category;
        $this->version = $template->version;
        $this->description = $template->description;
        $this->model_name = $template->model_name;
        $this->speciality_id = $template->speciality_id;
        $this->doctor_id = $template->doctor_id;
        $this->display_order = $template->display_order;
        $this->visibility = $template->visibility;
        $this->status = $template->status;
    }

    public function delete($id)
    {
        MsMedicalSheetTemplate::findOrFail($id)->delete();
        session()->flash('success', '🗑️ تم حذف القالب بنجاح');
    }

    public function resetInput()
    {
        $this->reset([
            'template_id', 'template_name', 'category', 'structure', 'version', 'description',
            'model_name', 'speciality_id', 'doctor_id', 'display_order', 'visibility', 'status'
        ]);
    }

    public function render()
    {
        $templates = MsMedicalSheetTemplate::where('template_name', 'like', "%{$this->search}%")
            ->orderBy('display_order')
            ->paginate(10);

        return view('livewire.medical.medical-sheet-template-manager', [
            'templates' => $templates,
            'specialities' => specialties::all(),
            'doctors' => Employee::all(),
        ]);
    }
}