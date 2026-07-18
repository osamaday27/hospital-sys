<?php
namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class suppliers_accountscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('suppliers.program.suppliers_accounts.index');
    }
    public function details($id)
    {
        return view('suppliers.program.suppliers_accounts.details', compact('id'));
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