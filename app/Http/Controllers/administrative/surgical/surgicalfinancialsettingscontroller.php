<?php
namespace App\Http\Controllers\administrative\surgical;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class surgicalfinancialsettingscontroller extends Controller
{
  

    public function index()
    {
        return view('administrative.surgical.settings.financial.index');
    }
    public function surgeryserviceitem($id)
    {
        return view('administrative.surgical.settings.financial.surgeryserviceitem',compact('id'));
    }

 
}

    