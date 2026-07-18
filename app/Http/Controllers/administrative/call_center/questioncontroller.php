<?php

namespace App\Http\Controllers\administrative\call_center;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\administrative\adm_s_answer_type;
use App\Models\administrative\adm_s_question;
use App\Models\administrative\adm_s_questionnaires;

class questioncontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $questions = adm_s_question::with(['questionnaireType', 'answerType'])->select('*');

            // تصفية الأسئلة بناءً على `type_id` إذا كان موجودًا في الطلب
            if ($request->has('type_id') && !empty($request->type_id)) {
                $questions->where('type_id', $request->type_id);
            }

            return DataTables::of($questions)
                ->addColumn('type_name', function ($row) {
                    return $row->questionnaireType ? $row->questionnaireType->name_ar : 'غير محدد';
                })
                ->addColumn('answer_name', function ($row) {
                    if (!empty($row->text)) {
                        return "text/نص"; // إذا كان هناك إجابة نصية مخصصة، يتم عرضها
                    } elseif ($row->answer_id && $row->answerType) {
                        return $row->answerType->name_ar . ' / ' . $row->answerType->name_en;
                    }
                    return 'غير محدد';
                })

                ->addColumn('created_by', function ($row) {
                    return $row->created_by ?? 'غير معروف';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-info btn-sm edit-btn" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $questionnaires = adm_s_questionnaires::all();
        $answerTypes = adm_s_answer_type::all();
        return view('administrative.call_center.settings.questions.index', compact('questionnaires', 'answerTypes'));
    }



    public function store(Request $request)
    {



        try {
            $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'type_id' => 'required|exists:adm_s_questionnaires,id',
                'answer_id' => 'nullable|exists:adm_s_answer_types,id',
                'text' => 'required|boolean',
            ]);


            $question = adm_s_question::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'type_id' => $request->type_id,
                'answer_id' => $request->answer_id,
                'text' => $request->text,
                'created_by' => Auth::user()->name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تمت الإضافة بنجاح',
                'question' => $question
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $question = adm_s_question::findOrFail($id);
        return response()->json($question);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type_id' => 'required|exists:adm_s_questionnaires,id',
            'answer_id' => 'nullable|exists:adm_s_answer_types,id',
            'text' => 'required|boolean',
        ]);

        $question = adm_s_question::findOrFail($id);



        $question->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'type_id' => $request->type_id,
            'answer_id' => $request->answer_id,
            'text' => $request->text,
        ]);

        return response()->json(['status' => 'success', 'message' => 'تم التعديل بنجاح']);
    }

    public function destroy($id)
    {
        $question = adm_s_question::findOrFail($id);
        $question->delete();

        return response()->json(['status' => 'success', 'message' => 'تم الحذف بنجاح']);
    }
}
