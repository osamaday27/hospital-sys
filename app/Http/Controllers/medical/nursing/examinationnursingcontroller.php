<?php
namespace App\Http\Controllers\medical\nursing;

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

class examinationnursingcontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('medical.nursing.program.index');
    }

    public function create()
    {
       
    }
    public function store(Request $request)
    {
       
    }

    // عرض تفاصيل 
    public function show(AdmSClinic $admSClinic)
    {
        
    }

    // عرض صفحة تعديل 
    public function edit($encryptedId)
    {
       
    }

    // تحديث 
    public function update(Request $request)
    {
        
    }

    public function destroy($id)
    {
       
        
    }

   
    
}