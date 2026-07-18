<?php
namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class suppliers_settingscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('suppliers.settings.index');
    }
}
