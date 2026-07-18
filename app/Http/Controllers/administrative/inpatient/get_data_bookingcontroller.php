<?php
namespace App\Http\Controllers\administrative\inpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\adm_s_admission_reasons;
use App\models\general\adm_s_rooms;
use App\models\general\adm_s_beds;
use App\models\general\nursingstation;
use App\models\general\specialties;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\hr\Employee;
class get_data_bookingcontroller extends Controller
{
    public function getBedsByRoom(Request $request)
    {
        $beds = adm_s_beds::where('room_id', $request->room_id)
            ->where('status', 'available') // تأكد من أن الأسرة متاحة
            ->get();
        return response()->json($beds);
    }
    public function getRoomsByNursingStation(Request $request)
    {
        $rooms = adm_s_rooms::where('nursing_stations_id', $request->nursing_station_id)
            ->whereHas('beds', function ($query) {
                $query->where('status', 'available'); // تأكد من أن الأسرة متاحة
            })
            ->get();
        return response()->json($rooms);
    }

    public function getentrysources(Request $request)
    {
        // استرجاع مصادر الدخول المتاحة والنشطة
        $entrysources = adm_s_admission_reasons::where('type', $request->type)->where('status', 'active')->get();

        // تحديد اللغة الحالية للمستخدم
        $locale = app()->getLocale(); // الحصول على اللغة الحالية

        // إضافة الترجمات المناسبة لكل مصدر دخول
        $entrysourcesTranslated = $entrysources->map(function ($entrysource) use ($locale) {
            // تحديد اسم مصدر الدخول بناءً على اللغة
            $name = $locale == 'ar' ? $entrysource->name_ar : $entrysource->name_en;

            return [
                'id' => $entrysource->id,
                'name' => $name,
            ];
        });

        // إرجاع البيانات على هيئة JSON
        return response()->json($entrysourcesTranslated);
    }
    public function getphysician(Request $request)
    {
        // استرجاع الموظفين الذين هم أطباء وحالتهم "في الخدمة"
        $employees = Employee::where('employee_type', 'physician')->where('employee_status', 'in_service')->get();

        // تحديد اللغة الحالية للمستخدم
        $locale = app()->getLocale(); // الحصول على اللغة الحالية

        // إضافة الترجمة المناسبة لكل طبيب
        $employeesTranslated = $employees->map(function ($employee) use ($locale) {
            // إذا كانت اللغة هي العربية
            if ($locale == 'ar') {
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name_ar . ' ' . $employee->second_name_ar . ' ' . $employee->third_name_ar . ' ' . $employee->fourth_name_ar,
                ];
            } else {
                // إذا كانت اللغة هي الإنجليزية
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name_en . ' ' . $employee->second_name_en . ' ' . $employee->third_name_en . ' ' . $employee->fourth_name_en,
                ];
            }
        });

        // إرجاع البيانات على هيئة JSON
        return response()->json($employeesTranslated);
    }
    public function getnurse(Request $request)
    {
        // استرجاع الموظفين الذين هم أطباء وحالتهم "في الخدمة"
        $employees = Employee::where('employee_type', 'nursing')->where('employee_status', 'in_service')->get();

        // تحديد اللغة الحالية للمستخدم
        $locale = app()->getLocale(); // الحصول على اللغة الحالية

        // إضافة الترجمة المناسبة لكل طبيب
        $employeesTranslated = $employees->map(function ($employee) use ($locale) {
            // إذا كانت اللغة هي العربية
            if ($locale == 'ar') {
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name_ar . ' ' . $employee->second_name_ar . ' ' . $employee->third_name_ar . ' ' . $employee->fourth_name_ar,
                ];
            } else {
                // إذا كانت اللغة هي الإنجليزية
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name_en . ' ' . $employee->second_name_en . ' ' . $employee->third_name_en . ' ' . $employee->fourth_name_en,
                ];
            }
        });

        // إرجاع البيانات على هيئة JSON
        return response()->json($employeesTranslated);
    }

    public function getspecialty(Request $request)
    {
        // استرجاع مصادر الدخول المتاحة والنشطة
        $specializations = specialties::where('status', 'active')->get();

        // تحديد اللغة الحالية للمستخدم
        $locale = app()->getLocale(); // الحصول على اللغة الحالية

        // إضافة الترجمات المناسبة لكل مصدر دخول
        $specializationTranslated = $specializations->map(function ($specialization) use ($locale) {
            // تحديد اسم مصدر الدخول بناءً على اللغة
            $name = $locale == 'ar' ? $specialization->name_ar : $specialization->name_en;

            return [
                'id' => $specialization->id,
                'name' => $name,
            ];
        });

        // إرجاع البيانات على هيئة JSON
        return response()->json($specializationTranslated);
    }
}