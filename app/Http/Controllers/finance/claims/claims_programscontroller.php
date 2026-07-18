<?php
namespace App\Http\Controllers\finance\claims;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class claims_programscontroller extends Controller
{
    public function claims_manager()
    {
        return view('finance.claims.program.claims_manager');
    }
    public function claims_create()
    {
        return view('finance.claims.program.claims_create');
    }
}