<?php
namespace App\Http\Controllers\finance\banks;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class banks_settingscontroller extends Controller
{
    public function index()
    {
        return view('finance.banks.settings.index');
    }
    public function finance_banks()
    {
        return view('finance.banks.settings.finance_banks');
    }
    public function finance_bank_branches()
    {
        return view('finance.banks.settings.finance_bank_branches');
    }
    public function finance_bank_accounts()
    {
        return view('finance.banks.settings.finance_bank_accounts');
    }
    public function finance_currencies()
    {
        return view('finance.banks.settings.finance_currencies');
    }
    public function finance_bank_permissions()
    {
        return view('finance.banks.settings.finance_bank_permissions');
    }
       public function finance_bank_thresholds()
    {
        return view('finance.banks.settings.finance_bank_thresholds');
    }
     public function finance_bank_gl_integration()
    {
        return view('finance.banks.settings.finance_bank_gl_integration');
    }
       public function finance_default_currency ()
    {
        return view('finance.banks.settings.finance_defaultcurrency');
    }
}