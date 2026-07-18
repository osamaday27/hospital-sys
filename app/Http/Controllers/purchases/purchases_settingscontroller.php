<?php
namespace App\Http\Controllers\purchases;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class purchases_settingscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('purchases.settings.index');
    }
}
