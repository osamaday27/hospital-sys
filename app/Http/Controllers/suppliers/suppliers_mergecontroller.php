<?php
namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class suppliers_mergecontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('suppliers.program.suppliers_merge.index');
    }
   public function mergeslog()
    {
        return view('suppliers.program.suppliers_merge.mergeslog');
    }
}