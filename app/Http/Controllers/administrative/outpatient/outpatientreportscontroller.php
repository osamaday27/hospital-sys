<?php
namespace App\Http\Controllers\administrative\outpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\area;
use App\models\general\city;
use App\models\administrative\medical_record;
use App\models\general\government;
use App\models\general\nationalities;
use App\models\finance\contract_last_level;
use App\models\general\countries;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\Traits\web\calculateage;

class outpatientreportscontroller extends Controller
{
    use file_storage, calculateage;

    public function index()
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        return view('administrative.outpatient.program.index', compact('contract', 'nationality', 'country'));
    }

    public function create()
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        return view('administrative.medical_record.create', compact('contract', 'nationality', 'country'));
    }

 public function getpatient(Request $request)
{
    // تحقق من وجود مصطلح البحث
    if (!$request->has('search_term') || empty($request->search_term)) {
        // إذا لم يتم توفير مصطلح البحث، إعادة استجابة فارغة
        return DataTables::of(collect())->make(true);
    }

    // جلب البيانات من قاعدة البيانات
    $query = medical_record::with('contract', 'nationalities')->select('patient.*');

    // تطبيق البحث إذا تم إدخال مصطلح البحث
    $searchTerm = $request->search_term;
    $query->where(function ($q) use ($searchTerm) {
        $q->where('patient.full_name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('patient.phone_number', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('patient.phone_number2', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('patient.national_id', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('patient.medical_number', 'LIKE', '%' . $searchTerm . '%');
    });

    // تنفيذ العملية بناءً على DataTables
    return DataTables::of($query)
        ->addColumn('patient_data', function ($record) {
            $imageSrc = $record->img ? asset($record->img) : asset('assets/images/users/user-dummy-img.jpg');
            return '
                <div class="d-flex align-items-center">
                    <img src="' . $imageSrc . '" alt="" class="avatar-xs rounded-circle shadow me-3">
                    <div>
                        <h5 class="fs-14 mb-1  fw-bold" style="color: #033056 !important;">' . $record->full_name . '</h5>
                        <p class="text-muted mb-0">' . trans('outpatient_trans.patient_medical_number') . ': <span class="fw-medium">' . $record->medical_number . '</span></p>
                        <p class="text-muted mb-0">' . trans('outpatient_trans.contract_name') . ': <span class="fw-medium">' . ($record->contract ? $record->contract->name : '<span class="text-muted">N/A</span>') . '</span></p>
                    </div>
                </div>';
        })
        ->addColumn('patient_contact', function ($record) {
            return '
                <div>
                    <h6 class="mb-1 text-primary fw-bold">' . ($record->phone_number ? $record->phone_number : '<span class="text-muted"></span>') . '</h6>
                    <p class="text-muted mb-0">' . ($record->phone_number2 ? $record->phone_number2 : '<span class="text-muted"></span>') . '</p>
                </div>';
        })
        ->addColumn('patient_action', function ($record) {
            $patientImage = $record->img ? asset($record->img) : asset('assets/images/users/user-dummy-img.jpg');
            $fullName = $record->full_name;
            $contract_name = $record->contract->name;
            $nationalities = $record->nationality ? $record->nationalities->name : 'N/A';
            $gender = $record->gender;
            $date_of_birth = $record->date_of_birth;
            $age = $this->calculateAge($record->date_of_birth);
            $phone_number = $record->phone_number;
            $phone_number2 = $record->phone_number2;
            $whatsapp = $record->whatsapp;
            $imageUrl = $patientImage;
            $medical_number = $record->medical_number;
            return '
                <div class="btn-group shadow">
                    <button type="button" class="btn btn-primary shadow-none" id="select-patient-btn" onclick="showpatient(\'' .
                addslashes($fullName) . '\', \'' . addslashes($contract_name) . '\', \'' . addslashes($gender) . '\', \'' .
                addslashes($imageUrl) . '\', \'' . addslashes($medical_number) . '\', \'' . addslashes($nationalities) . '\', \'' .
                addslashes($date_of_birth) . '\', \'' . addslashes($age) . '\', \'' . addslashes($phone_number) . '\', \'' .
                addslashes($phone_number2) . '\', \'' . addslashes($whatsapp) . '\')">
                        <i class="ri-user-follow-line fs-15 mb-1 align-middle me-1"></i>
                    </button>
                </div>';
        })
        ->rawColumns(['patient_data', 'patient_contact', 'patient_action', 'medical_number'])
        ->make(true);
}

}