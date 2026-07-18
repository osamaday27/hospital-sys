<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Suppliers\SupplierAlert;
use App\Models\Suppliers\SuppliersData;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class supplieralertscontroller extends Controller
{
    public function index(Request $request)
    {
        // قائمة الموردين للاختيار في المودال
        $suppliers = SuppliersData::select('id','code','name_ar','name_en')
            ->whereNull('deleted_at')
            ->get();

        if ($request->ajax()) {
            $query = SupplierAlert::with('supplier')
                        ->select('supplier_alerts.*');

            return DataTables::of($query)
                ->addColumn('supplier_ar', fn($row) => optional($row->supplier)->name_ar)
                ->addColumn('supplier_en', fn($row) => optional($row->supplier)->name_en)
                ->addColumn('type', fn($row) => $row->type === 'addition' ? 'إضافة' : 'خصم')
                ->editColumn('value', fn($row) => number_format($row->value, 2))
                ->editColumn('notify_date', fn($row) => $row->notify_date->format('Y-m-d'))
                ->addColumn('notes', fn($row) => $row->notes)
                ->addColumn('action', function($row){
                    return '
                        <button data-id="'.$row->id.'" class="btn btn-sm btn-outline-secondary edit-btn">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button data-id="'.$row->id.'" class="btn btn-sm btn-outline-danger delete-btn">
                            <i class="ri-delete-bin-line"></i>
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('suppliers.program.supplier_alerts.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'  => ['required', Rule::exists('suppliers_data','id')],
            'type'         => ['required', Rule::in(['addition','discount'])],
            'value'        => 'required|numeric',
            'notify_date'  => 'required|date',
            'notes'        => 'nullable|string',
        ], [], [
            'supplier_id' => __('suppliers.supplier_ar'),
            'type'        => __('suppliers.type'),
            'value'       => __('suppliers.value'),
            'notify_date' => __('suppliers.date'),
            'notes'       => __('suppliers.notes'),
        ]);

        $alert = SupplierAlert::create($data);
        $alert->load('supplier');

        return response()->json([
            'message' => __('suppliers.alert_toast_added'),
            'data'    => [
                'id'           => $alert->id,
                'supplier_ar'  => $alert->supplier->name_ar,
                'type'         => $alert->type === 'addition' ? 'إضافة' : 'خصم',
                'value'        => number_format($alert->value, 2),
                'notify_date'  => $alert->notify_date->format('Y-m-d'),
                'notes'        => $alert->notes,
            ],
        ]);
    }

    public function edit($id)
    {
        $alert = SupplierAlert::findOrFail($id);
        return response()->json($alert);
    }

    public function update(Request $request, $id)
    {
        $alert = SupplierAlert::findOrFail($id);

        $data = $request->validate([
            'supplier_id'  => ['required', Rule::exists('suppliers_data','id')],
            'type'         => ['required', Rule::in(['addition','discount'])],
            'value'        => 'required|numeric',
            'notify_date'  => 'required|date',
            'notes'        => 'nullable|string',
        ], [], [
            'supplier_id' => __('suppliers.supplier_ar'),
            'type'        => __('suppliers.type'),
            'value'       => __('suppliers.value'),
            'notify_date' => __('suppliers.date'),
            'notes'       => __('suppliers.notes'),
        ]);

        $alert->update($data);
        $alert->load('supplier');

        return response()->json([
            'message' => __('suppliers.alert_toast_updated'),
            'data'    => [
                'id'           => $alert->id,
                'supplier_ar'  => $alert->supplier->name_ar,
                'type'         => $alert->type === 'addition' ? 'إضافة' : 'خصم',
                'value'        => number_format($alert->value, 2),
                'notify_date'  => $alert->notify_date->format('Y-m-d'),
                'notes'        => $alert->notes,
            ],
        ]);
    }

    public function destroy($id)
    {
        $alert = SupplierAlert::findOrFail($id);
        $alert->delete();

        return response()->json([
            'message' => __('suppliers.alert_toast_deleted'),
        ]);
    }
}
