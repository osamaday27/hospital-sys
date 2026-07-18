<?php

namespace App\Http\Controllers\general;

use App\Http\Controllers\Controller;
use App\models\general\adm_s_p_dis_dep;
use App\models\hr\Department;
use Illuminate\Http\Request;

class p_dis_depcontroller extends Controller
{
  public function index()
{
    // جلب الأقسام
    $departments = Department::all();
    // جلب جميع البيانات من الجدول مع الأقسام المرتبطة
    $existingData = adm_s_p_dis_dep::with('departments')->where('status','active')->get();

    // إرجاع العرض مع البيانات
    return view('administrative.inpatient.settings.p_dis_dep.create', compact('departments', 'existingData'));
}

public function store(Request $request, $id = null)
{
    // التحقق من المدخلات (السماح بأن تكون الأقسام فارغة)
    $validated = $request->validate([
        'departments' => 'nullable|array', // يمكن أن يكون الحقل فارغًا
        'departments.*' => 'exists:hr_department,id', // تحقق من صحة الأقسام المختارة
    ]);

    // جلب الأقسام المختارة أو تعيينها إلى مصفوفة فارغة إذا لم يتم تحديد أي قسم
    $selectedDepartments = $request->departments ?? [];

    if ($id) {
        // تعديل البيانات الموجودة
        $existingData = adm_s_p_dis_dep::find($id);
        if ($existingData) {
            // تحديث الأقسام
            $existingData->departments()->sync($selectedDepartments);
        }
    } else {
        // معالجة الأقسام المختارة
        foreach ($selectedDepartments as $departmentId) {
            $exists = adm_s_p_dis_dep::withTrashed()->where('hr_department_id', $departmentId)->exists();

            if (!$exists) {
                // إذا لم يكن القسم موجودًا، يتم إنشاؤه
                adm_s_p_dis_dep::create([
                    'hr_department_id' => $departmentId, // ربط القسم بمعرّف الإدارة
                    'status' => 'active', // تعيين الحالة إلى نشط
                ]);
            } else {
                // تحديث الحالة إلى نشط إذا كانت موجودة مسبقًا
                adm_s_p_dis_dep::withTrashed()->where('hr_department_id', $departmentId)->restore();
                adm_s_p_dis_dep::where('hr_department_id', $departmentId)->update(['status' => 'active']);
            }
        }
    }

    // التحقق من الأقسام غير المختارة وتحديث حالتها
    $allDepartments = adm_s_p_dis_dep::all(); // جلب جميع الأقسام
    foreach ($allDepartments as $existingData) {
        if (!in_array($existingData->hr_department_id, $selectedDepartments)) {
            // إذا لم يكن القسم في الأقسام المختارة، قم بتحديث حالته إلى "غير نشط"
            $existingData->update([
                'status' => 'inactive', // تحديث الحالة إلى غير نشط
            ]);
        }
    }

    // إعادة التوجيه مع رسالة نجاح
    return redirect()->route('adm_res_pa_discharge.index')->with('success', 'تم حفظ الأقسام بنجاح.');
}

}