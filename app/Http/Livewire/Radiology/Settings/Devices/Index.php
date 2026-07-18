<?php

namespace App\Http\Livewire\Radiology\Settings\Devices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\radiologydevicessettings;
use App\Models\administrative\radiologydevicestypessettings;
use App\Models\administrative\radiologyroomssettings;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['page', 'search', 'activeTab'];

    // البحث
    public $search = '';

    // Tabs
    public $activeTab = 'list'; // list | form

    // خصائص الجهاز
    public $device_id;
    public $name = ['ar' => '', 'en' => ''];
    public $description = ['ar' => '', 'en' => ''];
    public $device_type_id, $room_id;
    public $status = 'available';
    public $installation_date, $maintenance_date, $vendor, $serial_no;

    // بيانات مساعدة
    public $types = [];
    public $rooms = [];

    // ✅ القواعد
    protected $rules = [
        'name.ar'           => 'required|string|max:255',
        'name.en'           => 'required|string|max:255',
        'description.ar'    => 'nullable|string|max:500',
        'description.en'    => 'nullable|string|max:500',
        'device_type_id'    => 'required|exists:rad_s_devicestypes,id',
        'room_id'           => 'required|exists:rad_s_rooms,id',
        'status'            => 'required|in:available,maintenance,inactive',
        'installation_date' => 'nullable|date',
        'maintenance_date'  => 'nullable|date',
        'vendor'            => 'nullable|string|max:255',
        'serial_no'         => 'nullable|string|max:255',
    ];

    // ✅ الرسائل التحذيرية بالعربي
    protected $messages = [
        'name.ar.required' => '⚠️ اسم الجهاز بالعربية مطلوب.',
        'name.ar.string'   => '⚠️ اسم الجهاز بالعربية يجب أن يكون نصًا.',
        'name.ar.max'      => '⚠️ اسم الجهاز بالعربية يجب ألا يزيد عن 255 حرفًا.',

        'name.en.required' => '⚠️ اسم الجهاز بالإنجليزية مطلوب.',
        'name.en.string'   => '⚠️ اسم الجهاز بالإنجليزية يجب أن يكون نصًا.',
        'name.en.max'      => '⚠️ اسم الجهاز بالإنجليزية يجب ألا يزيد عن 255 حرفًا.',

        'description.ar.string' => '⚠️ الوصف بالعربية يجب أن يكون نصًا.',
        'description.ar.max'    => '⚠️ الوصف بالعربية يجب ألا يزيد عن 500 حرفًا.',

        'description.en.string' => '⚠️ الوصف بالإنجليزية يجب أن يكون نصًا.',
        'description.en.max'    => '⚠️ الوصف بالإنجليزية يجب ألا يزيد عن 500 حرفًا.',

        'device_type_id.required' => '⚠️ اختيار نوع الجهاز مطلوب.',
        'device_type_id.exists'   => '⚠️ نوع الجهاز المحدد غير موجود.',

        'room_id.required' => '⚠️ اختيار الغرفة مطلوب.',
        'room_id.exists'   => '⚠️ الغرفة المحددة غير موجودة.',

        'status.required' => '⚠️ حالة الجهاز مطلوبة.',
        'status.in'       => '⚠️ حالة الجهاز يجب أن تكون متاحة أو تحت الصيانة أو غير مفعلة.',

        'installation_date.date' => '⚠️ تاريخ التركيب يجب أن يكون تاريخًا صحيحًا.',
        'maintenance_date.date'  => '⚠️ تاريخ الصيانة يجب أن يكون تاريخًا صحيحًا.',

        'vendor.string' => '⚠️ اسم المورد يجب أن يكون نصًا.',
        'vendor.max'    => '⚠️ اسم المورد يجب ألا يزيد عن 255 حرفًا.',

        'serial_no.string' => '⚠️ الرقم التسلسلي يجب أن يكون نصًا.',
        'serial_no.max'    => '⚠️ الرقم التسلسلي يجب ألا يزيد عن 255 حرفًا.',
    ];

    public function mount()
    {
        $this->types = radiologydevicestypessettings::all();
        $this->rooms = radiologyroomssettings::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // حفظ / تعديل
    public function save()
    {
        $this->validate();

        if ($this->device_id) {
            $device = radiologydevicessettings::findOrFail($this->device_id);
            $device->setTranslations('name', $this->name);
            $device->setTranslations('description', $this->description);
            $device->device_type_id    = $this->device_type_id;
            $device->room_id           = $this->room_id;
            $device->status            = $this->status;
            $device->installation_date = $this->installation_date;
            $device->maintenance_date  = $this->maintenance_date;
            $device->vendor            = $this->vendor;
            $device->serial_no         = $this->serial_no;
            $device->save();

            session()->flash('success', __('devices.updated'));
        } else {
            $device = new radiologydevicessettings();
            $device->setTranslations('name', $this->name);
            $device->setTranslations('description', $this->description);
            $device->device_type_id    = $this->device_type_id;
            $device->room_id           = $this->room_id;
            $device->status            = $this->status;
            $device->installation_date = $this->installation_date;
            $device->maintenance_date  = $this->maintenance_date;
            $device->vendor            = $this->vendor;
            $device->serial_no         = $this->serial_no;
            $device->save();

            session()->flash('success', __('devices.created'));
        }

        $this->resetInput();
        $this->activeTab = 'list';
    }

    // تعديل
    public function edit($id)
    {
        $device = radiologydevicessettings::findOrFail($id);

        $this->device_id   = $device->id;
        $this->name        = $device->getTranslations('name');
        $this->description = $device->getTranslations('description');
        $this->device_type_id    = $device->device_type_id;
        $this->room_id           = $device->room_id;
        $this->status            = $device->status;
        $this->installation_date = $device->installation_date;
        $this->maintenance_date  = $device->maintenance_date;
        $this->vendor            = $device->vendor;
        $this->serial_no         = $device->serial_no;

        $this->activeTab = 'form';
    }

    // جديد
    public function createNew()
    {
        $this->resetInput();
        $this->activeTab = 'form';
    }

    // حذف
    public function delete($id)
    {
        $device = radiologydevicessettings::find($id);

        if ($device) {
            $device->delete();
            session()->flash('success', __('devices.deleted'));
        } else {
            session()->flash('error', __('devices.not_found'));
        }
    }

    // reset
    public function resetInput()
    {
        $this->device_id = null;
        $this->name      = ['ar' => '', 'en' => ''];
        $this->description = ['ar' => '', 'en' => ''];
        $this->device_type_id = null;
        $this->room_id       = null;
        $this->status        = 'available';
        $this->installation_date = null;
        $this->maintenance_date  = null;
        $this->vendor        = null;
        $this->serial_no     = null;
    }

    public function render()
    {
        $devices = radiologydevicessettings::with(['type','room'])
            ->when($this->search, fn($q) =>
                $q->where('name->ar','like',"%{$this->search}%")
                  ->orWhere('name->en','like',"%{$this->search}%")
                  ->orWhere('vendor','like',"%{$this->search}%")
                  ->orWhere('serial_no','like',"%{$this->search}%")
            )
            ->orderBy('id','desc')
            ->paginate(10);

        return view('livewire.radiology.settings.devices.index',[
            'devices'=>$devices,
            'types'=>$this->types,
            'rooms'=>$this->rooms,
        ]);
    }
}
