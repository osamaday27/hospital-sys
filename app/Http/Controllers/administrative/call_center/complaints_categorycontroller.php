<?php

namespace App\Http\Controllers\administrative\call_center;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\administrative\adm_s_complaint_category;

class complaints_categorycontroller extends Controller
{
    public function index(Request $request)
    {


    if ($request->ajax()) {
        $complaintCategory = adm_s_complaint_category::select('adm_s_complaint_categories.*');
        return DataTables::of($complaintCategory)
            ->addColumn('status', function ($row) {
                return $row->status ;
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('complaints-category.edit', $row->id) . '" class="btn btn-info btn-sm">
                         <i class="fas fa-edit"></i> ' . trans('buttons.edit') . '
                    </a>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i> ' . trans('buttons.delete') . '
                    </button>
                    <a href="' . route('complaints-problem.index', ['category_id' => $row->id]) . '" class="btn btn-light btn-sm">
                         <i class="fas fa-trash"></i> ' . trans('call_center_trans.problems') . '
                    </a>

                ';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

        return view('administrative/call_center/settings/complaints/categories.index');
    }
    public function create()
    {
        return view('administrative/call_center/settings/complaints/categories.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        adm_s_complaint_category::create($request->all());

        return redirect()->route('complaints-category.index')->with('success', trans('call_center_trans.data_saved_successfully'));
    }
    public function edit($id)
{
    $category = adm_s_complaint_category::findOrFail($id);
    return view('administrative/call_center/settings/complaints/categories.edit', compact('category'));
}
public function update(Request $request, $id)
{
    $category = adm_s_complaint_category::findOrFail($id);

    $validatedData = $request->validate([
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'status' => 'required|boolean',
    ]);

    $category->update($validatedData);

    return redirect()->route('complaints-category.index')->with('success', trans('call_center_trans.data_updated_successfully'));
}

public function destroy($id)
{
    $category = adm_s_complaint_category::findOrFail($id);
    $category->delete();
    return response()->json(['message' => trans('call_center_trans.data_deleted_successfully')]);
}


}
