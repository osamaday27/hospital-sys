<?php
namespace App\Http\Controllers\finance\lockers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;

class lockerdesktopcontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('finance.lockers.program.lockerdesktop.index');
    }
    public function delivering_sub_lockers_main_lockers()
    {
        return view('finance.lockers.program.delivering_sub_lockers_main_lockers');
    }

}