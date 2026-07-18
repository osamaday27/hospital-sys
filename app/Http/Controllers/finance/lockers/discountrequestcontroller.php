<?php
namespace App\Http\Controllers\finance\lockers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class discountrequestcontroller extends Controller
{
  
    public function discountrequest($invoice_id)
    {
        $invoice_id=$invoice_id;
        // Return the view for discount request with the invoice ID
        return view('finance.lockers.program.reviewdiscountrequests',compact('invoice_id'));
    }
    public function discount_requests()
    {
        // Return the view for managing discount requests
        return view('finance.lockers.program.discountrequests');
    }
     
}