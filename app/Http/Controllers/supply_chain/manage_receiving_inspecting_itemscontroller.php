<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Models\supply_chain\storemovement;
use App\Helpers\NumberHelper;
use App\Models\supply_chain\EmployeeItemRequest;
class manage_receiving_inspecting_itemscontroller extends Controller
{
    use file_storage;

 public function index(){
        return view('supply_chain.program.manage_receiving_inspecting_items.index');
 }

}
