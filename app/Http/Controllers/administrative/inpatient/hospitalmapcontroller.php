<?php

namespace App\Http\Controllers\administrative\inpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\adm_s_buildings;
use App\models\general\adm_s_floors;
use App\models\general\nursingstation;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;

class hospitalmapcontroller extends Controller
{
    public function index()
    {
        // جلب المباني مع الطوابق والمحطات والغرف والأسرة
        $buildings = adm_s_buildings::with(['floors.nursingStations.rooms.beds'])->get();
        
        // تمرير البيانات إلى الـ view
        return view('administrative.inpatient.program.map.index', compact('buildings'));
    }
}