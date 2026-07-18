<?php

namespace App\Http\Controllers\administrative\radiology;

use App\Http\Controllers\Controller;
use App\models\administrative\radiology\radiologydevicessettings;

class radiologydevicessettingscontroller extends Controller
{
    public function index()
    {
        return view('administrative.radiology.settings.devices.index');
    }
}
