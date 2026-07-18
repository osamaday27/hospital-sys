<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Services\SupplyChain\ProductUnitService;
use App\Helpers\NumberHelper;
use App\Models\supply_chain\clientItemRequest;
class client_requeststorecontroller extends Controller
{
    public function issue_items_to_client()
    {
        return view('supply_chain.program.rquest_mangment.issue_items_to_client');

    }
    public function create_request_client()
    {
        return view('supply_chain.program.rquest_mangment.create_request_client');
    }
    public function client_requestshow($id)
    {
        return view('supply_chain.program.rquest_mangment.client_requestshow', compact('id'));
    }
    public function client_requestedit($id)
    {
        return view('supply_chain.program.rquest_mangment.client_requestedit', compact('id'));
    }
    public function print($id)
    {
        $request = \App\Models\supply_chain\ClientItemRequest::with([
            'client:id,full_name,medical_number',
            'store:id,name_ar,manual_code',
            'items.product:id,name_ar,product_code',
            'items.unit:id,name_ar,name_en'
        ])->findOrFail($id);
    
        return view('supply_chain.program.rquest_mangment.print_client_request', [
            'title' => 'طباعة طلب صرف أصناف لعميل',
            'request' => $request,
        ]);
    }
    
}