<?php

namespace App\Http\Controllers\medical\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\administrative\medical_record;
use App\models\finance\service;
use App\models\medical\request_medical_examination;
use App\models\general\specialties;

class request_medical_examinationcontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tests = request_medical_examination::with('User')->orderBy('created_at', 'desc');
            return DataTables::of($tests)
                ->editColumn('service_id', function ($row) {
                    return $row->service->name ?? 'N/A'; // استخدم الحقل الصحيح لاسم الخدمة
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
        }
        return view('request_medical_examination.index');
    }

    // حفظ طلب العملية الجراحية
    public function store(Request $request)
    {
        $request->validate([
            'id_patient' => 'required|exists:patient,id',
            'id_visit' => 'required|string|max:255',
            'service_id' => 'nullable|string',
            'eye' => 'nullable|string',
            'preliminary_diagnosis' => 'nullable|string',
            'clinical_info' => 'nullable|string',
            'test_type' => 'nullable|string',
        ]);

        request_medical_examination::create([
            'patient_id' => $request->id_patient,
            'visit_id' => $request->id_visit,
            'service_id' => $request->service_id,
            'eye' => $request->eye,
            'preliminary_diagnosis' => $request->preliminary_diagnosis,
            'clinical_info' => $request->clinical_info,
            'test_type' => $request->test_type,
            'requested_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'تم انشاء الطلب بنجاح']);
    }
    public function getServices()
    {
        $locale = app()->getLocale(); // تحديد اللغة الحالية

        $services = Service::whereHas('sublevel.level1', function ($query) {
            $query->where('level_type', 'outpatient'); // تصفية الخدمات حسب نوع المستوى
        })->get(['id', 'name']); // جلب البيانات

        // تحويل الاسم إلى اللغة المطلوبة
        $services = $services->map(function ($service) use ($locale) {
            return [
                'id' => $service->id,
                'name' => $service->getTranslation('name', $locale), // استخراج الاسم حسب اللغة
            ];
        });

        return response()->json($services);
    }
    public function getspecialty()
    {
        $locale = app()->getLocale();
        $specialties = specialties::select('id', 'name_' . $locale . ' as name')->get();
        return response()->json($specialties);
    }
}