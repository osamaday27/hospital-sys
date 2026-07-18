<?php
namespace App\Http\Controllers\finance\contracts_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class contracts_managementcardcontroller extends Controller
{
    
    public function index()
    {
        return view('finance.pricing_contracts_management.contracts_management.index');
    }

    
}