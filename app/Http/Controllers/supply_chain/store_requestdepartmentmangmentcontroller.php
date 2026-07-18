<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Models\supply_chain\storemovement;
use App\Helpers\NumberHelper;
use App\Models\supply_chain\departmentItemRequest;
class store_requestdepartmentmangmentcontroller extends Controller
{
    use file_storage;

    public function issue_items_to_department()
    {
        return view('supply_chain.program.rquest_mangment.issue_items_to_department');
    }
    public function create_request_department()
    {
        return view('supply_chain.program.rquest_mangment.create_request_department');
    } 
    public function department_requestshow($id)
    {
        return view('supply_chain.program.rquest_mangment.department_requestshow',compact('id'));
    } 
    public function department_requestedit($id)
    {
        return view('supply_chain.program.rquest_mangment.department_requestedit',compact('id'));
    } 
    public function print($id)
    {
        $request = departmentItemRequest::where('id', $id)
            ->with(['store', 'department', 'details.product', 'details.unit'])
            ->firstOrFail();
    
        return view('supply_chain.program.rquest_mangment.print_department_request', [
            'title' => 'طباعة طلب صرف أصناف لقسم',
            'request' => $request,
        ]);
    }
    
}