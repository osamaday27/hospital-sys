<?php

namespace App\Http\Controllers\administrative\radiology;

use App\Http\Controllers\Controller;

class radiologypackagescontroller extends Controller
{
    public function index()
    {
        return view('administrative.radiology.settings.packages.index');
    }
}
