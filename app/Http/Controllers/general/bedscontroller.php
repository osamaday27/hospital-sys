<?php

namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\models\general\nursingstation;
use App\models\general\adm_s_rooms;
use App\models\general\adm_s_beds;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class bedscontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $beds = adm_s_beds::with('rooms')->get();

            return DataTables::of($beds)
                ->addColumn('room', function ($beds) {
                    return $beds->rooms->name;
                })
                ->addColumn('type', function ($beds) {
                    $types = [
                        'general' => trans('inpatient_trans.general'),
                        'icu' => trans('inpatient_trans.icu'),
                        'nursery' => trans('inpatient_trans.nursery'),
                       
                    ];

                    return $types[$beds->type] ?? trans('inpatient_trans.unknown');
                })
                ->addColumn('status', function ($beds) {
                    $statuses = [
                        'available' => ['label' => trans('inpatient_trans.available'), 'color' => 'success', 'icon' => 'check-circle'],
                        'occupied' => ['label' => trans('inpatient_trans.occupied'), 'color' => 'danger', 'icon' => 'x-circle'],
                        'maintenance' => ['label' => trans('inpatient_trans.under_maintenance'), 'color' => 'warning', 'icon' => 'wrench'],
                        'reserved' => ['label' => trans('inpatient_trans.reserved'), 'color' => 'primary', 'icon' => 'bookmark'],
                        'out_of_service' => ['label' => trans('inpatient_trans.out_of_service'), 'color' => 'secondary', 'icon' => 'power-off'],
                        'sanitized' => ['label' => trans('inpatient_trans.sanitized'), 'color' => 'info', 'icon' => 'spray-can'],
                        'closed' => ['label' => trans('inpatient_trans.closed'), 'color' => 'dark', 'icon' => 'lock'],
                        'pending_cleaning' => ['label' => trans('inpatient_trans.pending_cleaning'), 'color' => 'warning', 'icon' => 'trash'],
                        'allocated' => ['label' => trans('inpatient_trans.allocated'), 'color' => 'success', 'icon' => 'user-check'],
                        'isolation' => ['label' => trans('inpatient_trans.isolation'), 'color' => 'danger', 'icon' => 'spray-can'],
                    ];

                    $status = $statuses[$beds->status] ?? ['label' => trans('status.unknown'), 'color' => 'secondary', 'icon' => 'question-circle'];

                    return '<span style="font-size: inherit;" class="badge bg-' .
                        $status['color'] .
                        '">
                            <i class=" bx bx-' .
                        $status['icon'] .
                        '"></i> ' .
                        $status['label'] .
                        '
                        </span>';
                })
                ->addColumn('action', function ($beds) {
                    $encryptedId = Crypt::encryptString($beds->id);
                    $deleteFunction = 'deleteFunction(' . $beds->id . ')';

                    return '
                  <a href="' .
                        route('bed_settings.edit', ['id' => $encryptedId]) .
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
                ->editColumn('created_at', function ($beds) {
                    return Carbon::parse($beds->created_at)->format('d/m/Y h:i A');
                })
                ->rawColumns(['action', 'status', 'room'])
                ->make(true);
        }

        return view('administrative.inpatient.settings.beds.index');
    }

    public function create()
    {
       $rooms = adm_s_rooms::with(['floor', 'nursingstation'])->get();
        
        return view('administrative.inpatient.settings.beds.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_id' => 'required|exists:adm_s_rooms,id',
            'type' => 'required|in:general,icu,nursery',
        ]);
        adm_s_beds::create($validated + ['user_id' => auth()->id()]);
        return redirect()->route('bed_settings.index')->with('success', 'تم إضافة السرير  بنجاح.');
    }

    public function edit($id)
    {
         $rooms = adm_s_rooms::with(['floor', 'nursingstation'])->get();
        // فك تشفير الـ ID
        $idn = Crypt::decryptString($id);
        // الحصول على بيانات المستوى باستخدام الـ ID
        $bed = adm_s_beds::findOrFail($idn);
        return view('administrative.inpatient.settings.beds.edit', compact('rooms', 'bed'));
    }

    public function update(Request $request)
    {
       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_id' => 'required|exists:adm_s_rooms,id',
            'type' => 'required|in:general,icu,operation_room,icu,nursery',
            'status' => 'required|in:available,occupied,maintenance,reserved,out_of_service,sanitized,closed,pending_cleaning,allocated,isolation',
       ]);
        $adm_s_beds = adm_s_beds::find($request->id);
        $adm_s_beds->update($request->all());
        return redirect()->route('bed_settings.index')->with('success', 'تم تحديث المحطة بنجاح.');
    }

    public function destroy($id)
    {
        try {
            $adm_s_beds = adm_s_beds::findOrFail($id);

            if ($adm_s_beds) {
                // إذا لم تكن هناك سجلات مرتبطة
                $adm_s_beds->delete();
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