<?php
namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class hrrecruitmentprograms extends Controller
{
    
    public function index()
    {
        return view('hr.recruitment.programs');
    }

    
}