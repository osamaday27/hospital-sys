<?php
namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;

class assetscontroller extends Controller
{
    use file_storage;

    public function programs()
    {
        return view('assets.programs.index');
    }
        public function settings()
    {
        return view('assets.settings.index');
    }
            public function reports()
    {
        return view('assets.reports.index');
    }
}
