<?php
namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HrSettingsController extends Controller
{
    
    public function index()
    {
        return view('hr.settings.index');
    }

    
}