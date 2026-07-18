<?php
namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class suppliers_programcontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('suppliers.program.index');
    }
    public function service_invoices()
    {
        return view('suppliers.program.service_invoices');
    }
    public function serviceinvoicesindex()
    {
        return view('suppliers.program.serviceinvoicesindex');
    }

    public function supplier_notifications()
    {
        return view('suppliers.program.supplier_notifications');
    }
   
    
}