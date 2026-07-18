<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class supply_chain_settingscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('supply_chain.settings.index');
    }
}
