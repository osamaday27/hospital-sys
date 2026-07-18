<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Services\SupplyChain\ProductUnitService;
use App\Helpers\NumberHelper;
use App\Models\supply_chain\storeItemRequest;
class store_requeststorecontroller extends Controller
{
    public function issue_items_to_store()
    
    {        return view('supply_chain.program.rquest_mangment.issue_items_to_store');

    }
    public function create_request_store()
    {
        return view('supply_chain.program.rquest_mangment.create_request_store');
    }
    public function store_requestshow($id)
    {
        return view('supply_chain.program.rquest_mangment.store_requestshow', compact('id'));
    }
    public function store_requestedit($id)
    {
        return view('supply_chain.program.rquest_mangment.store_requestedit', compact('id'));
    }
    public function print($id)
    {
        $request = storeItemRequest::where('id', $id)
            ->with(['store', 'store', 'details.product', 'details.unit'])
            ->firstOrFail();

        return view('supply_chain.program.rquest_mangment.print_store_request', [
            'title' => 'طباعة طلب صرف أصناف لقسم',
            'request' => $request,
        ]);
    }
}