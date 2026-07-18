<?php

namespace App\Http\Controllers\administrative\call_center;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\administrative\adm_s_complaint_responsible;

class complaints_responsiblecontroller extends Controller
{
    public function index(Request $request)
{
    $responsibles = adm_s_complaint_responsible::all();

    // استخراج سجل مديري الأقسام وسجل مديري الإدارات
    $sectionManagerRecord = $responsibles->firstWhere('responsibles', 'sections managers');
    $departmentManagerRecord = $responsibles->firstWhere('responsibles', 'departments managers');

    // إذا لم يكن السجل موجوداً، نعطيه قيمة افتراضية 0
    $sectionManagerStatus = $sectionManagerRecord ? $sectionManagerRecord->status : 0;
    $departmentManagerStatus = $departmentManagerRecord ? $departmentManagerRecord->status : 0;

    return view('administrative/call_center/settings/complaints/responsible.index', compact( 'sectionManagerStatus', 'departmentManagerStatus'));
}

    public function create(Request $request)
{
    // تحديد حالة كل خيار: 1 إذا تم اختياره، و0 إذا لم يتم اختياره
    $sectionStatus = $request->has('sectionManager') ? 1 : 0;
    $departmentStatus = $request->has('departmentManager') ? 1 : 0;

    // تحديث السجل الخاص بمديري الأقسام أو إنشاؤه إذا لم يكن موجودًا
    adm_s_complaint_responsible::updateOrCreate(
        ['responsibles' => 'sections managers'],
        ['status' => $sectionStatus]
    );

    // تحديث السجل الخاص بمديري الادارات أو إنشاؤه إذا لم يكن موجودًا
    adm_s_complaint_responsible::updateOrCreate(
        ['responsibles' => 'departments managers'],
        ['status' => $departmentStatus]
    );

    return redirect()->back()->with('success',  trans('call_center_trans.data_saved_successfully') );
}

}
