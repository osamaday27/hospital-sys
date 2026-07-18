<?php

namespace App\Http\Controllers\administrative\call_center;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\administrative\adm_s_answer_values;
use App\Models\administrative\adm_s_answer_type;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AnswerValueController extends Controller
{
    public function index(Request $request)
    {
        $typeId = $request->query('type_id'); // استقبال `type_id` من الـ URL

        if ($request->ajax()) {
            // استعلام البيانات مع التأكد من أن الفلترة تتم بشكل صحيح
            $query = adm_s_answer_values::with('type')->select('adm_s_answer_values.*');

            if (!empty($typeId)) {
                $query = $query->where('type_id', $typeId); // تطبيق الفلترة
            }

            return DataTables::of($query)
                ->addColumn('type_name', function ($row) {
                    return optional($row->type)->name_ar ?? 'غير متاح';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="' . route('answer_values.edit', $row->id) . '" class="btn btn-info btn-sm">
                        <i class="fas fa-edit"></i> ' . trans('buttons.edit') . '
                    </a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i> ' . trans('buttons.delete') . '
                    </button>
                ';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })
                ->rawColumns(['action'])
                ->make(true);
        }



        return view('administrative.call_center.settings.answer_values.index', compact('typeId'));
    }
    public function create(Request $request)
    {
        // التأكد من أن id النوع موجود
        $typeId = $request->query('type_id');
        $answerType = adm_s_answer_type::findOrFail($typeId);

        return view('administrative.call_center.settings.answer_values.create', compact('answerType'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type_id' => 'required|exists:adm_s_answer_types,id',
            'value' => 'nullable|integer',
        ]);

        adm_s_answer_values::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'type_id' => $request->type_id,
            'value' => $request->value,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('answer_values.index', ['type_id' => $request->type_id])
            ->with('success', 'تمت الإضافة بنجاح');
    }
    public function edit(adm_s_answer_values $answerValue)
    {
        $type_id = $answerValue['type_id'];
        $type = adm_s_answer_type::find($type_id);
        return view('administrative.call_center.settings.answer_values.edit', compact('answerValue', 'type'));
    }

    public function update(Request $request, adm_s_answer_values $answerValue)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type_id' => 'required|exists:adm_s_answer_types,id',
            'value' => 'nullable|integer',
        ]);

        $answerValue->update($request->all());

        return redirect()->route('answer_values.index', ['type_id' => $request->type_id])
            ->with('success', 'تمت الإضافة بنجاح');
    }

    public function destroy($id)
    {


        $answerValue = adm_s_answer_values::findOrFail($id);
        $answerValue->delete();

        return response()->json(['status' => 'success', 'message' => 'تم الحذف بنجاح']);
    }
}
