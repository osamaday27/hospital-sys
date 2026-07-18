<?php

namespace App\Http\Livewire\Radiology\Settings\Devicestypes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\radiologydevicestypessettings;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['page', 'search', 'activeTab'];

    // 🔍 البحث
    public $search = '';

    // 📌 التابات
    public $activeTab = 'list'; // list | form

    // 🖥️ الخصائص
    public $device_id;
    public $name = ['ar' => '', 'en' => ''];
    public $description = ['ar' => '', 'en' => ''];
    public $type;
    public $category; // diagnostic / therapeutic
    public $status = 'active';

    // ✅ القواعد
    protected $rules = [
        'name.ar'        => 'required|string|max:255',
        'name.en'        => 'required|string|max:255',
        'description.ar' => 'nullable|string|max:500',
        'description.en' => 'nullable|string|max:500',
        'type'           => 'required|in:X-Ray,Digital X-Ray,MRI,CT,Ultrasound,Mammography,Nuclear Medicine',
        'category'       => 'required|in:diagnostic,therapeutic',
        'status'         => 'required|in:active,inactive',
    ];

    // ✅ الرسائل التحذيرية بالعربي
    protected $messages = [
        'name.ar.required' => '⚠️ اسم النوع بالعربية مطلوب.',
        'name.ar.string'   => '⚠️ اسم النوع بالعربية يجب أن يكون نصًا.',
        'name.ar.max'      => '⚠️ اسم النوع بالعربية يجب ألا يزيد عن 255 حرفًا.',

        'name.en.required' => '⚠️ اسم النوع بالإنجليزية مطلوب.',
        'name.en.string'   => '⚠️ اسم النوع بالإنجليزية يجب أن يكون نصًا.',
        'name.en.max'      => '⚠️ اسم النوع بالإنجليزية يجب ألا يزيد عن 255 حرفًا.',

        'description.ar.string' => '⚠️ الوصف بالعربية يجب أن يكون نصًا.',
        'description.ar.max'    => '⚠️ الوصف بالعربية يجب ألا يزيد عن 500 حرفًا.',

        'description.en.string' => '⚠️ الوصف بالإنجليزية يجب أن يكون نصًا.',
        'description.en.max'    => '⚠️ الوصف بالإنجليزية يجب ألا يزيد عن 500 حرفًا.',

        'type.required' => '⚠️ نوع الجهاز مطلوب.',
        'type.in'       => '⚠️ نوع الجهاز غير صحيح.',

        'category.required' => '⚠️ فئة الجهاز (تشخيصي / علاجي) مطلوبة.',
        'category.in'       => '⚠️ فئة الجهاز يجب أن تكون تشخيصية أو علاجية.',

        'status.required' => '⚠️ حالة الجهاز مطلوبة.',
        'status.in'       => '⚠️ حالة الجهاز يجب أن تكون إما نشط أو غير نشط.',
    ];

    // ✅ حفظ / تعديل
    public function save()
    {
        $this->validate();

        if ($this->device_id) {
            // تعديل
            $device = radiologydevicestypessettings::findOrFail($this->device_id);
            $device->setTranslations('name', $this->name);
            $device->setTranslations('description', $this->description);
            $device->type     = $this->type;
            $device->category = $this->category;
            $device->status   = $this->status;
            $device->save();

            session()->flash('success', __('radiology_devicestypes.updated'));
        } else {
            // جديد
            $device = new radiologydevicestypessettings();
            $device->setTranslations('name', $this->name);
            $device->setTranslations('description', $this->description);
            $device->type     = $this->type;
            $device->category = $this->category;
            $device->status   = $this->status;
            $device->save();

            session()->flash('success', __('radiology_devicestypes.created'));
        }

        $this->resetInput();
        $this->activeTab = 'list';
    }

    // ✅ تعديل
    public function edit($id)
    {
        $device = radiologydevicestypessettings::findOrFail($id);

        $this->device_id   = $device->id;
        $this->name        = $device->getTranslations('name');
        $this->description = $device->getTranslations('description');
        $this->type        = $device->type;
        $this->category    = $device->category;
        $this->status      = $device->status;

        $this->activeTab = 'form';
    }

    // ✅ إنشاء جديد
    public function createNew()
    {
        $this->resetInput();
        $this->activeTab = 'form';
    }

    // ✅ حذف
    public function delete($id)
    {
        $device = radiologydevicestypessettings::find($id);

        if ($device) {
            $device->delete();
            session()->flash('success', __('radiology_devicestypes.deleted'));
        } else {
            session()->flash('error', __('radiology_devicestypes.not_found'));
        }
    }

    // ✅ إعادة التهيئة
    public function resetInput()
    {
        $this->device_id   = null;
        $this->name        = ['ar' => '', 'en' => ''];
        $this->description = ['ar' => '', 'en' => ''];
        $this->type        = null;
        $this->category    = null;
        $this->status      = 'active';
    }

    // ✅ البحث يرجع للصفحة الأولى
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ✅ Render
    public function render()
    {
        $devices = radiologydevicestypessettings::query()
            ->when($this->search, fn($q) =>
                $q->where('name->ar', 'like', "%{$this->search}%")
                  ->orWhere('name->en', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
            )
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.radiology.settings.devicestypes.index', [
            'devices' => $devices,
        ]);
    }
}
