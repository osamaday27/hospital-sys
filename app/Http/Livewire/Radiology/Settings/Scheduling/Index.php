<?php

namespace App\Http\Livewire\Radiology\Settings\Scheduling;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\radiologyschedulingsettings;
use App\Models\administrative\radiologyroomssettings;
use App\Models\administrative\radiologydevicessettings;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
protected $listeners = ['deleteConfirmed' => 'delete'];

    public $activeTab = 'list';
    public $search = '';

    // Form fields
    public $schedule_id;
    public $room_id;
    public $device_id;
    public $shift_type;
    public $start_time;
    public $end_time;
    public $is_holiday = false;
    public $is_maintenance = false;
    public $status = 'active';

    // Dropdowns
    public $rooms = [];
    public $devices = [];

    protected $rules = [
        'room_id'    => 'required|exists:rad_s_rooms,id',
        'device_id'  => 'required|exists:rad_s_devices,id',
        'shift_type' => 'required|in:morning,evening,night',
        'start_time' => 'required|date_format:H:i',
        'end_time'   => 'required|date_format:H:i|after:start_time',
        'status'     => 'required|in:active,inactive',
    ];

    protected $messages = [
        'room_id.required'  => '⚠️ اختيار الغرفة مطلوب.',
        'room_id.exists'    => '⚠️ الغرفة المحددة غير موجودة.',

        'device_id.required'=> '⚠️ اختيار الجهاز مطلوب.',
        'device_id.exists'  => '⚠️ الجهاز المحدد غير موجود.',

        'shift_type.required' => '⚠️ نوع الفترة مطلوب.',
        'shift_type.in'       => '⚠️ نوع الفترة يجب أن تكون (صباحية، مسائية، ليلية).',

        'start_time.required'    => '⚠️ وقت البداية مطلوب.',
        'start_time.date_format' => '⚠️ وقت البداية يجب أن يكون بالصيغة HH:MM.',

        'end_time.required'    => '⚠️ وقت النهاية مطلوب.',
        'end_time.date_format' => '⚠️ وقت النهاية يجب أن يكون بالصيغة HH:MM.',
        'end_time.after'       => '⚠️ وقت النهاية يجب أن يكون بعد وقت البداية.',

        'status.required' => '⚠️ حالة الجدولة مطلوبة.',
        'status.in'       => '⚠️ الحالة يجب أن تكون نشطة أو غير نشطة.',
    ];

    public function mount()
    {
        $this->rooms = radiologyroomssettings::all();
        $this->devices = radiologydevicessettings::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->schedule_id) {
            $schedule = radiologyschedulingsettings::findOrFail($this->schedule_id);
            $schedule->update($validated + [
                'is_holiday'     => $this->is_holiday,
                'is_maintenance' => $this->is_maintenance,
            ]);
            session()->flash('success', '✅ تم تعديل الجدولة بنجاح');
        } else {
            radiologyschedulingsettings::create($validated + [
                'is_holiday'     => $this->is_holiday,
                'is_maintenance' => $this->is_maintenance,
            ]);
            session()->flash('success', '✅ تم إضافة الجدولة بنجاح');
        }

        $this->resetForm();
        $this->activeTab = 'list';
    }

    public function edit($id)
    {
        $schedule = radiologyschedulingsettings::findOrFail($id);

        $this->schedule_id   = $schedule->id;
        $this->room_id       = $schedule->room_id;
        $this->device_id     = $schedule->device_id;
        $this->shift_type    = $schedule->shift_type;
        $this->start_time    = $schedule->start_time;
        $this->end_time      = $schedule->end_time;
        $this->is_holiday    = $schedule->is_holiday;
        $this->is_maintenance= $schedule->is_maintenance;
        $this->status        = $schedule->status;

        $this->activeTab = 'form';
    }

    public function createNew()
    {
        $this->resetForm();
        $this->activeTab = 'form';
    }

   public function delete($id)
{
    $schedule = radiologyschedulingsettings::findOrFail($id);
    $schedule->delete();
    session()->flash('success','🗑️ تم حذف السجل بنجاح');
}

    public function resetForm()
    {
        $this->schedule_id   = null;
        $this->room_id       = null;
        $this->device_id     = null;
        $this->shift_type    = null;
        $this->start_time    = null;
        $this->end_time      = null;
        $this->is_holiday    = false;
        $this->is_maintenance= false;
        $this->status        = 'active';
    }

    public function render()
    {
        $schedules = radiologyschedulingsettings::with(['room', 'device'])
            ->when($this->search, function ($q) {
                $q->whereHas('room', fn($qq) =>
                    $qq->where('name->ar','like',"%{$this->search}%")
                       ->orWhere('name->en','like',"%{$this->search}%")
                )->orWhereHas('device', fn($qq) =>
                    $qq->where('name->ar','like',"%{$this->search}%")
                       ->orWhere('name->en','like',"%{$this->search}%")
                );
            })
            ->latest()
            ->paginate(10);

        return view('livewire.radiology.settings.scheduling.index', [
            'schedules' => $schedules,
            'rooms'     => $this->rooms,
            'devices'   => $this->devices,
        ]);
    }
}
