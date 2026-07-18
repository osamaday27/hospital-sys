<?php
namespace App\Http\Controllers\finance\banks;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class bank_programscontroller extends Controller
{
    public function index()
    {
        return view('finance.banks.program.index');
    }

    public function cheque_issue()
    {
        return view('finance.banks.program.cheque_issue');
    }
    public function ChequeBooks()
    {
        return view('finance.banks.program.ChequeBooks');
    }
    public function cheque_approval()
    {
        return view('finance.banks.program.cheque_approval');
    }
    public function cheque_reconciliation()
    {
        return view('finance.banks.program.cheque_reconciliation');
    }
      public function bank_transfers()
    {
        return view('finance.banks.program.bank_transfers');
    }
     public function bank_dashboard()
    {
        return view('finance.banks.program.bank_dashboard');
    }
}