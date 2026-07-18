<?php

namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\models\general\nursingstation;
use App\models\general\adm_s_admission_reasons;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class reasons_x_scontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reasons = adm_s_admission_reasons::where('type','exit')->get();

            return DataTables::of($reasons)

                ->addColumn('status', function ($reasons) {
                    $statuses = [
                        'active' => ['label' => trans('inpatient_trans.active'), 'color' => 'success', 'icon' => 'check-circle'],
                        'inactive' => ['label' => trans('inpatient_trans.inactive'), 'color' => 'danger', 'icon' => 'x-circle'],
                    ];

                    $status = $statuses[$reasons->status] ?? ['label' => trans('status.unknown'), 'color' => 'secondary', 'icon' => 'question-circle'];

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
                ->addColumn('action', function ($reasons) {
                    $encryptedId = Crypt::encryptString($reasons->id);
                    $deleteFunction = 'deleteFunction(' . $reasons->id . ')';

                    return '
                  <a href="' .
                        route('reasons_exit_settings.edit', ['id' => $encryptedId]) .
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
                ->editColumn('created_at', function ($reasons) {
                    return Carbon::parse($reasons->created_at)->format('d/m/Y h:i A');
                })
                ->rawColumns(['action', 'status', 'room'])
                ->make(true);
        }

        return view('administrative.inpatient.settings.reasons_exit.index');
    }

    public function create()
    {

        return view('administrative.inpatient.settings.reasons_exit.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
             'name_ar' => 'required|string|max:255',
             'name_en' => 'required|string|max:255',
        ]);
        adm_s_admission_reasons::create($validated + ['created_by' => auth()->id()]+ ['type' => 'exit']);
        return redirect()->route('reasons_exit_settings.index')->with('success', 'تم إضافة السبب   بنجاح.');
    }

    public function edit($id)
    {
      
        // فك تشفير الـ ID
        $idn = Crypt::decryptString($id);
        // الحصول على بيانات المستوى باستخدام الـ ID
        $reasons_exit = adm_s_admission_reasons::findOrFail($idn);
        return view('administrative.inpatient.settings.reasons_exit.edit', compact('reasons_exit'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
             'name_ar' => 'required|string|max:255',
             'name_en' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        $adm_s_reasons = adm_s_admission_reasons::find($request->id);
        $adm_s_reasons->update($request->all());
        return redirect()->route('reasons_exit_settings.index')->with('success', 'تم تحديث السبب بنجاح.');
    }

    public function destroy($id)
    {
        try {
            $adm_s_reasons = adm_s_admission_reasons::findOrFail($id);

            if ($adm_s_reasons) {
                // إذا لم تكن هناك سجلات مرتبطة
                $adm_s_reasons->delete();
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