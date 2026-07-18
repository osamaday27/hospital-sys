<?php
namespace App\Http\Controllers\administrative\inpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\area;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_buildings;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;

class inpatient_programscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('administrative.inpatient.program.index');
    }

    public function create()
    {
        $buildings = adm_s_buildings::all();
        $specialties = specialties::all();
        return view('administrative.outpatient.program.create', compact('buildings', 'specialties'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'specialty_id' => 'nullable|exists:specialties,id',
            'floor_id' => 'nullable|exists:adm_s_floors,id',
            'clinic_code' => 'nullable|string|unique:adm_s_clinics',
            'logo' => 'nullable',
            'asset_id' => 'nullable|exists:asset_items,id',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $AdmSClinicData = $request->all();

        if ($request->hasFile('logo')) {
            $AdmSClinicData['logo'] = $this->file_storage($request->file('logo'), 'clincl_img');
        }

        admsclinic::create($AdmSClinicData);

        return redirect()->route('outpatient_programs.index')->with('success', trans('general_trans.added_successfully'));
    }

    // عرض تفاصيل عيادة
    public function show(AdmSClinic $admSClinic)
    {
        return view('adm_s_clinics.show', compact('admSClinic'));
    }

    // عرض صفحة تعديل عيادة
    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $admsclinic = admsclinic::findOrFail($id);
        $buildings = adm_s_buildings::all();
        $specialties = specialties::all();
        return view('administrative.outpatient.program.edit', compact('admsclinic', 'specialties', 'buildings'));
    }

    // تحديث عيادة
    public function update(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'specialty_id' => 'nullable|exists:specialties,id',
            'floor_id' => 'nullable|exists:adm_s_floors,id',
            'clinic_code' => 'nullable|string|unique:adm_s_clinics,clinic_code,' . $request->id,
            'logo' => 'nullable',
            //  'service_id' => 'required|exists:fin_services,id',
            // 'status' => 'required|in:active,inactive',
            'asset_id' => 'nullable|exists:asset_items,id',
            'phone_number' => 'nullable|string|max:20',
        ]);
        $admsclinic = admsclinic::findOrFail($request->id);

        // الحصول على جميع البيانات من الطلب
        $admSClinicData = $request->all();

        // إذا تم تحميل صورة جديدة للشعار
        if ($request->hasFile('logo')) {
            // إزالة الصورة القديمة إذا كانت موجودة
            if ($admsclinic->logo) {
                // حذف الصورة القديمة من التخزين إذا كانت موجودة
                $this->delete_old_file($admsclinic->logo);
            }

            // تخزين الصورة الجديدة
            $admSClinicData['logo'] = $this->file_storage($request->file('logo'), 'clincl_img');
        }

        // تحديث البيانات
        $admsclinic->update($admSClinicData);

        return redirect()->route('outpatient_programs.index')->with('success', trans('general_trans.updated_successfully'));
    }

    public function destroy($id)
    {
        try {
            $admsclinic = Admsclinic::findOrFail($id); // تأكد من أن اسم الموديل يبدأ بحرف كبير.

            if ($admsclinic) {
                // التحقق إذا كانت مرتبطة بجدول 'orders' أو أي جدول آخر (يمكنك تغيير هذه الدالة حسب الحاجة)
                /* if ($admsclinic->childLists()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('general_trans.cannot_delete_has_relations'),
                ]);
            } else {*/
                // إذا لم تكن هناك سجلات مرتبطة
                $admsclinic->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => trans('general_trans.deleted_successfully'),
                ]);
                /*  }*/
            }

            return response()->json(
                [
                    'status' => 'error',
                    'message' => trans('general_trans.deleted_failed'),
                ],
                404,
            );
        } catch (\Exception $e) {
            // إرجاع رسالة خطأ في حالة وجود مشكلة في التنفيذ
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getclinics()
    {
        $clinics = admsclinic::with('specialty', 'floor')->get(); // استعلام للحصول على العيادات مع التخصصات والطوابق

        return DataTables::of($clinics)
            ->addColumn('status', function ($clinic) {
                return '<span class="badge bg-' . ($clinic->status == 'active' ? 'success' : 'danger') . '">' . trans('status.' . $clinic->status) . '</span>';
            })
            ->editColumn('created_at', function ($clinic) {
                return Carbon::parse($clinic->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('logo', function ($clinic) {
                // تأكد من أن لديك عمود logo يحتوي على المسار الصحيح للصورة
                $logoUrl = asset($clinic->logo); // تعديل المسار بناءً على مكان تخزين الصورة
                return '<img src="' . $logoUrl . '" alt="' . $clinic->name_en . '" class="img-thumbnail" width="50">';
            })
            ->addColumn('specialty', function ($clinic) {
                // عرض اسم التخصص المرتبط بالعيادة
                return $clinic->specialty ? $clinic->specialty->name_en : '-';
            })
            ->addColumn('floor', function ($clinic) {
                // عرض اسم الدور المرتبط بالعيادة
                return $clinic->floor ? $clinic->floor->name_en : '-';
            })
            ->addColumn('action', function ($clinic) {
                $encryptedId = Crypt::encryptString($clinic->id);
                $deleteFunction = 'deleteFunction(' . $clinic->id . ')';

                return '
            <a href="' .
                    route('outpatient_programs.edit', ['clinic' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
                     <a href="' .
                    route('schedule_programs.index', ['clinic' => $encryptedId]) .
                    '" class="btn btn-info btn-sm">' .
                    trans('outpatient_trans.schedule_programs') .
                    '</a>
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'logo', 'action']) // إضافة عمود 'logo' لتتم معالجته كـ raw
            ->make(true);
    }

    public function getFloors($buildingId)
    {
        $admsclinic = adm_s_floors::where('building_id', $buildingId)->get(['id', 'name_ar', 'name_en']);
        return response()->json($admsclinic);
    }
}