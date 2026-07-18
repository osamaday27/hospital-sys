<?php

namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\models\general\nursingstation;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_rooms;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use App\models\general\adm_s_residencies;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class roomscontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rooms = adm_s_rooms::with('floor', 'nursingstation')->get();

            return DataTables::of($rooms)
                ->addColumn('floor', function ($rooms) {
                    return $rooms->floor->name_ar;
                })
                ->addColumn('type', function ($rooms) {
                    $types = [
                        'office' => trans('inpatient_trans.office'),
                        'patient_room' => trans('inpatient_trans.patient_room'),
                        'operation_room' => trans('inpatient_trans.operation_room'),
                        'icu' => trans('inpatient_trans.icu'),
                        'emergency_room' => trans('inpatient_trans.emergency_room'),
                        'delivery_room' => trans('inpatient_trans.delivery_room'),
                        'waiting_room' => trans('inpatient_trans.waiting_room'),
                        'isolation_room' => trans('inpatient_trans.isolation_room'),
                        'laboratory' => trans('inpatient_trans.laboratory'),
                        'xray_room' => trans('inpatient_trans.xray_room'),
                        'pharmacy' => trans('inpatient_trans.pharmacy'),
                        'storage' => trans('inpatient_trans.storage'),
                        'consultation_room' => trans('inpatient_trans.consultation_room'),
                        'recovery_room' => trans('inpatient_trans.recovery_room'),
                        'nurse_station' => trans('inpatient_trans.nurse_station'),
                    ];

                    return $types[$rooms->type] ?? trans('inpatient_trans.unknown');
                })
                ->addColumn('nursingstation', function ($rooms) {
                    return $rooms->nursingstation->name_ar;
                })
                  ->addColumn('residencies', function ($rooms) {
                    return $rooms->residencies->name_ar;
                })
                ->addColumn('status', function ($rooms) {
                    $statuses = [
                        'available' => ['label' => trans('inpatient_trans.available'), 'color' => 'success', 'icon' => 'check-circle'],
                        'occupied' => ['label' => trans('inpatient_trans.occupied'), 'color' => 'danger', 'icon' => 'x-circle'],
                        'under_maintenance' => ['label' => trans('inpatient_trans.under_maintenance'), 'color' => 'warning', 'icon' => 'tools'],
                        'reserved' => ['label' => trans('inpatient_trans.reserved'), 'color' => 'primary', 'icon' => 'bookmark'],
                        'out_of_service' => ['label' => trans('inpatient_trans.out_of_service'), 'color' => 'secondary', 'icon' => 'power-off'],
                        'sanitized' => ['label' => trans('inpatient_trans.sanitized'), 'color' => 'info', 'icon' => 'spray-can'],
                        'closed' => ['label' => trans('inpatient_trans.closed'), 'color' => 'dark', 'icon' => 'lock'],
                        'pending_cleaning' => ['label' => trans('inpatient_trans.pending_cleaning'), 'color' => 'light', 'icon' => 'broom'],
                        'allocated' => ['label' => trans('inpatient_trans.allocated'), 'color' => 'success', 'icon' => 'user-check'],
                        'isolation' => ['label' => trans('inpatient_trans.isolation'), 'color' => 'danger', 'icon' => 'shield-virus'],
                    ];

                    $status = $statuses[$rooms->status] ?? ['label' => trans('status.unknown'), 'color' => 'secondary', 'icon' => 'question-circle'];

                    return '<span class="badge bg-' .
                        $status['color'] .
                        '">
                            <i class=" bx bx-' .
                        $status['icon'] .
                        '"></i> ' .
                        $status['label'] .
                        '
                        </span>';
                })
                ->addColumn('action', function ($rooms) {
                    $encryptedId = Crypt::encryptString($rooms->id);
                    $deleteFunction = 'deleteFunction(' . $rooms->id . ')';

                    return '
                  <a href="' .
                        route('room_settings.edit', ['id' => $encryptedId]) .
                        '" class="btn btn-info btn-sm">
                      <i class="bx bx-edit"></i> ' .
                        trans('buttons.edit') .
                        '
                  </a>
                  <a href="javascript:void(0)" onclick="' .
                        $deleteFunction .
                        '" class="btn btn-danger btn-sm delete-btn">
                      <i class=" bx bx-trash"></i> ' .
                        trans('buttons.delete') .
                        '
                  </a>';
                })
                ->editColumn('created_at', function ($rooms) {
                    return Carbon::parse($rooms->created_at)->format('d/m/Y h:i A');
                })
                ->rawColumns(['action', 'status', 'floor', 'nursingstation','residencies'])
                ->make(true);
        }

        return view('administrative.inpatient.settings.rooms.index');
    }

    public function create()
    {
        $floors = adm_s_floors::all();
        $nursingstation = nursingstation::all();
        $residencies = adm_s_residencies::all();

        return view('administrative.inpatient.settings.rooms.create', compact('floors', 'nursingstation', 'residencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nursing_stations_id' => 'required|exists:adm_s_nursing_stations,id',
            'residency_id' => 'required|exists:adm_s_residencies,id',
         'type' => 'required|in:office,patient_room,operation_room,icu,emergency_room,delivery_room,waiting_room,isolation_room,laboratory,xray_room,pharmacy,storage,consultation_room,recovery_room,nurse_station',
        ]);
        $floor_id = nursingstation::findOrFail($request->nursing_stations_id)->floor_id;
        adm_s_rooms::create($validated + ['user_id' => auth()->id()] + ['floor_id' => $floor_id]);

        return redirect()->route('room_settings.index')->with('success', 'تم إضافة الغرفة بنجاح.');
    }

    public function edit($id)
    {
        $floors = adm_s_floors::all();
        $nursingstation = nursingstation::all();
        // فك تشفير الـ ID
        $idn = Crypt::decryptString($id);
        $residencies = adm_s_residencies::all();

        // الحصول على بيانات المستوى باستخدام الـ ID
        $room = adm_s_rooms::findOrFail($idn);
        return view('administrative.inpatient.settings.rooms.edit', compact('room', 'floors', 'nursingstation','residencies'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nursing_stations_id' => 'required|exists:adm_s_nursing_stations,id',
            'type' => 'required|in:office,patient_room,operation_room,icu,emergency_room,delivery_room,waiting_room,isolation_room,laboratory,xray_room,pharmacy,storage,consultation_room,recovery_room,nurse_station',
        ]);
        $adm_s_rooms = adm_s_rooms::find($request->id);
        $floor_id = nursingstation::findOrFail($request->nursing_stations_id)->floor_id;
        $adm_s_rooms->update($request->all() + ['floor_id' => $floor_id]);
        return redirect()->route('room_settings.index')->with('success', 'تم تحديث المحطة بنجاح.');
    }

    public function destroy($id)
    {
        try {
            $adm_s_rooms = adm_s_rooms::findOrFail($id);

            if ($adm_s_rooms) {
                // إذا لم تكن هناك سجلات مرتبطة
                $adm_s_rooms->delete();
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