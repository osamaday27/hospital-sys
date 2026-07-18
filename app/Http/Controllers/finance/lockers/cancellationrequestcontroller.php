<?php
namespace App\Http\Controllers\finance\lockers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class cancellationrequestcontroller extends Controller
{
  
    public function cancellationrequest($invoice_id)
    {
        $invoice_id=$invoice_id;
        // Return the view for cancellation request with the invoice ID
        return view('finance.lockers.program.cancellation_request',compact('invoice_id'));
    }
    public function cancellationrequestmangment()
    {
      
        return view('finance.lockers.program.cancellation_request_mangment');
    } 
}