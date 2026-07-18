<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Services\SupplyChain\ProductUnitService;
use App\Helpers\NumberHelper;
use App\Models\supply_chain\clientItemRequest;
class inventory_auditscontroller extends Controller
{
    public function inventory_audits()
    {
        return view('supply_chain.program.rquest_mangment.inventory_audits');

    }
    public function inventory_audits_create()
    {
        return view('supply_chain.program.rquest_mangment.inventory_audits_create');
    }
   
    public function employee_request_process ($id)
    {
        return view('supply_chain.program.rquest_mangment.employee_request_process', compact('id'));
    }
    public function department_request_process ($id)
    {
        return view('supply_chain.program.rquest_mangment.department_request_process', compact('id'));
    }
    public function view_request($id)
    {
        return view('supply_chain.program.rquest_mangment.view_request', compact('id'));
    }
    public function client_requestshow($id)
    {
        return view('supply_chain.program.rquest_mangment.client_requestshow', compact('id'));
    }
    public function client_requestedit($id)
    {
        return view('supply_chain.program.rquest_mangment.client_requestedit', compact('id'));
    }

    public function print_client_request($id, $type)
    {
        switch ($type) {
            case 'employee':
                $request = \App\Models\supply_chain\EmployeeItemRequest::with([
                    'employee:id,full_name',
                    'store:id,name_ar,manual_code',
                    'details.product:id,name_ar,product_code',
                    'details.unit:id,name_ar,name_en'
                ])->findOrFail($id);
                $items = $request->details;
                $title = 'طباعة طلب صرف أصناف لموظف';
                break;

            case 'department':
                $request = \App\Models\supply_chain\DepartmentItemRequest::with([
                    'department:id,name_ar',
                    'store:id,name_ar,manual_code',
                    'items.product:id,name_ar,product_code',
                    'items.unit:id,name_ar,name_en'
                ])->findOrFail($id);
                $items = $request->items;
                $title = 'طباعة طلب صرف أصناف لقسم';
                break;

            case 'store':
                $request = \App\Models\supply_chain\StoreTransferRequest::with([
                    'store:id,name_ar,manual_code',
                    'storeTo:id,name_ar',
                    'items.product:id,name_ar,product_code',
                    'items.unit:id,name_ar,name_en'
                ])->findOrFail($id);
                $items = $request->items;
                $title = 'طباعة تحويل مباشر من مخزن لمخزن';
                break;

            case 'client':
            default:
                $request = \App\Models\supply_chain\ClientItemRequest::with([
                    'client:id,full_name,medical_number',
                    'store:id,name_ar,manual_code',
                    'items.product:id,name_ar,product_code',
                    'items.unit:id,name_ar,name_en'
                ])->findOrFail($id);
                $items = $request->items;
                $title = 'طباعة طلب صرف أصناف لعميل';
                break;
        }

        $referenceNo = $request->request_number;

        $movements = \App\Models\supply_chain\StoreMovement::with('unit')
            ->where('reference_no', $referenceNo)
            ->get(['product_id', 'units_id', 'quantity', 'batch_no', 'expiry_date', 'movement_date', 'store_id', 'user_id']);

        $users = \App\Models\User::pluck('name', 'id');
        $stores = \App\Models\supply_chain\storemaindata::pluck('name_ar', 'id');
        $units = \App\Models\supply_chain\unit::pluck('name_ar', 'id');

        foreach ($items as $item) {
            $relatedMovements = $movements->where('product_id', $item->product_id);

            $item->issued_quantity = $relatedMovements->sum('quantity');
            $firstMovement = $relatedMovements->first();
            $item->issued_unit_name = $firstMovement ? ($units[$firstMovement->units_id] ?? null) : null;
            $item->issuer_name = $firstMovement ? ($users[$firstMovement->user_id] ?? null) : null;
            $item->movement_date = $firstMovement->movement_date ?? null;
            $item->store_name = $firstMovement ? ($stores[$firstMovement->store_id] ?? null) : null;
            $item->batch_no = $firstMovement->batch_no ?? null;
            $item->expiry_date = $firstMovement->expiry_date ?? null;
        }

        return view('supply_chain.program.rquest_mangment.print_all_request_detail', [
            'title' => $title,
            'request' => $request,
            'selectedRequest' => $request,
            'selectedType' => $type,
        ]);
    }


}