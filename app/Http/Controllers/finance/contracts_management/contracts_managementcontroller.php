<?php
namespace App\Http\Controllers\finance\pricing_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class contracts_managementcontroller extends Controller
{
    
    public function index()
    {
        return view('finance.pricing_contracts_management.pricing_management.index');
    }

    
}