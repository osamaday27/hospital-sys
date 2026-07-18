<?php

namespace App\Http\Controllers\administrative\radiology;

use App\Http\Controllers\Controller;

class radiologyfinancialsettingscontroller extends Controller
{
    public function index()
    {
        return view('administrative.radiology.settings.financial.index');
    }
}
