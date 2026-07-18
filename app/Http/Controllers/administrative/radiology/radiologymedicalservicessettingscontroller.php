<?php

namespace App\Http\Controllers\administrative\radiology;

use App\Http\Controllers\Controller;

class radiologymedicalservicessettingscontroller extends Controller
{
    public function index()
    {
return view('administrative.radiology.settings.medicalservices.index');
    }
}


