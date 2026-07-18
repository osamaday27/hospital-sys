<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_buildings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class floors_settingscontroller extends Controller
{
    // Display a listing of the resource
    public function index(Request $request)
    {
        // فك تشفير المعرف
        $buildingId = decrypt($request->get('building_id'));

        // استرجاع إعدادات الطوابق بناءً على المعرف
        $buildingSettings = adm_s_buildings::find($buildingId);
        return view('general.floors.index', compact('buildingSettings'));
    }

    // Show the form for creating a new resource
    public function create($id)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($id);
        // الحصول على بيانات المستوى باستخدام الـ ID
        $building_settings = adm_s_buildings::findOrFail($id);
        return view('general.floors.create', compact('id', 'building_settings'));
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'rooms_count' => 'required|integer|min:1',
            'building_id' => 'required|integer',
            'type' => 'required|string|in:administrative,medical,all',
        ]);

        adm_s_floors::create($request->all());
        return redirect()->back()->with('success', trans('floors_trans.added_successfully'));
    }

    // Display the specified resource
    public function show(adm_s_floors $building)
    {
        return view('general.floors.show', compact('building'));
    }
    public function edit($encryptedId)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($encryptedId);

        // الحصول على بيانات المستوى باستخدام الـ ID
        $floors_settings = adm_s_floors::findOrFail($id);
        // تمرير البيانات إلى الواجهة (view)
        return view('general.floors.edit', compact('floors_settings'));
    }

    // Update the specified resource in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'rooms_count' => 'required|integer|min:1',
        ]);
        // التحقق من وجود السجل
        $adm_s_floors = adm_s_floors::findOrFail($request->id); // البحث عن السجل باستخدام ID

        $adm_s_floors->update($request->all());

        return redirect()->back()->with('success', 'adm_s_floors updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $floors = adm_s_floors::findOrFail($id);

            if ($floors) {
                // التحقق إذا كانت مرتبطة بجدول orders
                if ($floors->parentList()->exists()) {
                    return response()->json(
                        [
                            'status' => 'success',
                            'message' => trans('general_trans.cannot_delete_has_relations'),
                        ],
                    );
                } else {
                    // إذا لم تكن هناك سجلات مرتبطة
                    $floors->delete();
                    return response()->json([
                        'status' => 'success',
                        'message' => trans('general_trans.deleted_successfully'),
                    ]);
                }
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

    public function getfloors_settings(Request $request)
    {
        $buildingId = $request->get('building_id'); // الحصول على id المبنى
        $floors_settings = adm_s_floors::where('building_id', $buildingId)->with('building')->get(); // البحث عن الطوابق بناءً على المبنى

        return DataTables::of($floors_settings)
            ->addColumn('status', function ($floors_settings) {
                // تحديد الحالة باللغة العربية
                $status = $floors_settings->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    case 'temporarily_suspended':
                        return '<span class="badge bg-danger">' . trans('status.temporarily_suspended') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->addcolumn('building', function ($floors_settings) {
                // إرجاع اسم المبنى
                return $floors_settings->building->name_ar;
            })
            ->editColumn('created_at', function ($floors_settings) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($floors_settings->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($floors_settings) {
                $encryptedId = Crypt::encryptString($floors_settings->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $floors_settings->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
                  <a href="' .
                    route('floors_settings.edit', ['floors_settings' => $encryptedId]) .
                    '" class="btn btn-info btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            <a href="' .
                    route('floors_settings.edit', ['floors_settings' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'action', 'building'])
            ->make(true);
    }
}