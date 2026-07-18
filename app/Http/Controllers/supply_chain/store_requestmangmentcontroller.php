<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Models\supply_chain\storemovement;
use App\Helpers\NumberHelper;
use App\Models\supply_chain\EmployeeItemRequest;
class store_requestmangmentcontroller extends Controller
{
    use file_storage;

    public function issue_items_to_employee()
    {
        return view('supply_chain.program.issue_items_to_employee');
    }
    public function create_request_employee()
    {
        return view('supply_chain.program.create_request_employee');
    } 
    public function employee_requestshow($id)
    {
        return view('supply_chain.program.rquest_mangment.employee_requestshow',compact('id'));
    } 
    public function employee_requestedit($id)
    {
        return view('supply_chain.program.rquest_mangment.employee_requestedit',compact('id'));
    } 
    public function print($id)
    {
        $request = EmployeeItemRequest::where('id', $id)
            ->with(['store', 'employee', 'details.product', 'details.unit'])
            ->firstOrFail();
    
        return view('supply_chain.program.rquest_mangment.print_employee_request', [
            'title' => 'طباعة طلب صرف أصناف لموظف',
            'request' => $request,
        ]);
    }
    
}