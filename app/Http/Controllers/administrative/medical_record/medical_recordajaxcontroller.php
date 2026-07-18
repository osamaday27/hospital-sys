<?php
namespace App\Http\Controllers\administrative\medical_record;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\administrative\medical_record;
use App\models\finance\contract_last_level;
use App\models\general\nationalities;
use App\models\general\countries;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;

class medical_recordajaxcontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('administrative.medical_record.index');
    }

    public function create()
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        return view('administrative.medical_record.create', compact('contract', 'nationality', 'country'));
    }

    public function store(Request $request)
    {
        // تحقق من وجود المريض بنفس الاسم وتاريخ الميلاد
        if (medical_record::where('full_name', $request->full_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->exists()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => trans('message_trans.exists')], 409);
            }
            return redirect()->back()->with('info', trans('message_trans.exists'));
        }

        // التحقق من صحة البيانات
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'phone_number' => ['nullable', 'regex:/^01[0125][0-9]{8}$/', 'unique:patient,phone_number'],
            'national_id' => ['nullable', 'digits:14', 'unique:patient,national_id'],
            'passport' => ['nullable', 'string', 'unique:patient,passport'],
            'img_url' => 'nullable|image',
        ]);

        // معالجة الصورة
        $img = null;
        if ($request->hasFile('img_url')) {
            $file = $request->file('img_url');
            if (is_array($file)) {
                $file = $file[0];
            }
            $result = $this->file_storage($file, 'patient_img');
            $img = is_array($result) ? ($result[0] ?? null) : $result;
        }

        $user = Auth::user();

        $patient = new medical_record();
        $patient->medical_number = null; // ⚠️ مش هيتولد الرقم الطبي دلوقتي
        $patient->data_confirmed = false; // ✅ غير مكتمل
        $patient->full_name = $request->full_name;
        $patient->first_name_ar = $request->first_name_ar;
        $patient->second_name_ar = $request->second_name_ar;
        $patient->third_name_ar = $request->third_name_ar;
        $patient->fourth_name_ar = $request->fourth_name_ar;
        $patient->first_name_en = $request->first_name_en;
        $patient->second_name_en = $request->second_name_en;
        $patient->third_name_en = $request->third_name_en;
        $patient->fourth_name_en = $request->fourth_name_en;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->gender = $request->gender;
        $patient->national_id = $request->national_id;
        $patient->passport = $request->passport;
        $patient->marital_status = $request->marital_status;
        $patient->phone_number = is_array($request->phone_number) ? implode(',', $request->phone_number) : $request->phone_number;
        $patient->phone_number2 = $request->phone_number2;
        $patient->whatsapp = $request->whatsapp;
        $patient->the_best_way_to_communicate = $request->the_best_way_to_communicate;
        $patient->insurance_contracting = $request->insurance_contracting;
        $patient->nationality = $request->nationality;
        $patient->country = $request->id_country;
        $patient->governorate = $request->id_governmentes;
        $patient->city = $request->id_city;
        $patient->area = $request->id_area;
        $patient->jop_id = $request->jop_id;
        $patient->how_to_identify_place_id = $request->how_to_identify_place_id;
        $patient->status = 'active';
        $patient->full_address = $request->full_address;
        $patient->img = $img;
        $patient->default_branch = $user->default_branch ?? null;
        $patient->user_id = $user->id;
        $patient->account_id = $user->account_id ?? null;

        $patient->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة المريض بنجاح (بانتظار استكمال الملف الطبي)',
                'data' => ['id' => $patient->id, 'full_name' => $patient->full_name],
            ]);
        }

        return redirect()->route('medical_record.index')->with('success', 'تمت إضافة المريض بنجاح (بانتظار استكمال الملف الطبي)');
    }

    public function update(Request $request)
    {
        $patient = medical_record::findOrFail($request->id);

        // التحقق من تكرار الاسم وتاريخ الميلاد
        if (medical_record::where('full_name', $request->full_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->where('id', '!=', $request->id)
            ->exists()) {
            return redirect()->back()->with('info', trans('message_trans.exists'));
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'marital_status' => 'required|string',
            'phone_number' => 'nullable|string',
            'phone_number2' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'img_url' => 'nullable|image',
        ]);

        // معالجة الصورة
        $img = $patient->img;
        if ($request->hasFile('img_url')) {
            $file = $request->file('img_url');
            if (is_array($file)) {
                $file = $file[0];
            }
            $result = $this->file_storage($file, 'patient_img');
            $img = is_array($result) ? ($result[0] ?? null) : $result;
        }

        $patient->full_name = $request->full_name;
        $patient->first_name_ar = $request->first_name_ar;
        $patient->second_name_ar = $request->second_name_ar;
        $patient->third_name_ar = $request->third_name_ar;
        $patient->fourth_name_ar = $request->fourth_name_ar;
        $patient->first_name_en = $request->first_name_en;
        $patient->second_name_en = $request->second_name_en;
        $patient->third_name_en = $request->third_name_en;
        $patient->fourth_name_en = $request->fourth_name_en;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->gender = $request->gender;
        $patient->national_id = $request->national_id;
        $patient->passport = $request->passport_number;
        $patient->marital_status = $request->marital_status;
        $patient->phone_number = $request->phone_number;
        $patient->phone_number2 = $request->phone_number2;
        $patient->whatsapp = $request->whatsapp;
        $patient->the_best_way_to_communicate = $request->the_best_way_to_communicate;
        $patient->insurance_contracting = $request->insurance_contracting;
        $patient->nationality = $request->nationality;
        $patient->country = $request->country ?? $patient->country;
        $patient->governorate = $request->governorate ?? $patient->governorate;
        $patient->city = $request->city ?? $patient->city;
        $patient->area = $request->area ?? $patient->area;
        $patient->status = 'active';
        $patient->full_address = $request->full_address;
        $patient->img = $img;

        // ✅ أول مرة يستكمل بياناته → يتولد الرقم الطبي
        if (!$patient->data_confirmed) {
            $lastId = DB::table('patient')->max('id') + 1;
            $patient->medical_number = $lastId . '-' . date('Y');
            $patient->data_confirmed = true;
        }

        $patient->save();

        return redirect()->route('medical_record.index')->with('success', 'تم تحديث بيانات المريض بنجاح');
    }

    public function destroy($id)
    {
        $medical_record = medical_record::find($id);
        if ($medical_record) {
            $medical_record->delete();
            return response()->json(['success' => 'medical record deleted successfully']);
        }
        return response()->json(['error' => 'medical record not found'], 404);
    }

    public function getmedical_record()
    {
        $patients = medical_record::select(['id', 'medical_number', 'full_name', 'gender', 'nationality', 'phone_number', 'date_of_birth', 'created_at', 'status', 'img']);

        return DataTables::of($patients)
            ->addColumn('status', function ($patient) {
                switch ($patient->status) {
                    case 'active': return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive': return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    case 'temporarily_suspended': return '<span class="badge bg-danger">' . trans('status.temporarily_suspended') . '</span>';
                    default: return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('full_name', function ($patient) {
                $imageSrc = $patient->img ? asset($patient->img) : asset('assets/images/users/user-dummy-img.jpg');
                return '<img src="' . $imageSrc . '" class="img-thumbnail" style="width: 50px; height: 50px;"> ' . $patient->full_name;
            })
            ->editColumn('created_at', fn($p) => Carbon::parse($p->created_at)->format('d/m/Y h:i A'))
            ->editColumn('date_of_birth', function ($p) {
                $birthDate = Carbon::parse($p->date_of_birth);
                return $birthDate->age . ' سنة، ' . ($birthDate->diffInMonths(Carbon::now()) % 12) . ' شهر، ' . ($birthDate->diffInDays(Carbon::now()) % 30) . ' يوم';
            })
            ->editColumn('gender', function ($p) {
                if ($p->gender == "male") return '<i class="fas fa-male"></i> ' . trans('patient_trans.male');
                if ($p->gender == "female") return '<i class="fas fa-female"></i> ' . trans('patient_trans.female');
                return '<i class="fas fa-genderless"></i> ' . trans('patient_trans.unknown');
            })
            ->editColumn('phone_number', function ($p) {
                return collect([$p->phone_number, $p->phone_number2, $p->whatsapp])->filter()->map(fn($n) => "<div>$n</div>")->implode('');
            })
            ->addColumn('action', function ($p) {
                $encryptedId = Crypt::encryptString($p->id);
                return '
                <a href="' . route('medical_record.show', ['medical_record' => $encryptedId]) . '" class="btn btn-info btn-sm">' . trans('buttons.show') . '</a>
                <a href="' . route('medical_record.edit', ['medical_record' => $encryptedId]) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                <a href="javascript:void(0)" onclick="deleteFunction(' . $p->id . ')" class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['status', 'full_name', 'gender', 'phone_number', 'action'])
            ->make(true);
    }
}