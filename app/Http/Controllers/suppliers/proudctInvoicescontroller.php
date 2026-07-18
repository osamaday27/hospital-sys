<?php
namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class proudctInvoicescontroller extends Controller
{
    
    public function show($id)
    {
        return view('suppliers.program.warehouse_invoices.proudectinvoiceshow', compact('id'));
    }
    public function print($id)
    {
        return view('suppliers.program.warehouse_invoices.proudectinvoiceprint', compact('id'));
    }
    public function index()
    {
        return view('suppliers.program.warehouse_invoices.proudectinvoicesindex');
    }
    public function edit($id)
    {
        return view('suppliers.program.warehouse_invoices.proudectinvoicesedit', compact('id'));
    }
    public function payment($id)
    {
        return view('suppliers.program.warehouse_invoices.proudectinvoicespayment', compact('id'));
    }
    public function create()
    {
        return view('suppliers.program.warehouse_invoices.proudectinvoicescreate');
    }
}