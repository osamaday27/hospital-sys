<?php

namespace App\Http\Controllers\administrative\call_center;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\administrative\adm_s_questionnaires;
use App\Models\administrative\adm_b_questionnaire;


class questionnairecontroller extends Controller
{
    public function index(Request $request)
    {


    if ($request->ajax()) {
        $questionnaires = adm_s_questionnaires::select('adm_s_questionnaires.*');
        return DataTables::of($questionnaires)
            ->addColumn('type', function ($row) {
                return $row->type ;
            })
            ->addColumn('status', function ($row) {
                return $row->status ;
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('questionnaires.edit', $row->id) . '" class="btn btn-info btn-sm">
                         <i class="fas fa-edit"></i> ' . trans('buttons.edit') . '
                    </a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i> ' . trans('buttons.delete') . '
                    </button>
                    <a href="' . route('questions.index', ['type_id' => $row->id]) . '" class="btn btn-light btn-sm">
                         <i class="fas fa-trash"></i> ' . trans('call_center_trans.questions') . '
                    </a>

                ';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

        return view('administrative/call_center/settings/questionnaires.index');
    }

    public function create()
    {
        return view('administrative/call_center/settings/questionnaires.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|in:questionnaire,evaluation',
            'status' => 'required|boolean',
        ]);

        adm_s_questionnaires::create($request->all());

        return redirect()->route('questionnaires.index')->with('success', 'تمت إضافة الاستبيان بنجاح.');
    }

    public function edit(adm_s_questionnaires $questionnaire)
    {
        return view('administrative/call_center/settings/questionnaires.edit', compact('questionnaire'));
    }

    public function update(Request $request, adm_s_questionnaires $questionnaire)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|in:questionnaire,evaluation',
            'status' => 'required|boolean',
        ]);

        $questionnaire->update($request->all());

        return redirect()->route('questionnaires.index')->with('success', 'تم تعديل الاستبيان بنجاح.');
    }

    public function destroy(adm_s_questionnaires $questionnaire)
{
    // التحقق من وجود سجلات مرتبطة في جدول adm_b_questionnaire باستخدام questionnaire_id
    $hasLinkedRecords = adm_b_questionnaire::where('questionnair', $questionnaire->id)->exists();

    if ($hasLinkedRecords) {
        return response()->json([
            'status' => 'error',
            'message' => 'لا يمكن حذف الاستبيان لأنه مرتبط بسجلات أخرى.'
    ], 400);

    }

    $questionnaire->delete();
    return response()->json(['message' => 'تم حذف الاستبيان بنجاح.']);
}

}
