<?php

namespace App\Http\Controllers\administrative\radiology;

use App\Http\Controllers\Controller;

class radiologyschedulingsettingscontroller extends Controller
{
    public function index()
    {
        return view('administrative.radiology.settings.scheduling.index');
    }
}


