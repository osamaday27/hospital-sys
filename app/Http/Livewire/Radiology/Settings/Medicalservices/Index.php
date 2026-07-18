<?php

namespace App\Http\Livewire\Radiology\Settings\Medicalservices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\radiologymedicalservicessettings;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete'];

    public $search = '';
    public $activeTab = 'list';

    // 🏥 خصائص الخدمة
    public $service_id, $device_id;
    public $name = ['ar' => '', 'en' => ''];
    public $description = ['ar' => '', 'en' => ''];
    public $instructions = ['ar' => '', 'en' => ''];
    public $icd10_code, $cpt_code, $status = 'active';

    public $devices = [];

    protected function rules()
    {
        return [
            'name.ar'        => 'required|string|max:255',
            'name.en'        => 'required|string|max:255',
            'description.ar' => 'nullable|string|max:500',
            'description.en' => 'nullable|string|max:500',
            'instructions.ar'=> 'nullable|string|max:500',
            'instructions.en'=> 'nullable|string|max:500',
            'device_id'      => 'required|exists:rad_s_devices,id',
            'icd10_code'     => 'nullable|string|max:50',
            'cpt_code'       => 'nullable|string|max:50',
            'status'         => 'required|in:active,inactive',
        ];
    }

    // ✅ الرسائل التحذيرية بالعربي
    protected $messages = [
        'name.ar.required' => '⚠️ اسم الخدمة بالعربية مطلوب.',
        'name.ar.string'   => '⚠️ اسم الخدمة بالعربية يجب أن يكون نصًا.',
        'name.ar.max'      => '⚠️ اسم الخدمة بالعربية يجب ألا يزيد عن 255 حرفًا.',

        'name.en.required' => '⚠️ اسم الخدمة بالإنجليزية مطلوب.',
        'name.en.string'   => '⚠️ اسم الخدمة بالإنجليزية يجب أن يكون نصًا.',
        'name.en.max'      => '⚠️ اسم الخدمة بالإنجليزية يجب ألا يزيد عن 255 حرفًا.',

        'description.ar.string' => '⚠️ الوصف بالعربية يجب أن يكون نصًا.',
        'description.ar.max'    => '⚠️ الوصف بالعربية يجب ألا يزيد عن 500 حرفًا.',

        'description.en.string' => '⚠️ الوصف بالإنجليزية يجب أن يكون نصًا.',
        'description.en.max'    => '⚠️ الوصف بالإنجليزية يجب ألا يزيد عن 500 حرفًا.',

        'instructions.ar.string' => '⚠️ التعليمات بالعربية يجب أن تكون نصًا.',
        'instructions.ar.max'    => '⚠️ التعليمات بالعربية يجب ألا تزيد عن 500 حرفًا.',

        'instructions.en.string' => '⚠️ التعليمات بالإنجليزية يجب أن تكون نصًا.',
        'instructions.en.max'    => '⚠️ التعليمات بالإنجليزية يجب ألا تزيد عن 500 حرفًا.',

        'device_id.required' => '⚠️ اختيار الجهاز مطلوب.',
        'device_id.exists'   => '⚠️ الجهاز المحدد غير موجود.',

        'icd10_code.string' => '⚠️ كود ICD10 يجب أن يكون نصًا.',
        'icd10_code.max'    => '⚠️ كود ICD10 يجب ألا يزيد عن 50 حرفًا.',

        'cpt_code.string' => '⚠️ كود CPT يجب أن يكون نصًا.',
        'cpt_code.max'    => '⚠️ كود CPT يجب ألا يزيد عن 50 حرفًا.',

        'status.required' => '⚠️ حالة الخدمة مطلوبة.',
        'status.in'       => '⚠️ حالة الخدمة يجب أن تكون إما نشطة أو غير نشطة.',
    ];

    public function mount()
    {
        $this->devices = radiologymedicalservicessettings::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        radiologymedicalservicessettings::updateOrCreate(
            ['id' => $this->service_id],
            [
                'name'        => $this->name,
                'description' => $this->description,
                'instructions'=> $this->instructions,
                'device_id'   => $this->device_id,
                'icd10_code'  => $this->icd10_code,
                'cpt_code'    => $this->cpt_code,
                'status'      => $this->status,
            ]
        );

        session()->flash('success', $this->service_id ? '✅ تم التحديث بنجاح' : '✅ تم الحفظ بنجاح');
        $this->resetInput();
        $this->activeTab = 'list';
    }

    public function edit($id)
    {
        $service = radiologymedicalservicessettings::findOrFail($id);

        $this->service_id  = $service->id;
        $this->name        = $service->getTranslations('name');
        $this->description = $service->getTranslations('description');
        $this->instructions= $service->getTranslations('instructions');
        $this->device_id   = $service->device_id;
        $this->icd10_code  = $service->icd10_code;
        $this->cpt_code    = $service->cpt_code;
        $this->status      = $service->status;

        $this->activeTab = 'form';
    }

    public function delete($id)
    {
        $service = radiologymedicalservicessettings::find($id);

        if ($service) {
            $service->delete();
            session()->flash('success', '🗑️ تم الحذف بنجاح');
        } else {
            session()->flash('error', '❌ الخدمة غير موجودة');
        }
    }

    public function createNew()
    {
        $this->resetInput();
        $this->activeTab = 'form';
    }

    public function resetInput()
    {
        $this->service_id = null;
        $this->name = ['ar' => '', 'en' => ''];
        $this->description = ['ar' => '', 'en' => ''];
        $this->instructions = ['ar' => '', 'en' => ''];
        $this->device_id = null;
        $this->icd10_code = null;
        $this->cpt_code = null;
        $this->status = 'active';
    }

    public function render()
    {
        $services = radiologymedicalservicessettings::with('device')
            ->when($this->search, function ($q) {
                $q->where('name->ar', 'like', "%{$this->search}%")
                  ->orWhere('name->en', 'like', "%{$this->search}%")
                  ->orWhere('icd10_code', 'like', "%{$this->search}%")
                  ->orWhere('cpt_code', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.radiology.settings.medicalservices.index', compact('services'));
    }
}
