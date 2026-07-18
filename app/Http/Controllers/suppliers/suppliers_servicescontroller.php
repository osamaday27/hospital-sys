<?php

namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\models\suppliers\supplierservice;

class suppliers_servicescontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(supplierservice::query())
            ->editColumn('status', function($s) {
                return $s->status === 'active'
                    ? '<span class="badge bg-success">' . __('suppliers.active') . '</span>'
                    : '<span class="badge bg-secondary">' . __('suppliers.inactive') . '</span>';
            })

                ->addColumn('action', function ($s) {
                    return '
                        <button class="btn btn-sm btn-info details-btn" data-id="'.$s->id.'">تفاصيل</button>
                        <button class="btn btn-sm btn-warning edit-btn"   data-id="'.$s->id.'">تعديل</button>
                        <button class="btn btn-sm btn-danger delete-btn"  data-id="'.$s->id.'">حذف</button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('suppliers.settings.suppliers_services.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|unique:suppliers_services,code',
            'name_ar'     => 'required',
            'name_en'     => 'required',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable',
            'notes'       => 'nullable',
            'status'      => 'required|in:active,inactive',
        ]);

        supplierservice::create($data);

        return response()->json([
            'success' => true,
            'message' => __('suppliers.service_created_successfully')
        ]);
    }

    public function show($id)
    {
        return supplierservice::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $service = supplierservice::findOrFail($id);

        $data = $request->validate([
            'code'        => 'required|unique:suppliers_services,code,'.$id,
            'name_ar'     => 'required',
            'name_en'     => 'required',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable',
            'notes'       => 'nullable',
            'status'      => 'required|in:active,inactive',
        ]);

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => __('suppliers.service_updated_successfully')
        ]);
    }

    public function destroy($id)
    {
        supplierservice::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => __('suppliers.service_deleted_successfully')
        ]);
    }
}
