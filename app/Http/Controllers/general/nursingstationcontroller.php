<?php

namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\models\general\nursingstation;
use App\models\general\adm_s_floors;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class nursingstationcontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stations = NursingStation::with('floor', 'manager')->get();
            return DataTables::of($stations)
                ->addcolumn('floor', function ($stations) {
                    // إرجاع اسم المبنى
                    return $stations->floor->name_ar;
                })
                ->addcolumn('manager', function ($stations) {
                    // إرجاع اسم المبنى
                    return $stations->manager->full_name;
                })
                ->addColumn('action', function ($stations) {
                    $encryptedId = Crypt::encryptString($stations->id); // تشفير الـ ID
                    $deleteFunction = 'deleteFunction(' . $stations->id . ')'; // تصحيح الاستدعاء

                    // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                    return '
                  <a href="' .
                        route('nursing_station_settings.edit', ['id' => $encryptedId]) .
                        '" class="btn btn-info btn-sm">' .
                        trans('buttons.edit') .
                        '</a>
        
            <a href="javascript:void(0)" onclick="' .
                        $deleteFunction .
                        '" class="btn btn-danger btn-sm delete-btn">' .
                        trans('buttons.delete') .
                        '</a>';
                })
                ->editColumn('created_at', function ($stations) {
                    // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                    return Carbon::parse($stations->created_at)->format('d/m/Y h:i A');
                })
                ->rawColumns(['action', 'floor', 'manager'])
                ->make(true);
        }

        return view('administrative.inpatient.settings.nursing_stations.index');
    }

    public function create()
    {
        $floors = adm_s_floors::all();
        $managers = Employee::all();
        return view('administrative.inpatient.settings.nursing_stations.create', compact('floors', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'floor_id' => 'required|exists:adm_s_floors,id',
            'room_count' => 'required|integer|min:1',
            'manager_id' => 'nullable|exists:employees,id',
            'type' => 'required|in:general,specialized,emergency',
            'status' => 'required|in:active,inactive,under_maintenance',
            'notes' => 'nullable|string',
        ]);

        NursingStation::create($validated + ['created_by' => auth()->id()]);

        return redirect()->route('nursing_station_settings.index')->with('success', 'تم إضافة المحطة بنجاح.');
    }

    public function edit($id)
    {
        $floors = adm_s_floors::all();
        $managers = Employee::all();
        // فك تشفير الـ ID
        $idn = Crypt::decryptString($id);

        // الحصول على بيانات المستوى باستخدام الـ ID
        $nursingStation = NursingStation::findOrFail($idn);
        return view('administrative.inpatient.settings.nursing_stations.edit', compact('nursingStation', 'floors', 'managers'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'floor_id' => 'required|exists:adm_s_floors,id',
            'room_count' => 'required|integer|min:1',
            'manager_id' => 'nullable|exists:employees,id',
            'type' => 'required|in:general,specialized,emergency',
            'status' => 'required|in:active,inactive,under_maintenance',
            'notes' => 'nullable|string',
        ]);
        $nursingStation = NursingStation::find($request->id);

        $nursingStation->update($request->all());

        return redirect()->route('nursing_station_settings.index')->with('success', 'تم تحديث المحطة بنجاح.');
    }

    public function destroy($id)
    {
        try {
            $NursingStation = NursingStation::findOrFail($id);

            if ($NursingStation) {
                // إذا لم تكن هناك سجلات مرتبطة
                $NursingStation->delete();
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
}