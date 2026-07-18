<?php

namespace App\Http\Controllers\administrative\surgical;
use App\Http\Controllers\Controller;
use App\models\general\nursingstation;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_rooms_surgical;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use App\models\general\adm_s_residencies;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class roomsurgicalsettingscontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rooms = adm_s_rooms_surgical::with('floors')->get();

            return DataTables::of($rooms)
                ->addColumn('floor', function ($room) {
                    return optional($room->floors)->name_ar ?? trans('surgical_trans.unknown');
                })
                ->addColumn('name', function ($room) {
                    return $room->name_ar ?? trans('surgical_trans.unknown');
                })
                ->addColumn('type', function ($room) {
                    return trans('surgical_trans.operation_room');
                })
                ->addColumn('status', function ($room) {
                    $statuses = [
                        'available' => ['label' => trans('surgical_trans.available'), 'color' => 'success', 'icon' => 'check-circle'],
                        'occupied' => ['label' => trans('surgical_trans.occupied'), 'color' => 'danger', 'icon' => 'x-circle'],
                        'under_maintenance' => ['label' => trans('surgical_trans.under_maintenance'), 'color' => 'warning', 'icon' => 'tools'],
                    ];

                    $status = $statuses[$room->status] ?? ['label' => trans('status.unknown'), 'color' => 'secondary', 'icon' => 'question-circle'];

                    return '<span class="badge bg-' . $status['color'] . '"><i class="bx bx-' . $status['icon'] . '"></i> ' . $status['label'] . '</span>';
                })
                ->addColumn('action', function ($room) {
                    $encryptedId = Crypt::encryptString($room->id);
                    return '<a href="' .
                        route('scheduling_operation.index', ['id' => $encryptedId]) .
                        '" class="btn btn-info btn-sm">
                            <i class="bx bx-edit"></i> ' .
                        trans('buttons.scheduling') .
                        '
                     ';
                })
                ->editColumn('created_at', function ($room) {
                    return optional($room->created_at)->format('d/m/Y h:i A');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('administrative.surgical.settings.scheduling.index');
    }

    public function create()
    {
        $floors = adm_s_floors::all();
        return view('administrative.surgical.settings.rooms.create', compact('floors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'floor' => 'required|exists:adm_s_floors,id',
        ]);

        $floor = adm_s_floors::findOrFail($request->floor);
        $building_id = $floor->building_id;

        adm_s_rooms_surgical::create(
            array_merge($validated, [
                'user_id' => auth()->id(),
                'type' => 'operating',
                'building' => $building_id,
            ]),
        );

        return redirect()->route('roomsurgicalsettings.index')->with('success', 'تم إضافة الغرفة بنجاح.');
    }

    public function edit($id)
    {
        $floors = adm_s_floors::all();
      
        // فك تشفير الـ ID
        $idn = Crypt::decryptString($id);
        // الحصول على بيانات المستوى باستخدام الـ ID
        $room = adm_s_rooms_surgical::findOrFail($idn);
        return view('administrative.surgical.settings.rooms.edit', compact('room', 'floors'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'floor' => 'required|exists:adm_s_floors,id',
            'status' => 'required|in:available,occupied,under_maintenance', // تحديد القيم المسموح بها لحالة الغرفة
        ]);

        // العثور على الغرفة بناءً على الـ ID
        $room = adm_s_rooms_surgical::findOrFail($request->id);

        // العثور على الـ floor بناءً على الـ ID المرسل
        $floor = adm_s_floors::findOrFail($request->floor);

        // استخراج الـ building_id من الـ floor
        $building_id = $floor->building_id;

        // تحديث الغرفة
        $room->update([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'floor' => $validated['floor'],
            'status' => $validated['status'],
            'user_id' => auth()->id(),
            'type' => 'operating', // يمكن تغييره حسب الحاجة
            'building' => $building_id,
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('roomsurgicalsettings.index')->with('success', 'تم تعديل الغرفة بنجاح.');
    }

    public function destroy($id)
    {
        try {
            $adm_s_rooms_surgical = adm_s_rooms_surgical::findOrFail($id);

            if ($adm_s_rooms_surgical) {
                // إذا لم تكن هناك سجلات مرتبطة
                $adm_s_rooms_surgical->delete();
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