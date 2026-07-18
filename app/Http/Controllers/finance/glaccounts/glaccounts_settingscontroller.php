<?php
namespace App\Http\Controllers\finance\glaccounts;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class glaccounts_settingscontroller extends Controller
{
  

    public function index()
    {
        return view('finance.glaccounts.settings.index');
    }
    public function glaccounts()
    {
        return view('finance.glaccounts.settings.glaccounts');
    }

}