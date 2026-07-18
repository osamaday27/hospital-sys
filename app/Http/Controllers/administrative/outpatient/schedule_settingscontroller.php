<?php
namespace App\Http\Controllers\administrative\outpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\hr\Employee;
use App\models\administrative\admsclinic;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\scheduling_settings;
use Illuminate\Http\JsonResponse;
class schedule_settingscontroller extends Controller
{
    use file_storage;

    public function index($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $admsclinic = admsclinic::findOrFail($id);
        return view('administrative.outpatient.settings.schedule_index', compact('admsclinic'));
    }

    public function create($id)
    {
        $doctor= Employee::where('employee_type', 'physician')->where('employee_status', 'in_service')->get();
        $idi = Crypt::decrypt($id);
        $admsclinic = admsclinic::find($idi);
        return view('administrative.outpatient.settings.schedule_create', compact('doctor', 'admsclinic'));
    }
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            
            'id_doctor'            => 'required|integer|exists:employees,id',
            'id_clinic'            => 'required|integer|exists:adm_s_clinics,id',
            'day'                  => 'required|array',
            'day.*'                => 'in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            
            'saturday_shift1_from' => 'nullable|date_format:H:i',
            'saturday_shift1_to'   => 'nullable|date_format:H:i|after:saturday_shift1_from',
            'saturday_shift2_from' => 'nullable|date_format:H:i',
            'saturday_shift2_to'   => 'nullable|date_format:H:i|after:saturday_shift2_from',
            'saturday_shift3_from' => 'nullable|date_format:H:i',
            'saturday_shift3_to'   => 'nullable|date_format:H:i|after:saturday_shift3_from',
            
            'sunday_shift1_from'   => 'nullable|date_format:H:i',
            'sunday_shift1_to'     => 'nullable|date_format:H:i|after:sunday_shift1_from',
            'sunday_shift2_from'   => 'nullable|date_format:H:i',
            'sunday_shift2_to'     => 'nullable|date_format:H:i|after:sunday_shift2_from',
            'sunday_shift3_from'   => 'nullable|date_format:H:i',
            'sunday_shift3_to'     => 'nullable|date_format:H:i|after:sunday_shift3_from',
            
            'monday_shift1_from'   => 'nullable|date_format:H:i',
            'monday_shift1_to'     => 'nullable|date_format:H:i|after:monday_shift1_from',
            'monday_shift2_from'   => 'nullable|date_format:H:i',
            'monday_shift2_to'     => 'nullable|date_format:H:i|after:monday_shift2_from',
            'monday_shift3_from'   => 'nullable|date_format:H:i',
            'monday_shift3_to'     => 'nullable|date_format:H:i|after:monday_shift3_from',
            
            'tuesday_shift1_from'  => 'nullable|date_format:H:i',
            'tuesday_shift1_to'    => 'nullable|date_format:H:i|after:tuesday_shift1_from',
            'tuesday_shift2_from'  => 'nullable|date_format:H:i',
            'tuesday_shift2_to'    => 'nullable|date_format:H:i|after:tuesday_shift2_from',
            'tuesday_shift3_from'  => 'nullable|date_format:H:i',
            'tuesday_shift3_to'    => 'nullable|date_format:H:i|after:tuesday_shift3_from',
            
            'wednesday_shift1_from'=> 'nullable|date_format:H:i',
            'wednesday_shift1_to'  => 'nullable|date_format:H:i|after:wednesday_shift1_from',
            'wednesday_shift2_from'=> 'nullable|date_format:H:i',
            'wednesday_shift2_to'  => 'nullable|date_format:H:i|after:wednesday_shift2_from',
            'wednesday_shift3_from'=> 'nullable|date_format:H:i',
            'wednesday_shift3_to'  => 'nullable|date_format:H:i|after:wednesday_shift3_from',
            
            'thursday_shift1_from' => 'nullable|date_format:H:i',
            'thursday_shift1_to'   => 'nullable|date_format:H:i|after:thursday_shift1_from',
            'thursday_shift2_from' => 'nullable|date_format:H:i',
            'thursday_shift2_to'   => 'nullable|date_format:H:i|after:thursday_shift2_from',
            'thursday_shift3_from' => 'nullable|date_format:H:i',
            'thursday_shift3_to'   => 'nullable|date_format:H:i|after:thursday_shift3_from',
            
            'friday_shift1_from'   => 'nullable|date_format:H:i',
            'friday_shift1_to'     => 'nullable|date_format:H:i|after:friday_shift1_from',
            'friday_shift2_from'   => 'nullable|date_format:H:i',
            'friday_shift2_to'     => 'nullable|date_format:H:i|after:friday_shift2_from',
            'friday_shift3_from'   => 'nullable|date_format:H:i',
            'friday_shift3_to'     => 'nullable|date_format:H:i|after:friday_shift3_from',
        ]);
        $encryptedId = Crypt::encryptString($request->id_clinic);
        try {
            foreach ($request->day as $day) {
                $scheduleData = [
                    'id_doctor' => $request->id_doctor,
                    'id_clinic' => $request->id_clinic,
                    'day' => $day,
                    'first_shift_from' => $request->input("{$day}_shift1_from"),
                    'first_shift_to' => $request->input("{$day}_shift1_to"),
                    'second_shift_from' => $request->input("{$day}_shift2_from"),
                    'second_shift_to' => $request->input("{$day}_shift2_to"),
                    'third_shift_from' => $request->input("{$day}_shift3_from"),
                    'third_shift_to' => $request->input("{$day}_shift3_to"),
                    'status' => 'active',
                    'user_id' => Auth::id(),
                ];

                // تحقق من وجود سجل موجود مسبقاً للطبيب في نفس اليوم والعيادة
                $existingSchedule = scheduling_settings::where('id_doctor', $scheduleData['id_doctor'])->where('id_clinic', $scheduleData['id_clinic'])->where('day', $scheduleData['day'])->first();

                if ($existingSchedule) {
                    // تحديث السجل بدلاً من إنشاء سجل جديد
                    $existingSchedule->update($scheduleData);
                } else {
                    // التحقق من وجود تعارض في الأوقات
                    $conflictExists = scheduling_settings::where('id_doctor', $scheduleData['id_doctor'])
                        ->where('day', $scheduleData['day'])
                        ->where(function ($query) use ($scheduleData) {
                            $query
                                ->whereBetween('first_shift_from', [$scheduleData['first_shift_from'], $scheduleData['first_shift_to']])
                                ->orWhereBetween('first_shift_to', [$scheduleData['first_shift_from'], $scheduleData['first_shift_to']])
                                ->orWhereBetween('second_shift_from', [$scheduleData['second_shift_from'], $scheduleData['second_shift_to']])
                                ->orWhereBetween('second_shift_to', [$scheduleData['second_shift_from'], $scheduleData['second_shift_to']])
                                ->orWhereBetween('third_shift_from', [$scheduleData['third_shift_from'], $scheduleData['third_shift_to']])
                                ->orWhereBetween('third_shift_to', [$scheduleData['third_shift_from'], $scheduleData['third_shift_to']]);
                        })
                        ->exists();

                    if ($conflictExists) {
                        return redirect()->back()->with('error', trans('general_trans.scheduling_conflict'));
                    }

                    // إنشاء الجدولة
                    scheduling_settings::create($scheduleData);
                }
            }

            // إعادة التوجيه إلى صفحة الـ index مع تمرير الرسالة و `encryptedId`
            return redirect()
                ->route('schedule_settings.index', ['clinic' => $encryptedId])
                ->with('success', trans('general_trans.added_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', trans('general_trans.added_failed') . ': ' . $e->getMessage());
        }
    }

    // عرض تفاصيل جدولة معينة
    public function show(AdmSScheduling $scheduling)
    {
        return view('general.scheduling.show', compact('scheduling'));
    }

    // عرض نموذج تعديل جدولة
    public function edit($encryptedId)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($encryptedId);

        // الحصول على بيانات الجدولة باستخدام الـ ID
        $scheduling = AdmSScheduling::findOrFail($id);

        // جلب جميع الأطباء
        $doctors = Doctor::all();

        return view('general.scheduling.edit', compact('scheduling', 'doctors'));
    }

    public function getschedule(Request $request)
    {
        $clinicId = $request->get('clinic_id'); // Retrieve clinic ID
        $schedulingSettings = scheduling_settings::where('id_clinic', $clinicId)
            ->with('doctor') // Eager load doctor relationship
            ->get();

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
            ->addColumn('shift_1', fn($schedulingSettings) => $this->formatShift($schedulingSettings->first_shift_from, $schedulingSettings->first_shift_to))
            ->addColumn('shift_2', fn($schedulingSettings) => $this->formatShift($schedulingSettings->second_shift_from, $schedulingSettings->second_shift_to))
            ->addColumn('shift_3', fn($schedulingSettings) => $this->formatShift($schedulingSettings->third_shift_from, $schedulingSettings->third_shift_to))
            ->addColumn('status', function ($schedulingSettings) {
                // إنشاء checkbox مع حالة محددة بناءً على قيمة status
                $checked = $schedulingSettings->status === 'active' ? 'checked' : '';
                return '<input type="checkbox" class="status-toggle" data-id="' . $schedulingSettings->id . '" ' . $checked . '>';
            })
            ->addColumn('doctor', fn($schedulingSettings) => $schedulingSettings->doctor->full_name)
            ->editColumn('created_at', fn($schedulingSettings) => Carbon::parse($schedulingSettings->created_at)->format('d/m/Y h:i A'))
            ->rawColumns(['status', 'doctor', 'day', 'shift_1', 'shift_2', 'shift_3'])
            ->make(true);
    }

    private function formatShift($from, $to)
    {
        return $from && $to ? "$from - $to" : '-';
    }
    public function getDoctorSchedule(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $clinicId = $request->input('clinic_id');

        // جلب الجدولة للطبيب والعيادة المحددة
        $schedule = scheduling_settings::where('id_doctor', $doctorId)->where('id_clinic', $clinicId)->get()->groupBy('day');

        $formattedSchedule = [];

        foreach ($schedule as $day => $settings) {
            $formattedSchedule[$day] = [
                'shift1' => [
                    'from' => $settings->first()->first_shift_from,
                    'to' => $settings->first()->first_shift_to,
                ],
                'shift2' => [
                    'from' => $settings->first()->second_shift_from,
                    'to' => $settings->first()->second_shift_to,
                ],
                'shift3' => [
                    'from' => $settings->first()->third_shift_from,
                    'to' => $settings->first()->third_shift_to,
                ],
            ];
        }

        return response()->json(['schedule' => $formattedSchedule]);
    }
    public function updateScheduleDay(Request $request)
    {
        // التحقق من صلاحيات المستخدم (اختياري)
        $request->validate([
            'id' => 'required|integer|exists:adm_s_scheduling,id',
            'status' => 'required|string|in:active,inactive', // تحديد القيم المسموح بها
        ]);

        // الحصول على البيانات من الطلب
        $scheduleId = $request->input('id');
        $status = $request->input('status');

        // تحديث حالة الجدول بناءً على الـ ID
        $schedule = scheduling_settings::find($scheduleId);
        if (!$schedule) {
            return response()->json(['error' => 'السجل غير موجود.'], 404);
        }

        // تحديث حالة الـ schedule
        $schedule->status = $status;
        $schedule->save();

        // إعادة الاستجابة بتأكيد التحديث
        return response()->json(['success' => 'تم تحديث الحالة بنجاح!']);
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
    
                $query = scheduling_settings::where('id_doctor', $doctorId)
                    ->where('id_clinic', $clinicId)
                    ->where('day', $checkDay)
                    ->where('status', 'active');
    
                if ($i === 0) {
                    // لو اليوم الحالي فقط تحقق من الوقت الحالي
                    $query->where(function($q) use ($currentDate) {
                        $currentTime = $currentDate->format('H:i');
                        $q->where('first_shift_from', '>=', $currentTime)
                          ->orWhere('second_shift_from', '>=', $currentTime)
                          ->orWhere('third_shift_from', '>=', $currentTime);
                    });
                }
    
                // ترتيب حسب أول شيفت
                $query->orderBy('first_shift_from');
    
                $nearestSchedule = $query->first();
    
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
                        'first_shift_from' => $nearestSchedule->first_shift_from,
                        'first_shift_to' => $nearestSchedule->first_shift_to,
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