<?php

namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\models\suppliers\suppliersgroup;
use App\models\general\branch;

class suppliers_groupscontroller extends Controller
{
    public function index(Request $request)
    {
        $branches = branch::all();

        if ($request->ajax()) {
            $query = suppliersgroup::with(['branch','creator'])->select('suppliers_groups.*');

            return DataTables::of($query)
                ->addColumn('branch', function($m){
                    return $m->branch->name;
                })
                ->addColumn('created_by', function($m){
                    return optional($m->creator)->name;
                })
                // تنسيق حقل created_at
                ->editColumn('created_at', function($m){
                    return $m->created_at
                              ->format('Y-m-d\TH:i:s');
                })
                // تنسيق حقل updated_at
                ->editColumn('updated_at', function($m){
                    return $m->updated_at
                              ->format('Y-m-d\TH:i:s');
                })
                // تنسيق حقل status
                ->editColumn('status', function($m){
                    return $m->status == 'active'
                        ? '<span class="badge bg-success">'. __('suppliers.active') .'</span>'
                        : '<span class="badge bg-secondary">'. __('suppliers.inactive') .'</span>';
                })
                ->addColumn('action', function($m) {
                    return '
                    <button data-id="'. $m->id .'" class="btn btn-sm btn-warning edit-btn">'. __('general.edit') .'</button>
                    <button data-id="'. $m->id .'" class="btn btn-sm btn-danger delete-btn">'. __('general.delete') .'</button>';
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view('suppliers.settings.suppliers_groups.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|unique:suppliers_groups,code',
            'name_ar'     => 'required',
            'name_en'     => 'required',
            'description' => 'nullable',
            'branch_id'   => 'required|exists:branches,id',
            'status'      => 'required|in:active,inactive',
        ]);

        $data['created_by'] = Auth::id();

        suppliersgroup::create($data);

        return response()->json([
            'success' => true,
            'message' => __('suppliers.group_created_successfully')
        ]);
    }

    public function show($id)
    {
        return suppliersgroup::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $group = suppliersgroup::findOrFail($id);

        $data = $request->validate([
            'code'        => 'required|unique:suppliers_groups,code,'.$group->id,
            'name_ar'     => 'required',
            'name_en'     => 'required',
            'description' => 'nullable',
            'branch_id'   => 'required|exists:branches,id',
            'status'      => 'required|in:active,inactive',
        ]);

        $group->update($data);

        return response()->json([
            'success' => true,
            'message' => __('suppliers.group_updated_successfully')
        ]);
    }

    public function destroy($id)
    {
        $group = suppliersgroup::findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => __('suppliers.group_deleted_successfully')
        ]);
    }
}
