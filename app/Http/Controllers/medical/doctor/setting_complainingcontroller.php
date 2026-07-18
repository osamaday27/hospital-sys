<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\specialties;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\models\medical\ms_medical_complaints;

class setting_complainingcontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $locale = app()->getLocale(); // تحديد اللغة الحالية
            $data = ms_medical_complaints::with('specialty')->get();

            return DataTables::of($data)
                ->addColumn('name', function ($data) use ($locale) {
                    return $data->getTranslation('name', $locale); // جلب الاسم بالترجمة
                })
                ->addColumn('specialties', function ($data) use ($locale) {
                    return app()->getLocale() == 'ar' ? $data->specialty->name_ar : $data->specialty->name_en;
                })
                ->addColumn('status', function ($data) {
                    // تحديد الحالة باللغة العربية
                    $status = $data->status;
                    switch ($status) {
                        case 'active':
                            return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                        case 'inactive':
                            return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                        default:
                            return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                    }
                })
                ->addColumn('action', function ($data) {
                    $encryptedId = Crypt::encryptString($data->id);
                    $deleteFunction = 'deleteFunction(' . $data->id . ')';
                    return '
                    <a href="' .
                        route('setting_complaining.edit', ['id' => $encryptedId]) .
                        '"
                        class="btn btn-info btn-sm">' .
                        trans('buttons.edit') .
                        '</a>
                    <a href="javascript:void(0)" onclick="' .
                        $deleteFunction .
                        '"
                        class="btn btn-danger btn-sm delete-btn">' .
                        trans('buttons.delete') .
                        '</a>';
                })
                ->editColumn('created_at', function ($data) {
                    return Carbon::parse($data->created_at)->format('d/m/Y h:i A');
                })
                ->rawColumns(['action', 'specialties', 'status'])
                ->make(true);
        }

        return view('medical.doctor.settings.complaining.index');
    }

    public function create()
    {
        $specialties = specialties::all();
        return view('medical.doctor.settings.complaining.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'severity' => 'nullable|string',
            'specialization' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // حفظ البيانات في جدول m_s_medical_complaints
        $complaint = new ms_medical_complaints();
        $complaint->setTranslations('name', ['ar' => $validated['name_ar'], 'en' => $validated['name_en']]);
        $complaint->code = $validated['code'] ?? null;
        $complaint->description = $validated['description'] ?? null;
        $complaint->type = $validated['type'] ?? null;
        $complaint->severity = $validated['severity'] ?? null;
        $complaint->specialization = $validated['specialization'] ?? null;
        $complaint->status ='active' ?? null;
        $complaint->user_id = auth()->id(); // حفظ ID المستخدم الذي أضاف البيانات
        $complaint->save();
        return redirect()->route('setting_complaining.index')->with('success', 'تمت إضافة الشكوى الطبية بنجاح.');
        
    }

    public function edit($id)
    {
        $specialties = specialties::all();
        // فك تشفير الـ ID
        $idn = Crypt::decryptString($id);

        // الحصول على بيانات المستوى باستخدام الـ ID
        $complaints = ms_medical_complaints::findOrFail($idn);
        return view('medical.doctor.settings.complaining.edit', compact('complaints', 'specialties'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'severity' => 'nullable|string',
            'specialization' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        // البحث عن السجل المراد تحديثه
        $complaint = ms_medical_complaints::findOrFail($request->id);
        // تحديث القيم
        $complaint->setTranslations('name', ['ar' => $validated['name_ar'], 'en' => $validated['name_en']]);
        $complaint->code = $validated['code'] ?? null;
        $complaint->description = $validated['description'] ?? null;
        $complaint->type = $validated['type'] ?? null;
        $complaint->severity = $validated['severity'] ?? null;
        $complaint->specialization = $validated['specialization'] ?? null;
        $complaint->status ='active' ?? null;
        $complaint->save();
        return redirect()->route('setting_complaining.index')->with('success', 'تم تعديل الشكوى الطبية بنجاح.');
    }

    public function destroy($id)
    {
        try {
            $ms_medical_complaints = ms_medical_complaints::findOrFail($id);

            if ($ms_medical_complaints) {
                // إذا لم تكن هناك سجلات مرتبطة
                $ms_medical_complaints->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => trans('general_trans.deleted_successfully'),
                ]);
            }

            return response()->json(
                [
                    'status' => 'error',
                    'message' => trans('general_trans.deleted_failed'),
                ],
                404,
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
 public function autocomplete(Request $request)
{
    $query = $request->get('query');
    $locale = app()->getLocale();

    $categories = ms_medical_complaints::where("name->{$locale}", 'like', "%{$query}%")
        ->get()
        ->map(function ($item) use ($locale) {
            return $item->getTranslation('name', $locale);
        });

    return response()->json($categories);
}


}