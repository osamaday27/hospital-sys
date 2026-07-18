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
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\general\government;
use App\models\administrative\how_to_identify_place;
use App\models\administrative\jops;
class new_patient_livewere extends Controller
{
    use file_storage;

    public function index()
    {
        return view('administrative.medical_record.index');
    }

    public function create()
    {
        $contract    = contract_last_level::get();
        $nationality = nationalities::all();
        $country     = countries::all();
        $jops        = jops::all();
        $how_to_identify_place = how_to_identify_place::all();
        
        
        return view('administrative.medical_record.createliveware', compact('contract', 'nationality', 'country', 'jops', 'how_to_identify_place'));
    }

    public function store(Request $request)
    {
        // لو فيه نفس الاسم + تاريخ الميلاد
        if (
            medical_record::where('full_name', $request->full_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->exists()
        ) {
            return redirect()->back()->withInput()->with('info', trans('message_trans.exists'));
        }

        // فاليديشن
        $validated = $request->validate(
            [
                'full_name'          => 'required|string|max:255',
                'first_name_ar'      => 'nullable|string|max:255',
                'second_name_ar'     => 'nullable|string|max:255',
                'third_name_ar'      => 'nullable|string|max:255',
                'fourth_name_ar'     => 'nullable|string|max:255',
                'first_name_en'      => 'nullable|string|max:255',
                'second_name_en'     => 'nullable|string|max:255',
                'third_name_en'      => 'nullable|string|max:255',
                'fourth_name_en'     => 'nullable|string|max:255',
                'date_of_birth'      => 'required|date',
                'gender'             => 'required|string',
                'marital_status'     => 'nullable|string',
                'phone_number'       => ['nullable', 'regex:/^01[0125][0-9]{8}$/', 'unique:patient,phone_number'],
                'phone_number2'      => 'nullable|string',
                'whatsapp'           => 'nullable|string',
                'national_id'        => ['nullable', 'digits:14', 'unique:patient,national_id'],
                'passport'           => ['nullable', 'string', 'unique:patient,passport'],
                'the_best_way_to_communicate' => 'nullable|string',
                'insurance_contracting'       => 'nullable|string',
                'nationality'        => 'nullable|string',
                'id_country'         => 'nullable|string',
                'id_governmentes'    => 'nullable|string',
                'id_city'            => 'nullable|string',
                'id_area'            => 'nullable|string',
                'full_address'       => 'nullable|string',
                'img_url'            => 'nullable|image',
            ],
            [
                'phone_number.regex'   => 'رقم الهاتف غير صالح. يجب أن يبدأ بـ 01 ويتألف من 11 رقمًا.',
                'phone_number.unique'  => 'رقم الهاتف مستخدم مسبقًا.',
                'national_id.digits'   => 'الرقم القومي يجب أن يتألف من 14 رقمًا.',
                'national_id.unique'   => 'الرقم القومي مستخدم مسبقًا.',
                'passport.unique'      => 'رقم جواز السفر مستخدم مسبقًا.',
            ]
        );

        // رفع الصورة
        $img = null;
        if ($request->hasFile('img_url')) {
            $file   = $request->file('img_url');
            if (is_array($file)) { $file = $file[0]; }
            $result = $this->file_storage($file, 'patient_img');
            $img    = is_array($result) ? ($result[0] ?? null) : $result;
        }

        $user   = Auth::user();
        $lastId = DB::table('patient')->max('id') + 1;

        $patient = new medical_record();
        $patient->medical_number           = $lastId . '-2024';
        $patient->full_name                = $request->full_name;
        $patient->first_name_ar            = $request->first_name_ar;
        $patient->second_name_ar           = $request->second_name_ar;
        $patient->third_name_ar            = $request->third_name_ar;
        $patient->fourth_name_ar           = $request->fourth_name_ar;
        $patient->first_name_en            = $request->first_name_en;
        $patient->second_name_en           = $request->second_name_en;
        $patient->third_name_en            = $request->third_name_en;
        $patient->fourth_name_en           = $request->fourth_name_en;
        $patient->date_of_birth            = $request->date_of_birth;
        $patient->gender                   = $request->gender;
        $patient->national_id              = $request->national_id;
        $patient->passport                 = $request->passport;
        $patient->marital_status           = $request->marital_status;

        $patient->phone_number             = is_array($request->phone_number)  ? implode(',', $request->phone_number)  : $request->phone_number;
        $patient->phone_number2            = is_array($request->phone_number2) ? implode(',', $request->phone_number2) : $request->phone_number2;
        $patient->whatsapp                 = is_array($request->whatsapp)      ? implode(',', $request->whatsapp)      : $request->whatsapp;

        $patient->the_best_way_to_communicate = $request->the_best_way_to_communicate;
        $patient->insurance_contracting    = $request->insurance_contracting;
        $patient->nationality              = $request->nationality;
        $patient->country                  = $request->id_country;
        $patient->governorate              = $request->id_governmentes;
        $patient->city                     = $request->id_city;
        $patient->area                     = $request->id_area;
        $patient->jop_id                   = $request->jop_id;
        $patient->how_to_identify_place_id = $request->how_to_identify_place_id;
        $patient->status                   = 'active';
        $patient->full_address             = $request->full_address;
        $patient->img                      = $img;
        $patient->default_branch           = $user->default_branch ?? null;
        $patient->user_id                  = $user->id;
        $patient->account_id               = $user->account_id ?? null;

        foreach ($patient->getAttributes() as $key => $value) {
            if (is_array($value)) {
                $patient->$key = implode(',', $value);
            }
        }

        $patient->save();

        // ✅ بدون AJAX: تحويل فوري لصفحة الحجز مع تمرير patient_id بالـ query
        return redirect()
            ->route('surgical_booking.index', ['patient_id' => $patient->id])
            ->with('success', 'تمت إضافة المريض بنجاح وسيتم تحديده تلقائياً في الحجز');
    }

    // باقي الدوال (show/edit/update/destroy/getmedical_record) تقدر تسيبها كما هي عندك إذا بتستخدمها في أماكن أخرى
}