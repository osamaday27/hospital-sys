<?php
namespace App\Http\Controllers\finance\pricing_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\finance\servicesublevel;
use App\models\finance\service;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class servicemanagementlevel3cordcontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.pricing_management.services.indexlevel3');
    }
    public function create()
    {
        // استرجاع المستويات الأولى التي حالتها نشطة
        $level2s = servicesublevel::where('status', 'active')->get();
        // تمرير البيانات إلى الفيو
        return view('finance.pricing_contracts_management.pricing_management.services.createlevel3', compact('level2s'));
    }
    // في Controller الخاص بـ service_management_level3

    public function store(Request $request)
    {
        try {
            // التحقق من وجود القيم مسبقًا
            if (
                service::where('name->ar', $request->name_ar)
                    ->Where('name->en', $request->name_en)
                    ->where('sublevel_id', $request->level2_id)
                    ->exists()
            ) {
                session()->flash('warning_system', trans('service_settings.exists'));
                return redirect()
                    ->back()
                    ->withErrors([trans('service_settings.exists')]);
            }

            // التحقق من صحة البيانات
            $request->validate([
                'level2_id' => 'required|exists:fin_service_sublevels,id',
                'hospital_code' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'age_restriction_from' => 'nullable|integer',
                'age_restriction_to' => 'nullable|integer',
                'repeat_duration' => 'nullable|string|max:255',
                'confidentiality' => 'in:high,normal,low',
                'patient_type.*' => 'in:male,female,both',
            ]);

            // إنشاء سجل جديد في قاعدة البيانات
            $level3 = new service(); // تأكد من وجود موديل Level3

            // تعبئة البيانات
            $level3->sublevel_id = $request->level2_id;
            $level3->hospital_code = $request->hospital_code;
            $level3->name = ['en' => $request->name_en, 'ar' => $request->name_ar];
            $level3->description = ['en' => $request->description_en, 'ar' => $request->description_ar];
            // إضافة الحقول الجديدة
            $level3->contract_service_name_ar = $request->contract_service_name_ar;
            $level3->contract_service_name_en = $request->contract_service_name_en;
            $level3->age_restriction_from = $request->age_restriction_from;
            $level3->age_restriction_to = $request->age_restriction_to;
            $level3->repeat_duration = $request->repeat_duration;
            $level3->confidentiality = $request->confidentiality;
            $level3->patient_type = $request->patient_type;
            $level3->user_id = Auth::user()->id;
            $level3->default_branch = 1;
            $level3->status = 'active';
            // حفظ السجل
            $level3->save();

            // رسالة النجاح
            session()->flash('add', trans('service_settings.level_created_success'));
            return redirect()->back();
        } catch (\Exception $e) {
            // رسالة الخطأ
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($encryptedId)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($encryptedId);

        // الحصول على بيانات المستوى باستخدام الـ ID
        $level3 = service::findOrFail($id);
        $level2s = servicesublevel::where('status', 'active')->get(); // المستويات الأولى
        // تمرير البيانات إلى الواجهة (view)
        return view('finance.pricing_contracts_management.pricing_management.services.editlevel3', compact('level2s', 'level3'));
    }

    public function update(Request $request, $id)
    {
        try {
            // التحقق من وجود السجل
            $level3 = service::findOrFail($id); // البحث عن السجل باستخدام ID

            // التحقق من وجود القيم مسبقًا باستثناء السجل الحالي
            if (
                service::where('name->ar', $request->name_ar)
                    ->where('name->en', $request->name_en)
                    ->where('sublevel_id', $request->level2_id)
                    ->where('id', '!=', $level3->id) // استثناء السجل الحالي
                    ->exists()
            ) {
                session()->flash('warning_system', trans('service_settings.exists'));
                return redirect()
                    ->back()
                    ->withErrors([trans('service_settings.exists')]);
            }

            // التحقق من صحة البيانات
            $request->validate([
                'level2_id' => 'required|exists:fin_service_sublevels,id',
                'hospital_code' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'age_restriction_from' => 'nullable|integer',
                'age_restriction_to' => 'nullable|integer',
                'repeat_duration' => 'nullable|string|max:255',
                'confidentiality' => 'in:high,normal,low',
                'patient_type.*' => 'in:male,female,both',
            ]);

            // تحديث السجل
            $level3->sublevel_id = $request->level2_id;
            $level3->hospital_code = $request->hospital_code;
            $level3->name = ['en' => $request->name_en, 'ar' => $request->name_ar];
            $level3->description = ['en' => $request->description_en, 'ar' => $request->description_ar];
            $level3->age_restriction_from = $request->age_restriction_from;
            // إضافة الحقول الجديدة
            $level3->contract_service_name_ar = $request->contract_service_name_ar;
            $level3->contract_service_name_en = $request->contract_service_name_en;
            $level3->age_restriction_to = $request->age_restriction_to;
            $level3->repeat_duration = $request->repeat_duration;
            $level3->confidentiality = $request->confidentiality;
            $level3->patient_type = $request->patient_type;
            $level3->user_id = Auth::user()->id;
            $level3->default_branch = 1;
            $level3->status = 'active';

            // حفظ التعديلات
            $level3->save();

            // رسالة النجاح
            session()->flash('update', trans('service_settings.level_updated_success'));
            return redirect()->route('service_management_level3.index');
        } catch (\Exception $e) {
            // رسالة الخطأ
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $servicelevel = service::find($id);

        if ($servicelevel) {
            $servicelevel->delete();
            return response()->json(['success' => 'medical record deleted successfully']);
        }

        return response()->json(['error' => 'medical record not found'], 404);
    }
    public function getservicelevel3()
    {
        // الاستعلام لجلب بيانات المستوى الأول والثاني والثالث
        $levels = service::with(['sublevel.level1']) // تحميل المستوى الثاني (servicesublevel) والمستوى الأول (servicelevel)
            ->select(['id', 'name', 'hospital_code', 'description', 'patient_type', 'age_restriction_from', 'age_restriction_to', 'repeat_duration', 'confidentiality', 'sublevel_id', 'default_branch', 'user_id', 'status'])
            ->get();

        return DataTables::of($levels)
            ->addColumn('status', function ($level) {
                $status = $level->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('name', function ($level) {
                return $level->getTranslation('name', app()->getLocale());
            })
            ->editColumn('description', function ($level) {
                return $level->getTranslation('description', app()->getLocale());
            })
            ->editColumn('level_type', function ($level) {
                // ترجمة نوع المستوى
                $sublevel = $level->sublevel;
                if ($sublevel && $sublevel->level1) {
                    // عرض اسم المستوى الأول واسم المستوى الثاني
                    $level1Name = $sublevel->level1->getTranslation('name', app()->getLocale());
                    $sublevelName = $sublevel->getTranslation('name', app()->getLocale());
                    return $level1Name . ' - ' . $sublevelName;
                }
                return trans('service_settings.select_level_type');
            })
            ->editColumn('patient_type', function ($level) {
                // ترجمة نوع المريض
                switch ($level->patient_type) {
                    case 'male':
                        return '<span class="badge bg-success">' . trans('service_settings.male') . '</span>';
                    case 'female':
                        return '<span class="badge bg-warning">' . trans('service_settings.female') . '</span>';
                    case 'both':
                        return '<span class="badge bg-secondary">' . trans('service_settings.both') . '</span>';
                    default:
                        return '-';
                }
            })

            ->editColumn('confidentiality', function ($level) {
                // ترجمة مستوى السرية
                switch ($level->confidentiality) {
                    case 'high':
                        return '<span class="badge bg-danger">' . trans('service_settings.high') . '</span>'; // افترض أن هناك ملف ترجمة 'confidentiality' يحتوي على هذه القيم
                    case 'normal':
                        return '<span class="badge bg-warning">' . trans('service_settings.normal') . '</span>';
                    case 'low':
                        return '<span class="badge bg-secondary">' . trans('service_settings.low') . '</span>';
                    default:
                        return '-';
                }
            })
            ->editColumn('created_at', function ($level) {
                return Carbon::parse($level->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($level) {
                $encryptedId = Crypt::encryptString($level->id);
                $deleteFunction = 'deleteFunction(' . $level->id . ')';

                return '
            <a href="' .
                    route('service_management_level3.edit', ['service_management_level3' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
                    
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'action', 'created_at', 'name', 'description', 'patient_type', 'confidentiality'])
            ->make(true);
    }
}