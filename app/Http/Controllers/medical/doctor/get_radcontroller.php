<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\models\finance\service;

class get_radcontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $locale = app()->getLocale(); // تحديد اللغة الحالية
            $data = service::whereHas('sublevel.level1', function ($query) {
                $query->where('level_type', 'radiology'); // تصفية الخدمات حسب نوع المستوى
            })->get();
            return DataTables::of($data)
                ->addColumn('name', function ($data) use ($locale) {
                    return $data->getTranslation('name', $locale); // جلب الاسم بالترجمة
                })
                ->make(true);
        }
        return view('medical.doctor.program.start_session_out');
    }
}