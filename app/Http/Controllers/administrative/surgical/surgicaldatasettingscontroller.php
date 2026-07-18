<?php
namespace App\Http\Controllers\administrative\surgical;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\administrative\surgicaldatasetting;
use App\models\finance\service;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
class surgicaldatasettingscontroller extends Controller
{
    /**
     * عرض صفحة الجدول باستخدام DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $services = service::whereHas('sublevel.level1', function ($query) {
                $query->where('level_type', 'operations'); // تصفية الخدمات حسب نوع المستوى
            })->get();

            return DataTables::of($services)
                ->addColumn('service_name', function ($row) {
                    return e($row->name); // تأمين الإخراج لمنع XSS
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = e(Crypt::encryptString($row->id)); // تأمين التشفير
                    $deleteFunction = 'deleteFunction(' . e($row->id) . ')'; // تأمين الإدخال لمنع الأخطاء

                    return '
                <a href="' .
                        e(route('surgicaldatasettings.create', ['id' => $encryptedId])) .
                        '"
                   class="btn btn-warning btn-sm">' .
                        trans('buttons.surgicaldatasettings') .
                        '</a>';
                })
                ->rawColumns(['action']) // تصحيح الاسم
                ->make(true);
        }

        return view('administrative.surgical.settings.surgicaldatasettings.index');
    }

    /**
     * عرض نموذج إنشاء جديد.
     */
    public function create($surgicaldatasettings)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($surgicaldatasettings);

        // جلب بيانات الخدمة
        $service = Service::findOrFail($id);

        // البحث عن السجل، وإن لم يوجد إرجاع كائن فارغ
        $surgicaldatasetting = SurgicalDatasetting::where('service_id', $id)->first() ?? new SurgicalDatasetting();

        // إرسال البيانات إلى الفيو
        return view('administrative.surgical.settings.surgicaldatasettings.create', compact('surgicaldatasetting', 'service'));
    }

    /**
     * حفظ البيانات الجديدة.
     */
    public function store(Request $request)
    {
        // التحقق من المدخلات
        $request->validate([
            'service_id' => 'required|exists:fin_services,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'possible_complications_ar' => 'nullable|string',
            'possible_complications_en' => 'nullable|string',
            'preparation_instructions_ar' => 'nullable|string',
            'preparation_instructions_en' => 'nullable|string',
            'category' => 'required|in:minor,major,advanced',
            'technique' => 'required|in:open,laparoscopic,robotic,other',
            'expected_duration_hours' => 'nullable|numeric|min:0',
            'expected_duration_minutes' => 'nullable|numeric|min:0|max:59',
            'complexity' => 'required|in:simple,moderate,complex',
            'surgical_method' => 'nullable|in:open,laparoscopic,robotic',
            'anesthesia_type' => 'nullable|in:general,local,regional,other',
            'ventilation_method' => 'nullable|in:mask,laryngeal_tube,spontaneous',
            'anesthesia_duration_hours' => 'nullable|numeric|min:0',
            'anesthesia_duration_minutes' => 'nullable|numeric|min:0|max:59',
            'blood_compatibility_test' => 'nullable|boolean',
            'pre_op_tests' => 'nullable|string',
            'pre_op_medications' => 'nullable|string',
            'surgical_tools' => 'nullable|string',
            'anesthesia_medications' => 'nullable|string',
        ]);

        // حساب مدة التخدير بالدقائق
        $anesthesia_duration = $request->input('anesthesia_duration_hours', 0) * 60 + $request->input('anesthesia_duration_minutes', 0);

        // حساب مدة العملية بالدقائق
        $totalMinutes = $request->input('expected_duration_hours', 0) * 60 + $request->input('expected_duration_minutes', 0);

        // التأكد من أن المدة لا تتجاوز 24 ساعة
        if ($totalMinutes >= 1440) {
            return back()->with('error', 'Expected duration cannot exceed 24 hours.');
        }

        // تحويل المدة إلى تنسيق HH:MM:SS
        $expectedDuration = sprintf('%02d:%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60, 0);
        $anesthesiaDurationFormatted = sprintf('%02d:%02d:%02d', floor($anesthesia_duration / 60), $anesthesia_duration % 60, 0);

        // البحث عن السجل أو إنشائه إذا لم يكن موجودًا
        $surgicalDatasetting = surgicaldatasetting::updateOrCreate(
            ['service_id' => $request->input('service_id')], // البحث باستخدام service_id
            [
                'name_ar' => $request->input('name_ar'),
                'name_en' => $request->input('name_en'),
                'description_ar' => $request->input('description_ar'),
                'description_en' => $request->input('description_en'),
                'possible_complications_ar' => $request->input('possible_complications_ar'),
                'possible_complications_en' => $request->input('possible_complications_en'),
                'preparation_instructions_ar' => $request->input('preparation_instructions_ar'),
                'preparation_instructions_en' => $request->input('preparation_instructions_en'),
                'category' => $request->input('category'),
                'technique' => $request->input('technique'),
                'expected_duration' => $expectedDuration,
                'complexity' => $request->input('complexity'),
                'surgical_method' => $request->input('surgical_method'),
                'anesthesia_type' => $request->input('anesthesia_type'),
                'ventilation_method' => $request->input('ventilation_method'),
                'anesthesia_duration' => $anesthesiaDurationFormatted, // حفظ مدة التخدير
                'blood_compatibility_test' => $request->input('blood_compatibility_test', false),
                'pre_op_tests' => $request->input('pre_op_tests'),
                'pre_op_medications' => $request->input('pre_op_medications'),
                'surgical_tools' => $request->input('surgical_tools'),
                'anesthesia_medications' => $request->input('anesthesia_medications'),
            ],
        );

        // العودة مع رسالة نجاح
        return redirect()->route('surgicaldatasettings.index')->with('success', trans('surgical_trans.setting_saved'));
    }

    /**
     * عرض نموذج التعديل.
     */
    public function edit($id)
    {
        $surgicalData = surgicaldatasetting::findOrFail($id);
        return view('administrative.surgicaldatasettings.edit', compact('surgicalData'));
    }

    /**
     * تحديث البيانات.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'service_id' => 'required|exists:fin_services,id',
        ]);

        $surgicalData = surgicaldatasetting::findOrFail($id);
        $surgicalData->update($request->all());

        return redirect()->route('surgicaldatasettings.index')->with('success', 'تم تحديث العملية الجراحية بنجاح.');
    }

    /**
     * حذف السجل.
     */
    public function destroy($id)
    {
        surgicaldatasetting::findOrFail($id)->delete();
        return redirect()->route('surgicaldatasettings.index')->with('success', 'تم حذف العملية الجراحية بنجاح.');
    }
    public function copy_surgical_tools()
    {
          return view('administrative.surgical.settings.surgicaldatasettings.copy_surgical_tools');
    }
}