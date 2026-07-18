<?php

namespace App\Http\Controllers\administrative\call_center;

use Exception;
use App\models\HR\section;
use Illuminate\Http\Request;
use App\models\HR\Department;
use App\Traits\web\file_storage;
use App\Traits\web\get_contract;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\models\administrative\adm_b_complaint;
use App\models\administrative\adm_b_complaint_status;
use Illuminate\Contracts\Encryption\DecryptException;
use App\models\administrative\adm_s_complaint_problem;
use App\models\administrative\adm_s_complaint_category;
use App\models\administrative\adm_s_complaint_responsible;
use App\models\administrative\medical_record;









class complaintscontroller extends Controller
{
    use file_storage, get_contract;

    public function index()
    {
        return view('administrative.call_center.program.complaints.index');
    }
    public function choosePatient()
    {
        return view('administrative.call_center.program.complaints.choose_patient');
    }

public function show($encryptedId)
{
    try {
        // فك تشفير المعرف
        $id = Crypt::decryptString($encryptedId);
    } catch (DecryptException $e) {
        return redirect()->route('complaints.index')->with('error', 'معرف الشكوى غير صالح.');
    }

    // جلب الشكوى
    $complaint = adm_b_complaint::with('patient')->findOrFail($id);

    // فك بيانات الاتصال (إذا كانت مخزنة بصيغة JSON)
    $contactInformation = is_array($complaint->contact_information)
        ? $complaint->contact_information
        : json_decode($complaint->contact_information, true);

    // جلب الإجراءات السابقة
    $previousActions = adm_b_complaint_status::where('complaint_id', $id)->get();

    // التحقق إذا كان المريض مسجل
    $patientInfo = null;
    if ($complaint->registered_patient ) {
        // نفترض أن العلاقة موجودة باسم patient، أو يمكنك استخدام نموذج مباشرة
        $patient = medical_record::find($complaint->patient_id);
        if ($patient) {
            $patientInfo = [
                'name' => $patient->full_name,
                'age' => $patient->age,
                'gender' => $patient->gender == 1 ? 'male' : 'female',
            ];
        }
    }

    return view('administrative.call_center.program.complaints.review', compact(
        'complaint',
        'contactInformation',
        'previousActions',
        'patientInfo'
    ));
}

public function deleteAttachment(Request $request)
{
    $file = $request->input('file');
    $complaintId = $request->input('complaint_id'); // تأكد من تمرير رقم الشكوى إذا كنت تحتاجه

    // قم بعمل عمليات الحذف هنا (مثلاً حذف الملف من التخزين وتحديث قاعدة البيانات)
    // حذف الملف من التخزين:
    if (Storage::disk('public')->exists( $file)) {
        Storage::disk('public')->delete( $file);
    }

    // تحديث سجل الشكوى: هنا تفترض أن المرفقات مخزنة كـ JSON وتحتاج إلى إزالة الملف المحدد
    $complaint = adm_b_complaint::findOrFail($complaintId);
    $attachments = json_decode($complaint->attachments, true) ?? [];
    // إزالة الملف من المصفوفة
    $attachments = array_filter($attachments, function($value) use ($file) {
        return $value !== $file;
    });
    // تحديث السجل
    $complaint->attachments = json_encode(array_values($attachments));
    $complaint->save();

    return response()->json(['success' => true]);
}

public function addAction(Request $request, $id)
{
    // تعريف مصفوفة الحالات المسموح بها مع ترجمتها
    $allowedStatuses = [
        'It has been sent to the department' => 'تم الإرسال إلى القسم',
        'The department has responded'         => 'تم الرد من القسم',
        'The follow-up with the patient has been done' => 'تم المتابعة مع المريض',
        'The issue has been resolved'           => 'تم الحل',
        'The problem has not been solved'       => 'لم يتم الحل',
        'closed'                                => 'مغلق',
    ];

    // التحقق من صحة المدخلات مع استخدام قاعدة in للتحقق من الحالة
    $validated = $request->validate([
        'new_action' => 'nullable|string',
        'status'     => 'required|string|in:' . implode(',', array_keys($allowedStatuses)),
    ]);

    // استرجاع الشكوى المطلوبة للتحديث
    $complaint = adm_b_complaint::findOrFail($id);

    // استخدام القيمة الإنجليزية المرسلة والحصول على الترجمة العربية
    $name_en = $validated['status'];
    $name_ar = $allowedStatuses[$name_en] ?? $name_en;

    // إعداد بيانات سجل الحالة الجديد
    $data = [
        'complaint_id'   => $complaint->id,
        'action'         => $validated['new_action'],
        'name_en'        => $name_en,
        'name_ar'        => $name_ar,
        'updated_by'     => auth()->id() ?? null,
        'responsible_id' => [] // أو تعيين المسؤولين حسب الحاجة
    ];

    adm_b_complaint_status::create($data);

    return redirect()->route('complaints.index')
        ->with('success', trans('call_center_trans.action_added_successfully'));
}

    private function complaintActionButtons($questionnaire)
    {
        // تشفير معرف الاستبيان
        $encryptedQuestionnaireId = Crypt::encryptString($questionnaire->id);

        $actions = '<div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-cog-outline"></i> ' . trans('inpatient_trans.action') . '
        </button>
        <div class="dropdown-menu dropdown-menu-end">';
        // **إظهار زر التعديل فقط إذا لم يكن مغلقًا**
        $latestStatus = $questionnaire->statuses()->orderBy('created_at', 'desc')->first();

        if ($latestStatus->name_en !== 'closed') {
            $actions .= '<a class="dropdown-item" href="' . route('complaints.edit', ['complaint' => $encryptedQuestionnaireId]) . '">
        <i class="ri-pencil-fill text-warning"></i> ' . trans('general_trans.edit') . '
        </a>';
        }

        // مراجعة الاستبيان
        $actions .= '<a class="dropdown-item" href="' . route('complaints.show', ['complaint' => $encryptedQuestionnaireId]) . '">
        <i class="ri-eye-fill text-info"></i> ' . trans('call_center_trans.review_complaint') . '
        </a>';
        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteFunction(\'' . $encryptedQuestionnaireId . '\', \'' . $questionnaire->id . '\')">
        <i class="ri-delete-bin-6-line text-danger"></i> ' . trans('general_trans.delete') . '
        </a>';


        return $actions;
    }
    public function getComplaints(Request $request)
    {
        // جلب الشكاوي مع العلاقات المطلوبة (مثلاً المريض)
        $complaints = adm_b_complaint::with('patient', 'statuses')
            ->orderBy('updated_at', 'desc')
            ->get();

        // تحويل النتائج إلى DataTables وتحديد الأعمدة المطلوبة
        return DataTables::of($complaints)
            ->addColumn('action', function ($complaint) {
                return $this->complaintActionButtons($complaint);
            })
            ->addColumn('title', function ($complaint) {
                return $complaint->title;
            })
            ->addColumn('patient_name', function ($complaint) {
                return $complaint->registered_patient == 0
                    ? $complaint->patient_name
                    : ($complaint->patient ? $complaint->patient->full_name : '-');
            })
            ->addColumn('priority', function ($complaint) {
                return $this->complaintPriorityBadge($complaint);})
            ->addColumn('complaint_date', function ($complaint) {
                return $complaint->complaint_date;
            })
            ->addColumn('status', function ($complaint) {
                // الحصول على أحدث حالة باستخدام ترتيب تنازلي حسب created_at
                $latestStatus = $complaint->statuses()->orderBy('created_at', 'desc')->first();
                if ($latestStatus) {
                    // إذا كنت تستخدم ترجمات أو Accessor لعرض الاسم المناسب بناءً على اللغة:
                    return app()->getLocale() == 'ar' ? $latestStatus->name_ar : $latestStatus->name_en;
                    // أو ببساطة: return $latestStatus->name_ar;
                }
                return '-';
            })
            ->rawColumns(['priority','action'])
            ->make(true);
    }

    private function complaintPriorityBadge($complaint)
    {
        $statusLabels = [
            'urgent' => 'bg-danger',
            'normal' => 'bg-primary',
            'necessary' => 'bg-success',

        ];

        return '<span class="badge ' . ($statusLabels[$complaint->priority] ?? 'badge-light') . '  badge-border">' . trans('status.' . $complaint->priority) . '</span>';
    }
    public function createComplaints(){

        $categories = adm_s_complaint_category::where('status', true)->get();
        $problems = adm_s_complaint_problem::where('status', true)->get();
        $sections = section::where('status', true)->get();
        $departments = Department::where('status', true)->get();

        return view('administrative.call_center.program.complaints.create',compact('categories','problems','sections','departments'));
    }
    public function store(Request $request)
{
    // التحقق من صحة البيانات (validation)
    $validated = $request->validate([
        'category'              => 'required|exists:adm_s_complaint_categories,id',
        'problem'               => 'required|exists:adm_s_complaint_problems,id',
        'priority'              => 'required|in:urgent,normal,necessary',
        'complaint_date'        => 'required|date',
        'department'            => 'required|exists:hr_department,id',
        'section'               => 'required|exists:hr_sections,id',
        'complaint_address'     => 'required|string|max:255',
        'complaint_title'       => 'required|string|max:255',
        'complaint_text'        => 'required|string',
        // المرفقات اختيارية مع تحديد الصيغ المسموح بها
        'complaint_attachments.*' => 'nullable|file|max:2048',
    ]);

    // تجميع بيانات الاتصال: نحول المصفوفتين إلى مصفوفة من الأزواج
    $phones = $request->input('contact_phone', []);
    $emails = $request->input('contact_email', []);
    $contacts = [];
    foreach ($phones as $index => $phone) {
        if (!empty($phone) || !empty($emails[$index] ?? '')) {
            $contacts[] = [
                'phone' => $phone,
                'email' => $emails[$index] ?? null,
            ];
        }
    }

    // معالجة رفع الملفات إن وجدت
    $attachments = [];
    if ($request->hasFile('complaint_attachments')) {
        $files = $request->file('complaint_attachments');
        $year = date('Y');
        $month = date('m');
        // استخدم مجلد "complaints" لتخزين مرفقات الشكوى
        $storagePath = "complaints/{$year}/{$month}";

        foreach ($files as $file) {
            if ($file->isValid()) {
                // إنشاء اسم آمن للملف
                $safeFileName = preg_replace('/[^A-Za-z0-9\-_.]/', '', $file->getClientOriginalName());
                $fileName = time() . '_' . uniqid() . '_' . $safeFileName;

                try {
                    // رفع الملف باستخدام storeAs في المجلد المحدد داخل مساحة التخزين العامة
                    $path = $file->storeAs($storagePath, $fileName, 'public');
                    $attachments[] = $path;
                } catch (Exception $e) {
                }
            }
        }
    }


    /*
     * نقوم بتجهيز البيانات لتخزينها في قاعدة البيانات بحيث نطابق أسماء الحقول الموجودة في الموديل:
     * - 'title'           => عنوان الشكوى (complaint_title)
     * - 'complaint_place' => مكان الشكوى (complaint_address)
     * - 'complaint'       => نص الشكوى (complaint_text)
     * - باقي الحقول تبقى كما هي.
     */
    $data = [
        'title'               => $request->input('complaint_title'),
        'registered_patient'  => $request->input('registered_patient'),  // قيمة مخفية من الفورم
        'patient_id'          => $request->input('patient_id'),
        'visit_id'            => $request->input('visit_id'),
        'patient_name'        => $request->input('name'),
        'patient_age'         => $request->input('age'),
        'patient_gender'      => $request->input('gender'),
        'problem_id'          => $request->input('problem'),
        'priority'            => $request->input('priority'),
        'complaint_date'      => $request->input('complaint_date'),
        'department_id'       => $request->input('department'),
        'sections_id'         => $request->input('section'),
        'complaint_place'     => $request->input('complaint_address'),
        'complaint'           => $request->input('complaint_text'),
        'attachments'         => $attachments,
        'contact_information' => $contacts,
    ];
    $data['attachments'] = json_encode($attachments);

 // إنشاء الشكوى باستخدام الموديل (adm_b_complaint)
 $complaint = adm_b_complaint::create($data);

 // الآن نحدد المسؤولين الحاليين عن الشكوى وفق الإعدادات الموجودة في جدول adm_s_complaint_responsible
 $responsibleIds = [];
 // استرجاع الإعدادات التي تكون حالتها 1
 $settings =adm_s_complaint_responsible::where('status', 1)->get();
 foreach ($settings as $setting) {
     // نفترض أن الحقل "responsibles" يحتوي على نص مثل "sections managers" أو "departments managers"
     if ($setting->responsibles == 'sections managers') {
         // الحصول على مدير القسم من الموديل الخاص بالأقسام (section)
         $section = section::find($request->input('section'));
         if ($section && $section->manager_id) {
             $responsibleIds[] = $section->manager_id;
         }
     }
     if ($setting->responsibles == 'departments managers') {
         // الحصول على مدير الإدارة من الموديل الخاص بالإدارات (Department)
         $department = Department::find($request->input('department'));
         if ($department && $department->manager_id) {
             $responsibleIds[] = $department->manager_id;
         }
     }
 }
// بعد تخزين الشكوى، نقوم بإنشاء حالة الشكوى
    $statusData = [
        'name_en'      => 'new',
        'name_ar'      => 'جديده',
        'responsible_id' => $responsibleIds, // إذا كانت من نوع array، أو اتركها فارغة حسب الحالة
        'complaint_id' => $complaint->id,
        'updated_by'   => auth()->id() ?? null,
    ];

    adm_b_complaint_status::create($statusData);
    return redirect()->route('complaints.index')
                     ->with('success', trans('call_center_trans.complaint_created_successfully'));
}

public function update(Request $request, $id)
{
    // استرجاع الشكوى المطلوبة للتحديث
    $complaint = adm_b_complaint::findOrFail($id);

    // التحقق من صحة البيانات (validation)
    $validated = $request->validate([
        'problem'               => 'required|exists:adm_s_complaint_problems,id',
        'priority'              => 'required|in:urgent,normal,necessary',
        'complaint_date'        => 'required|date',
        'department'            => 'required|exists:hr_department,id',
        'section'               => 'required|exists:hr_sections,id',
        'complaint_address'     => 'required|string|max:255',
        'complaint_title'       => 'required|string|max:255',
        'complaint_text'        => 'required|string',
        // المرفقات اختيارية مع تحديد الصيغ المسموح بها
        'complaint_attachments.*' => 'nullable|file|max:2048',
        // يمكن إضافة قواعد التحقق لاسم المريض والعمر والجنس
        'name'                  => 'required_if:registered_patient,0|string|max:255',
        'age'                   => 'required_if:registered_patient,0|integer|min:0',
        'gender'                => 'required_if:registered_patient,0|in:male,female',
    ]);

    // تجميع بيانات الاتصال: نحول المصفوفتين إلى مصفوفة من الأزواج
    $phones = $request->input('contact_phone', []);
    $emails = $request->input('contact_email', []);
    $contacts = [];
    foreach ($phones as $index => $phone) {
        if (!empty($phone) || !empty($emails[$index] ?? '')) {
            $contacts[] = [
                'phone' => $phone,
                'email' => $emails[$index] ?? null,
            ];
        }
    }

    // معالجة رفع الملفات الجديدة إن وجدت
    $attachments = [];
    // احتفظ بالمرفقات القديمة إن وُجدت
    $existingAttachments = json_decode($complaint->attachments, true) ?? [];

    if ($request->hasFile('complaint_attachments')) {
        $files = $request->file('complaint_attachments');
        $year = date('Y');
        $month = date('m');
        // استخدم مجلد "complaints" لتخزين مرفقات الشكوى
        $storagePath = "complaints/{$year}/{$month}";

        foreach ($files as $file) {
            if ($file->isValid()) {
                // إنشاء اسم آمن للملف
                $safeFileName = preg_replace('/[^A-Za-z0-9\-_.]/', '', $file->getClientOriginalName());
                $fileName = time() . '_' . uniqid() . '_' . $safeFileName;

                try {
                    // رفع الملف باستخدام storeAs في المجلد المحدد داخل مساحة التخزين العامة
                    $path = $file->storeAs($storagePath, $fileName, 'public');
                    $existingAttachments[] = $path;
                } catch (Exception $e) {
                    // يمكنك تسجيل الخطأ أو التعامل معه هنا
                }
            }
        }
    }
    // تحويل المرفقات إلى json (إذا لم يكن هناك Cast تلقائي)
    $attachments = json_encode($existingAttachments);

    /*
     * تجهيز البيانات لتحديث الشكوى بحيث تتطابق أسماء الحقول مع تلك الموجودة في الموديل:
     * - 'title'           => عنوان الشكوى (complaint_title)
     * - 'complaint_place' => مكان الشكوى (complaint_address)
     * - 'complaint'       => نص الشكوى (complaint_text)
     * - بالإضافة إلى بيانات المريض القابلة للتعديل (name, age, gender)
     */
    $data = [
        'title'               => $request->input('complaint_title'),
        'registered_patient'  => $request->input('registered_patient'),
        'patient_id'          => $request->input('patient_id'),
        'visit_id'            => $request->input('visit_id'),
        'patient_name'        => $request->input('name'),
        'patient_age'         => $request->input('age'),
        'patient_gender'      => $request->input('gender'),
        'problem_id'          => $request->input('problem'),
        'priority'            => $request->input('priority'),
        'complaint_date'      => $request->input('complaint_date'),
        'department_id'       => $request->input('department'),
        'sections_id'         => $request->input('section'),
        'complaint_place'     => $request->input('complaint_address'),
        'complaint'           => $request->input('complaint_text'),
        'attachments'         => $attachments,
        'contact_information' => $contacts,
    ];

    // تحديث السجل في قاعدة البيانات
    $complaint->update($data);

    // تحديث حالة الشكوى: نعيد تحديد المسؤولين الحاليين عن الشكوى وفق الإعدادات
    $complaintStatus = adm_b_complaint_status::where('complaint_id', $complaint->id)
                    ->orderBy('updated_at', 'desc') // أو orderBy('id', 'desc')
                    ->first();
if ($complaintStatus) {
    $complaintStatus->update([
        'updated_by' => auth()->id() ?? null,
    ]);
}


    return redirect()->route('complaints.index')
                     ->with('success', trans('call_center_trans.complaint_updated_successfully'));
}


public function edit($encryptedId)
{
    try {
        // فك تشفير الـ id
        $id = Crypt::decryptString($encryptedId);
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'معرف غير صالح');
    }

    // جلب سجل الشكوى مع العلاقات المطلوبة
    $complaint = adm_b_complaint::with([
        'patient',      // علاقة المريض
        'visit',        // علاقة الزيارة
        'problem.category', // جلب المشكلة والفئة المرتبطة بها
        'department',   // علاقة الإدارة
        'section',      // علاقة القسم
        'statuses'      // حالات الشكوى
    ])->findOrFail($id);
// dd($complaint->problem);
    // جلب الفئة بناءً على المشكلة المرتبطة بالشكوى
    $selectedCategory = $complaint->problem ? $complaint->problem->category_id : null;

    // جلب جميع الفئات والمشاكل والإدارات والأقسام
    $categories  = adm_s_complaint_category::all();
    $problems    = adm_s_complaint_problem::all();
    $departments = Department::all();
    $sections    = section::all();

    // تمرير البيانات إلى الفيو
    return view('administrative.call_center.program.complaints.edit', compact(
        'complaint', 'categories', 'problems', 'departments', 'sections', 'selectedCategory'
    ));
}

public function destroy($encryptedId)
{
    try {
        // فك تشفير الـ id
        $id = Crypt::decryptString($encryptedId);
    } catch (Exception $e) {
        // إذا فشل فك التشفير، إعادة التوجيه مع رسالة خطأ
        return redirect()->back()->with('error', 'معرف غير صالح');
    }

    $complaint = adm_b_complaint::findOrFail($id);

    try {
        $complaint->delete();
        return redirect()->route('complaints.index')
            ->with('success', trans('call_center_trans.complaint_deleted'));
    } catch (Exception $e) {
        return redirect()->back()->with('error', trans('call_center_trans.error_deleting_complaint'));
    }
}


}
