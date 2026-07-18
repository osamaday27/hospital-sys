<?php

namespace App\Http\Livewire\Administrative\Surgical;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\General\adm_s_rooms_surgical;
use App\Models\Administrative\adm_s_operation_schedules;

class ScheduleRoom extends Component
{
    public $room_id, $room, $duration;
    public $days = [];
    public $schedule = [];

    protected $rules = [
        'duration' => 'required|integer|min:1',
    ];

    public function mount($encryptedId)
    {
        // ✅ فك التشفير بطريقة آمنة
        try {
            $this->room_id = is_numeric($encryptedId) ? $encryptedId : Crypt::decrypt($encryptedId);
        } catch (\Exception $e) {
            $this->room_id = $encryptedId; // fallback لو مش مشفر
        }

        // ✅ تحميل الغرفة
        $this->room = adm_s_rooms_surgical::findOrFail($this->room_id);

        // ✅ تحميل الجدولة الحالية إن وجدت
        $this->loadSchedule();
    }

    public function loadSchedule()
    {
        $records = adm_s_operation_schedules::where('id_room', $this->room_id)->get();

        $this->schedule = [];
        $this->days = [];

        foreach ($records as $record) {
            $day = $record->day;
            $this->days[$day] = true;
            $this->schedule[$day] = [
                'start_time' => $record->start_time,
                'end_time' => $record->end_time,
                'status' => $record->status ?? 'active',
            ];
        }

        // ✅ تحميل المدة العامة من أول سجل موجود
        $this->duration = $records->first()->duration ?? '';
    }

    public function toggleDay($day)
    {
        if (isset($this->days[$day]) && !$this->days[$day]) {
            unset($this->schedule[$day]);
        } else {
            $this->schedule[$day] = [
                'start_time' => '',
                'end_time' => '',
                'status' => 'active',
            ];
        }
    }

    public function save()
    {
        $this->validate();

        foreach ($this->days as $day => $enabled) {
            if ($enabled) {
                $start = $this->schedule[$day]['start_time'] ?? null;
                $end = $this->schedule[$day]['end_time'] ?? null;
                $status = $this->schedule[$day]['status'] ?? 'active';

                if (!$start || !$end) continue;

                adm_s_operation_schedules::updateOrCreate(
                    ['id_room' => $this->room_id, 'day' => $day],
                    [
                        'duration' => $this->duration,
                        'start_time' => $start,
                        'end_time' => $end,
                        'status' => $status,
                        'branches_id' => 1,
                        'user_id' => Auth::id(),
                    ]
                );
            } else {
                adm_s_operation_schedules::where('id_room', $this->room_id)
                    ->where('day', $day)
                    ->delete();
            }
        }

        session()->flash('success', trans('general_trans.added_successfully'));
        $this->loadSchedule();
    }

    public function render()
    {
        $daysList = [
            'saturday' => trans('outpatient_trans.saturday'),
            'sunday' => trans('outpatient_trans.sunday'),
            'monday' => trans('outpatient_trans.monday'),
            'tuesday' => trans('outpatient_trans.tuesday'),
            'wednesday' => trans('outpatient_trans.wednesday'),
            'thursday' => trans('outpatient_trans.thursday'),
            'friday' => trans('outpatient_trans.friday'),
        ];

        return view('livewire.administrative.surgical.schedule-room', [
            'daysList' => $daysList,
        ]);
    }
}