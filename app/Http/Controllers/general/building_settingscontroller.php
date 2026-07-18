<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\models\general\adm_s_buildings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class building_settingscontroller extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $buildings = adm_s_buildings::all();
        return view('general.buildings.index', compact('buildings'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        return view('general.buildings.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'floors_count' => 'required|integer|min:1',
        ]);

        adm_s_buildings::create($request->all());

        return redirect()->route('building_settings.index')->with('success', 'adm_s_buildings created successfully.');
    }

    // Display the specified resource
    public function show(adm_s_buildings $building)
    {
        return view('general.buildings.show', compact('building'));
    }
    public function edit($encryptedId)
    {
        // فك تشفير الـ ID
        $id = Crypt::decryptString($encryptedId);

        // الحصول على بيانات المستوى باستخدام الـ ID
        $building_settings = adm_s_buildings::findOrFail($id);
        // تمرير البيانات إلى الواجهة (view)
        return view('general.buildings.edit', compact('building_settings'));
    }

    // Update the specified resource in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'floors_count' => 'required|integer|min:1',
        ]);
        // التحقق من وجود السجل
        $adm_s_buildings = adm_s_buildings::findOrFail($request->id); // البحث عن السجل باستخدام ID

        $adm_s_buildings->update($request->all());

        return redirect()->route('building_settings.index')->with('success', 'adm_s_buildings updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $building = adm_s_buildings::findOrFail($id);
            $building->delete();
            return response()->json(['success' => 'Building deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getbuilding_settings()
    {
        $building_settings = adm_s_buildings::get();

        return DataTables::of($building_settings)
            ->addColumn('status', function ($building_settings) {
                // تحديد الحالة باللغة العربية
                $status = $building_settings->status;
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
            ->editColumn('created_at', function ($building_settings) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($building_settings->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($building_settings) {
                $encryptedId = Crypt::encryptString($building_settings->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $building_settings->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
                  <a href="' .
                    route('floors_settings.create', ['id' => $encryptedId]) .
                    '" class="btn btn-info btn-sm">' .
                    trans('buttons.create_floor') .
                    '</a>
            <a href="' .
                    route('building_settings.edit', ['building_settings' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}