<?php
namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;

class purchases_programscontroller extends Controller
{

    public function index()
    {
        return view('purchases.programs.index');
    }
}
