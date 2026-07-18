<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class store_settingscontroller extends Controller
{
    public function index(){
        return view('supply_chain.settings.store_settings.index');
    }
}
