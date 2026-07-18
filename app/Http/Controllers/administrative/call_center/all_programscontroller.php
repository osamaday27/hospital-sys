<?php
namespace App\Http\Controllers\administrative\call_center;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\area;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_buildings;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;

class all_programscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('administrative.call_center.program.index');
    }

    public function create()
    {

    }
    public function store()
    {

    }

    public function show()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {


    }



}
