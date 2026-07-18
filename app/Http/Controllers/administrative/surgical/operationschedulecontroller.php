<?php
namespace App\Http\Controllers\administrative\surgical;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\hr\Employee;
use App\models\general\adm_s_rooms_surgical;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\surgicaldatasetting;
use App\models\administrative\adm_s_operation_schedules;
use Illuminate\Http\JsonResponse;
class operationschedulecontroller extends Controller
{
    public function index($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $adm_s_rooms_surgical = adm_s_rooms_surgical::findOrFail($id);
        return view('administrative.surgical.settings.scheduling.sindex', compact('adm_s_rooms_surgical'));
    }

    public function create($id = null)
    {
        if (!$id) {
            abort(404, 'المعرف غير موجود');
        }

    
        return view('administrative.surgical.settings.scheduling.create', compact('id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_room' => 'required|integer|exists:adm_s_rooms_surgical,id',
            'day' => 'required|array',
            'day.*' => 'in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'duration' => 'nullable|integer|min:1',

            'saturday_start_time' => 'nullable|date_format:H:i',
            'saturday_end_time' => 'nullable|date_format:H:i|after:saturday_start_time',

            'sunday_start_time' => 'nullable|date_format:H:i',
            'sunday_end_time' => 'nullable|date_format:H:i|after:sunday_start_time',

            'monday_start_time' => 'nullable|date_format:H:i',
            'monday_end_time' => 'nullable|date_format:H:i|after:monday_start_time',

            'tuesday_start_time' => 'nullable|date_format:H:i',
            'tuesday_end_time' => 'nullable|date_format:H:i|after:tuesday_start_time',

            'wednesday_start_time' => 'nullable|date_format:H:i',
            'wednesday_end_time' => 'nullable|date_format:H:i|after:wednesday_start_time',

            'thursday_start_time' => 'nullable|date_format:H:i',
            'thursday_end_time' => 'nullable|date_format:H:i|after:thursday_start_time',

            'friday_start_time' => 'nullable|date_format:H:i',
            'friday_end_time' => 'nullable|date_format:H:i|after:friday_start_time',
        ]);

        $encryptedId = Crypt::encryptString($request->id_room);

        try {
            foreach ($request->day as $day) {
                $start_time = $request->input("{$day}_start_time");
                $end_time = $request->input("{$day}_end_time");

                if (!$start_time || !$end_time) {
                    continue; // تخطي الأيام التي لم يتم تحديد وقت لها
                }

                $scheduleData = [
                    'id_room' => $request->id_room,
                    'duration' => $request->duration,
                    'day' => $day,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'branches_id' => 1,
                    'user_id' => Auth::id(),
                ];

                $existingSchedule = adm_s_operation_schedules::where('id_room', $scheduleData['id_room'])->where('day', $scheduleData['day'])->first();

                if ($existingSchedule) {
                    // التحقق من التعارض عند التحديث
                    $conflictExists = adm_s_operation_schedules::where('id_room', $scheduleData['id_room'])
                        ->where('day', $scheduleData['day'])
                        ->where('id', '!=', $existingSchedule->id) // تجاهل السجل الحالي عند التحديث
                        ->where(function ($query) use ($scheduleData) {
                            $query->where(function ($q) use ($scheduleData) {
                                $q->where('start_time', '<', $scheduleData['end_time'])->where('end_time', '>', $scheduleData['start_time']);
                            });
                        })
                        ->exists();

                    if ($conflictExists) {
                        return redirect()->back()->with('error', trans('general_trans.scheduling_conflict'));
                    }

                    $existingSchedule->update($scheduleData);
                } else {
                    // التحقق من التعارض عند الإضافة
                    $conflictExists = adm_s_operation_schedules::where('id_room', $scheduleData['id_room'])
                        ->where('day', $scheduleData['day'])
                        ->where(function ($query) use ($scheduleData) {
                            $query->where(function ($q) use ($scheduleData) {
                                $q->where('start_time', '<', $scheduleData['end_time'])->where('end_time', '>', $scheduleData['start_time']);
                            });
                        })
                        ->exists();

                    if ($conflictExists) {
                        return redirect()->back()->with('error', trans('general_trans.scheduling_conflict'));
                    }

                    adm_s_operation_schedules::create($scheduleData);
                }
            }

            return redirect()
                ->route('scheduling_operation.index', ['id' => $encryptedId])
                ->with('success', trans('general_trans.added_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', trans('general_trans.added_failed') . ': ' . $e->getMessage());
        }
    }

  public function getschedule(Request $request)
{
    $id_room = $request->get('id_room');
    $schedulingSettings = adm_s_operation_schedules::where('id_room', $id_room)->get();

    return DataTables::of($schedulingSettings)
        ->addColumn('day', function ($schedulingSettings) {
            $daysTranslations = [
                'saturday' => trans('outpatient_trans.saturday'),
                'sunday' => trans('outpatient_trans.sunday'),
                'monday' => trans('outpatient_trans.monday'),
                'tuesday' => trans('outpatient_trans.tuesday'),
                'wednesday' => trans('outpatient_trans.wednesday'),
                'thursday' => trans('outpatient_trans.thursday'),
                'friday' => trans('outpatient_trans.friday'),
            ];
            return $daysTranslations[$schedulingSettings->day] ?? trans('days.unknown');
        })
        ->addColumn('shift_1', fn($s) => $this->formatShift($s->start_time, $s->end_time))
        ->addColumn('status', function ($s) {
            $checked = $s->status === 'active' ? 'checked' : '';
            return '
                <div class="form-check form-switch text-center">
                    <input class="form-check-input status-toggle"
                           type="checkbox"
                           data-id="' . $s->id . '"
                           ' . $checked . '>
                </div>';
        })
        ->editColumn('created_at', fn($s) => Carbon::parse($s->created_at)->format('d/m/Y h:i A'))
        ->rawColumns(['day', 'shift_1', 'status'])
        ->make(true);
}

    private function formatShift($from, $to)
    {
        if ($from && $to) {
            return ' من ' . Carbon::parse($from)->format('h:i A') . ' الي ' . Carbon::parse($to)->format('h:i A');
        }
        return '-';
    }

    public function getDoctorSchedule(Request $request)
    {
        $id_room = $request->input('id_room');

        // جلب الجدولة للطبيب والعيادة المحددة
        $schedule = surgicaldatasetting::where('id_room', $id_room)->get()->groupBy('day');

        $formattedSchedule = [];

        foreach ($schedule as $day => $settings) {
            $formattedSchedule[$day] = [
                'shift1' => [
                    'from' => $settings->first()->start_time,
                    'to' => $settings->first()->end_time,
                ],
            ];
        }

        return response()->json(['schedule' => $formattedSchedule]);
    }
public function updateScheduleDay(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:adm_s_operation_schedules,id',
        'status' => 'required|string|in:active,inactive',
    ]);

    try {
        $schedule = \App\models\administrative\adm_s_operation_schedules::find($request->id);
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'السجل غير موجود.']);
        }

        $schedule->status = $request->status;
        $schedule->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث الحالة بنجاح!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()]);
    }
}



    public function getNearestAppointment($doctorId, $clinicId): JsonResponse
    {
        try {
            $currentDate = Carbon::now();
            $currentDay = strtolower($currentDate->format('l'));
            $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $currentDayIndex = array_search($currentDay, $daysOfWeek);
            $nearestSchedule = null;
            $nearestDate = null;
            // البحث في الأيام القادمة بدءًا من اليوم الحالي
            for ($i = 0; $i < 7; $i++) {
                $checkDayIndex = ($currentDayIndex + $i) % 7;
                $checkDay = $daysOfWeek[$checkDayIndex];
                $checkDate = $currentDate->copy()->addDays($i);

                $nearestSchedule = surgicaldatasetting::where('id_room', $doctorId)
                    ->where('id_clinic', $clinicId)
                    ->where('day', $checkDay)
                    ->where('status', 'active')
                    ->orderByRaw(
                        "
                    CASE
                        WHEN start_time >= ? THEN start_time
                        WHEN second_shift_from >= ? THEN second_shift_from
                        ELSE third_shift_from
                    END ASC",
                        [$currentDate->format('H:i'), $currentDate->format('H:i')],
                    )
                    ->first();

                if ($nearestSchedule) {
                    $nearestDate = $checkDate;
                    break;
                }
            }

            if ($nearestSchedule && $nearestDate) {
                return response()->json([
                    'status' => 'success',
                    'message' => trans('general_trans.nearest_appointment_found'),
                    'data' => [
                        'nearest_date' => $nearestDate->format('Y-m-d'),
                        'day' => $nearestSchedule->day,
                        'start_time' => $nearestSchedule->start_time,
                        'end_time' => $nearestSchedule->end_time,
                    ],
                ]);
            }

            return response()->json([
                'status' => 'info',
                'message' => trans('general_trans.no_available_appointments'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => trans('general_trans.operation_failed') . ': ' . $e->getMessage(),
            ]);
        }
    }
}