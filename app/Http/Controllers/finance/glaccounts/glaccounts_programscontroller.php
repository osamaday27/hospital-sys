<?php
namespace App\Http\Controllers\finance\glaccounts;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class glaccounts_programscontroller extends Controller
{
    public function index()
    {
        return view('finance.glaccounts.program.index');
    }

    public function gl_entries()
    {
        return view('finance.glaccounts.program.gl_entries');
    }
    public function trial_balance()
    {
        return view('finance.glaccounts.program.trial_balance');
    }
    public function financial_statements()
    {
        return view('finance.glaccounts.program.financial_statements');
    }
    public function gl_closing()
    {
        return view('finance.glaccounts.program.gl_closing');
    }
}