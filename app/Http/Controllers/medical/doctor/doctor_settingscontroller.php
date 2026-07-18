<?php
namespace App\Http\Controllers\medical\doctor;

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

class doctor_settingscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('medical.doctor.settings.index');
    }

    public function setting_medical_sheets()
    {
        return view('medical.doctor.settings.medical_sheets.index');
    }
    public function store(Request $request) {}

    // عرض تفاصيل
    public function show(AdmSClinic $admSClinic) {}

    // عرض صفحة تعديل
    public function edit($encryptedId) {}

    // تحديث
    public function update(Request $request) {}

    public function builder_medical_sheets($id) {
        return view('medical.doctor.settings.medical_sheets.builder', compact('id'));

    }
}