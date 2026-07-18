<?php
namespace App\Http\Controllers\administrative\call_center;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\administrative\adm_s_answer_type;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class answertypecontroller extends Controller
{

public function index(Request $request)
{

    if ($request->ajax()) {
        $answerTypes = adm_s_answer_type::with('admin')->select('adm_s_answer_types.*');

        return DataTables::of($answerTypes)
            ->addColumn('admin', function ($row) {
                return optional($row->admin)->name ?? 'غير متاح';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('answer_types.edit', $row->id) . '" class="btn btn-info btn-sm">
                         <i class="fas fa-trash"></i> ' . trans('buttons.edit') . '
                    </a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i> ' . trans('buttons.delete') . '
                    </button>
                    <a href="' . route('answer_values.create', ['type_id' => $row->id]) . '" class="btn btn-light btn-sm">
                         <i class="fas fa-trash"></i> ' . trans('call_center_trans.answer_value') . '
                    </a>
                    <a href="' . route('answer_values.index', ['type_id' => $row->id]) . '" class="btn btn-secondary btn-sm">
                         <i class="fas fa-trash"></i> ' . trans('call_center_trans.show_answer_values') . '
                    </a>
                ';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->filterColumn('admin', function ($query, $keyword) {
                $query->whereHas('admin', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('administrative.call_center.settings.answer_types.index');
}


    public function create()
    {
        return view('administrative.call_center.settings.answer_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:list,value',
        ]);

        adm_s_answer_type::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'type' => $request->type,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('answer_types.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(adm_s_answer_type $answerType)
    {
        return view('administrative.call_center.settings.answer_types.edit', compact('answerType'));
    }

    public function update(Request $request, adm_s_answer_type $answerType)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:list,value',
        ]);

        $answerType->update($request->all());

        return redirect()->route('answer_types.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $answerType = adm_s_answer_type::findOrFail($id);
        $answerType->delete();

        return response()->json(['status' => 'success', 'message' => 'تم الحذف بنجاح']);
    }
}
