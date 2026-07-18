<?php

namespace App\Http\Controllers\administrative\call_center;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\administrative\adm_s_complaint_category;
use App\Models\administrative\adm_s_complaint_problem;

class complaints_problemcontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $problems = adm_s_complaint_problem::with(['category'])->select('*');

            // تصفية الأسئلة بناءً على `category_id` إذا كان موجودًا في الطلب
            if ($request->has('category_id') && !empty($request->category_id)) {
                $problems->where('category_id', $request->category_id);
            }

            return DataTables::of($problems)
            ->addColumn('status', function ($row) {
                return $row->status ?? 'غير معروف';
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

        $categories = adm_s_complaint_category::all();
        return view('administrative/call_center/settings/complaints/problems.index', compact('categories'));
    }



    public function store(Request $request)
    {



        try {
            $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'category_id' => 'required|exists:adm_s_complaint_categories,id',
                'status' => 'required|in:1,0',
            ]);

            $problem = adm_s_complaint_problem::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'category_id' => $request->category_id,
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => trans('general.added_successfully'),
                'problem' => $problem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => trans('general.added_failed') . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $problem = adm_s_complaint_problem::findOrFail($id);
        return response()->json($problem);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'category_id' => 'required|exists:adm_s_complaint_categories,id',
            'status' => 'required|in:1,0',
        ]);

        $problem = adm_s_complaint_problem::findOrFail($id);



        $problem->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'status' => $request->status,
        ]);

        return response()->json(['status' => 'success', 'message' => trans('general.updated_successfully')]);
    }

    public function destroy($id)
    {
        $problem = adm_s_complaint_problem::findOrFail($id);
        $problem->delete();

        return response()->json(['status' => 'success', 'message' =>  trans('general.deleted_successfully') ]);
    }
}
