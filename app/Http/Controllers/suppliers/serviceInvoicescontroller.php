<?php
namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class serviceInvoicescontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('suppliers.program.index');
    }
    public function show($id)
    {
        return view('suppliers.program.serviceinvoiceshow', compact('id'));
    }
  public function print($id)
    {
        return view('suppliers.program.serviceinvoiceprint', compact('id'));
    }
    public function serviceinvoicesindex()
    {
        return view('suppliers.program.serviceinvoicesindex');
    }
    public function edit($id)
    {
        return view('suppliers.program.serviceinvoicesedit', compact('id'));
    }
    public function payment($id)
    {
        return view('suppliers.program.serviceinvoicespayment', compact('id'));
    }
}