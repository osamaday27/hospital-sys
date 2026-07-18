<?php
namespace App\Http\Controllers\finance\pricing_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\finance\servicelevel;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class servicemanagementlevel1cordcontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.pricing_management.services.indexlevel1');
    }
    public function create()
    {
        return view('finance.pricing_contracts_management.pricing_management.services.createlevel1');
    }
    public function store(Request $request)
    {
        // التحقق من وجود القيم مسبقًا
        if (
            servicelevel::where('name->ar', $request->name_ar)
                ->orWhere('name->en', $request->name_en)
                ->exists()
        ) {
            session()->flash('warning_system', trans('service_settings.exists'));
            return redirect()
                ->back()
                ->withErrors([trans('service_settings.exists')]);
        }

        try {
            // التحقق من صحة المدخلات
            $validated = $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'hospital_code' => 'required|string|max:50',
                'level_type' => 'required|string|max:50',
            ]);

            // إنشاء المستوى
            $serviceLevel = new servicelevel();
            $serviceLevel->name = ['en' => $request->name_en, 'ar' => $request->name_ar];
            $serviceLevel->hospital_code = $request->hospital_code;
            $serviceLevel->level_type = $request->level_type;
            $serviceLevel->user_id = Auth::user()->id;
            $serviceLevel->default_branch = 1;
            $serviceLevel->status = 'active';
            $serviceLevel->save();

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
        $level = servicelevel::findOrFail($id);

        // تمرير البيانات إلى الواجهة (view)
        return view('finance.pricing_contracts_management.pricing_management.services.editlevel1', compact('level'));
    }

    public function update(Request $request, $encryptedId)
    {
        // الحصول على بيانات المستوى
        $level = servicelevel::findOrFail($encryptedId);

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'hospital_code' => 'required|string|max:50',
            'level_type' => 'required|string|max:50',
        ]);

        // تحديث البيانات
        $level->update([
            'name' => ['en' => $request->name_en, 'ar' => $request->name_ar],
            'hospital_code' => $request->input('hospital_code'),
            'level_type' => $request->input('level_type'),
            'status' => $request->input('status'),
        ]);
        // إعادة التوجيه إلى الصفحة السابقة مع رسالة نجاح
        return redirect()->route('service_management_level1.index')->with('success', trans('messages.update_success'));
    }
  public function destroy($id)
{
    $servicelevel = ServiceLevel::find($id);

    if ($servicelevel) {
        // التحقق إذا كانت مرتبطة بجدول orders
        if ($servicelevel->servicesublevels()->exists()) {  
            return response()->json([
                'status' => 'success',
                'message' => 'This service level is related to orders and cannot be deleted.'
            ], 302);
        }

        // إذا لم تكن هناك سجلات مرتبطة
        $servicelevel->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'medical record deleted successfully'
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'medical record not found'
    ], 404);
}


    public function getservicelevel1()
    {
        $levels = servicelevel::select(['id', 'name', 'hospital_code', 'level_type', 'created_at', 'status']);

        return DataTables::of($levels)
            ->addColumn('status', function ($level) {
                // عرض الحالة مع التصنيفات
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
            ->editColumn('level_type', function ($level) {
                // ترجمة نوع المستوى
                switch ($level->level_type) {
                    case 'outpatient':
                        return trans('service_settings.outpatient_clinics');
                    case 'laboratory':
                        return trans('service_settings.laboratory');
                    case 'radiology':
                        return trans('service_settings.radiology');
                    case 'emergency':
                        return trans('service_settings.emergency');
                    case 'inpatient':
                        return trans('service_settings.inpatient');
                    case 'operations':
                        return trans('service_settings.operations');
                    case 'marketing':
                        return trans('service_settings.marketing');
                    case 'other':
                        return trans('service_settings.other');
                    default:
                        return trans('service_settings.select_level_type'); // Default if not matched
                }
            })
            ->editColumn('created_at', function ($level) {
                // تنسيق التاريخ
                return Carbon::parse($level->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($level) {
                $encryptedId = Crypt::encryptString($level->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $level->id . ')'; // دالة الحذف

                // إنشاء أزرار الإجراءات
                return '
            <a href="' .
                    route('service_management_level1.edit', ['service_management_level1' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'action', 'created_at', 'name'])

            ->make(true);
    }
}