<?php

namespace App\Http\Controllers\administrative\inquiries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\scheduling_settings;
use App\models\hr\Employee;

class inquiriescontroller extends Controller
{
   public function search(Request $request)
{
    $request->validate([
        'doctor_id' => 'required|exists:employees,id',
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
    ]);

    $query = scheduling_settings::where('id_doctor', $request->doctor_id);

    if ($request->from_date && $request->to_date) {
        $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
    }
$schedules = $query->orderBy('day')->get()->map(function ($schedule) {
    // التحقق أولاً من وجود مواعيد حقيقية
    // التحقق من الفترة الأولى
    if ($schedule->first_shift_from && $schedule->first_shift_to && $schedule->first_shift_from !== $schedule->first_shift_to) {
        // تحويل الأوقات إلى تنسيق 12 ساعة مع AM/PM
        $schedule->first_shift_from = Carbon::parse($schedule->first_shift_from)->format('g:i A');
        $schedule->first_shift_to = Carbon::parse($schedule->first_shift_to)->format('g:i A');
        $schedule->first_shift = $schedule->first_shift_from . ' - ' . $schedule->first_shift_to;
    } else {
        $schedule->first_shift = 'لا يوجد موعد';
    }

    // التحقق من الفترة الثانية
    if ($schedule->second_shift_from && $schedule->second_shift_to && $schedule->second_shift_from !== $schedule->second_shift_to) {
        // تحويل الأوقات إلى تنسيق 12 ساعة مع AM/PM
        $schedule->second_shift_from = Carbon::parse($schedule->second_shift_from)->format('g:i A');
        $schedule->second_shift_to = Carbon::parse($schedule->second_shift_to)->format('g:i A');
        $schedule->second_shift = $schedule->second_shift_from . ' - ' . $schedule->second_shift_to;
    } else {
        $schedule->second_shift = 'لا يوجد موعد';
    }

    // التحقق من الفترة الثالثة
    if ($schedule->third_shift_from && $schedule->third_shift_to && $schedule->third_shift_from !== $schedule->third_shift_to) {
        // تحويل الأوقات إلى تنسيق 12 ساعة مع AM/PM
        $schedule->third_shift_from = Carbon::parse($schedule->third_shift_from)->format('g:i A');
        $schedule->third_shift_to = Carbon::parse($schedule->third_shift_to)->format('g:i A');
        $schedule->third_shift = $schedule->third_shift_from . ' - ' . $schedule->third_shift_to;
    } else {
        $schedule->third_shift = 'لا يوجد موعد';
    }

    // إضافة اسم العيادة
    $schedule->clinic_name = $schedule->clinic ? $schedule->clinic->name_ar : 'غير محدد';

    return $schedule;
});


    $doctors = Employee::where('employee_type', 'physician')->where('employee_status', 'in_service')->get();

    // إذا كانت الطلبية AJAX، نعيد جزء من الـ view فقط
    if ($request->ajax()) {
        return view('administrative.inquiries.doctor.schedule_results', compact('schedules', 'doctors'));
    }

    return view('administrative.inquiries.doctor.doctor_schedule', compact('schedules', 'doctors'));
}

}