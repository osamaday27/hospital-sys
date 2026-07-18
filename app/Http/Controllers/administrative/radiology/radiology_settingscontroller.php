<?php
namespace App\Http\Controllers\administrative\radiology;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class radiology_settingscontroller extends Controller
{

    public function index()
    {
        return view('administrative.radiology.settings.index');
    }
  
}