<?php
namespace App\Http\Controllers\administrative\medical_record;

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
use App\models\administrative\how_to_identify_place;
use App\models\administrative\jops;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\Imports\PatientImport; // Import your PatientImport class
use Maatwebsite\Excel\Facades\Excel; // Import the Excel facade

class medical_recordcontroller extends Controller
{
    use file_storage;

    public function index()
    {
        $country = countries::where('account_id', Auth::user()->id_account)->get();

        return view('administrative.medical_record.index', compact('country'));
    }

    public function create()
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $how_to_identify_place = how_to_identify_place::all();
        $jops = jops::all();
        return view('administrative.medical_record.create', compact('contract', 'nationality', 'country', 'jops', 'how_to_identify_place'));
    }

    public function store(Request $request)
    {
        // التحقق من وجود السجل بنفس الاسم وتاريخ الميلاد
        if (medical_record::where('full_name', $request->full_name)->where('date_of_birth', $request->date_of_birth)->exists()) {
            return redirect()->back()->withInput()->with('info', trans('message_trans.exists'));
        }

        // التحقق من صحة المدخلات، مع فحص تنسيق رقم الهاتف الأول والرقم القومي وازدواجية البيانات
        $validated = $request->validate(
            [
                'full_name' => 'required|string|max:255',
                'first_name_ar' => 'nullable|string|max:255',
                'second_name_ar' => 'nullable|string|max:255',
                'third_name_ar' => 'nullable|string|max:255',
                'fourth_name_ar' => 'nullable|string|max:255',
                'first_name_en' => 'nullable|string|max:255',
                'second_name_en' => 'nullable|string|max:255',
                'third_name_en' => 'nullable|string|max:255',
                'fourth_name_en' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string',
                'marital_status' => 'nullable|string',
                // الهاتف الأول: يجب أن يبدأ بـ01 ويكون 11 رقمًا (مثال: 01012345678)
                'phone_number' => ['nullable', 'regex:/^01[0125][0-9]{8}$/', 'unique:patient,phone_number'],
                'phone_number2' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                // الرقم القومي: 14 رقمًا
                'national_id' => ['nullable', 'digits:14', 'unique:patient,national_id'],
                'passport' => ['nullable', 'string', 'unique:patient,passport'],
                'the_best_way_to_communicate' => 'nullable|string',
                'insurance_contracting' => 'nullable|string',
                'nationality' => 'nullable|string',
                'id_country' => 'nullable|string',
                'id_governmentes' => 'nullable|string',
                'id_city' => 'nullable|string',
                'id_area' => 'nullable|string',
                'full_address' => 'nullable|string',
                'img_url' => 'nullable|image',
            ],
            [
                'phone_number.regex' => 'رقم الهاتف غير صالح. يجب أن يبدأ بـ 01 ويتألف من 11 رقمًا.',
                'phone_number.unique' => 'رقم الهاتف مستخدم مسبقًا.',
                'national_id.digits' => 'الرقم القومي يجب أن يتألف من 14 رقمًا.',
                'national_id.unique' => 'الرقم القومي مستخدم مسبقًا.',
                'passport.unique' => 'رقم جواز السفر مستخدم مسبقًا.',
            ],
        );

        try {
            DB::beginTransaction();

            // معالجة الصورة (إن وجدت)
            $img = null;

            if ($request->hasFile('img_url')) {
                $file = $request->file('img_url');

                // لو جالك array من الصور، خذ أول واحدة فقط
                if (is_array($file)) {
                    $file = $file[0];
                }

                // رفع الصورة وأخذ أول مسار فقط
                $result = $this->file_storage($file, 'patient_img');
                $img = is_array($result) ? $result[0] ?? null : $result;
            }
            // إعداد رقم المريض التسلسلي
            $lastId = DB::table('patient')->max('id') + 1;
            $patient = new medical_record();
            $patient->medical_number = $lastId . '-' . date('Y');
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
            $patient->phone_number2 = is_array($request->phone_number2) ? implode(',', $request->phone_number2) : $request->phone_number2;
            $patient->whatsapp = is_array($request->whatsapp) ? implode(',', $request->whatsapp) : $request->whatsapp;
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
            $user = Auth::user();
            $patient->default_branch = $user->default_branch ?? null;
            $patient->user_id = $user->id;
            $patient->account_id = $user->account_id ?? null;
            $patient->email = $request->email;
            $patient->blood_type = $request->blood_type;
            $patient->allergies = $request->allergies;
            $patient->chronic_diseases = $request->chronic_diseases;
            $patient->emergency_contact_name = $request->emergency_contact_name;
            $patient->emergency_contact_phone = $request->emergency_contact_phone;
            $patient->emergency_contact_relation = $request->emergency_contact_relation;
            $patient->emergency_contact_address = $request->emergency_contact_address;

            $patient->notes = $request->notes;

            // ✅ علم أن البيانات مكتملة
            $patient->data_confirmed = 1;

            // تحويل أي array بطريق الخطأ إلى string
            foreach ($patient->getAttributes() as $key => $value) {
                if (is_array($value)) {
                    $patient->$key = implode(',', $value);
                }
            }

            $patient->save();
            DB::commit();

            return redirect()->route('medical_record.index')->with('success', 'تم إنشاء المريض بنجاح.');
        } catch (Exception $e) {
            DB::rollBack();
            // عرض رسالة الخطأ والرجوع إلى النموذج مع البيانات القديمة
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $decryptedId = Crypt::decryptString($id);
        $patient = medical_record::findOrFail($decryptedId);
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $how_to_identify_place = how_to_identify_place::all();
        $jops = jops::all();

        return view('administrative.medical_record.show', compact('patient', 'contract', 'nationality', 'country', 'jops', 'how_to_identify_place'));
    }

    public function edit($id)
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $decryptedId = Crypt::decryptString($id);
        $patient = medical_record::findOrFail($decryptedId);
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $how_to_identify_place = how_to_identify_place::all();
        $jops = jops::all();

        return view('administrative.medical_record.edit', compact('patient', 'contract', 'nationality', 'country', 'jops', 'how_to_identify_place'));
    }

    public function update(Request $request)
    {
        // جلب السجل أو إظهار 404
        $patient = medical_record::findOrFail($request->id);

        // التحقق من وجود سجل بنفس الاسم وتاريخ الميلاد باستثناء السجل الحالي
        if (medical_record::where('full_name', $request->full_name)->where('date_of_birth', $request->date_of_birth)->where('id', '!=', $patient->id)->exists()) {
            return redirect()->back()->withInput()->with('info', trans('message_trans.exists'));
        }

        // التحقق من صحة المدخلات مع فحص تنسيق الهاتف الأول والرقم القومي واستثناء السجل الحالي من قواعد unique
        $validated = $request->validate(
            [
                'full_name' => 'required|string|max:255',
                'first_name_ar' => 'nullable|string|max:255',
                'second_name_ar' => 'nullable|string|max:255',
                'third_name_ar' => 'nullable|string|max:255',
                'fourth_name_ar' => 'nullable|string|max:255',
                'first_name_en' => 'nullable|string|max:255',
                'second_name_en' => 'nullable|string|max:255',
                'third_name_en' => 'nullable|string|max:255',
                'fourth_name_en' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string',
                'marital_status' => 'nullable|string',
                // الهاتف الأول: يبدأ بـ01 ويتألف من 11 رقمًا، واستثناء السجل الحالي من الفحص
                'phone_number' => ['nullable', 'regex:/^01[0125][0-9]{8}$/', 'unique:patient,phone_number,' . $patient->id],
                'phone_number2' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                // الرقم القومي: 14 رقمًا واستثناء السجل الحالي من الفحص
                'national_id' => ['nullable', 'digits:14', 'unique:patient,national_id,' . $patient->id],
                // جواز السفر واستثناء السجل الحالي
                'passport' => ['nullable', 'string', 'unique:patient,passport,' . $patient->id],
                'the_best_way_to_communicate' => 'nullable|string',
                'insurance_contracting' => 'nullable|string',
                'nationality' => 'nullable|string',
                'id_country' => 'nullable|string',
                'id_governmentes' => 'nullable|string',
                'id_city' => 'nullable|string',
                'id_area' => 'nullable|string',
                'full_address' => 'nullable|string',
                'img_url' => 'nullable|image',
            ],
            [
                'phone_number.regex' => 'رقم الهاتف غير صالح. يجب أن يبدأ بـ 01 ويتألف من 11 رقمًا.',
                'phone_number.unique' => 'رقم الهاتف مستخدم مسبقًا.',
                'national_id.digits' => 'الرقم القومي يجب أن يتألف من 14 رقمًا.',
                'national_id.unique' => 'الرقم القومي مستخدم مسبقًا.',
                'passport.unique' => 'رقم جواز السفر مستخدم مسبقًا.',
            ],
        );

        try {
            DB::beginTransaction();

            // معالجة الصورة: الاحتفاظ بالقديمة إذا لم يُرسل جديد
            $img = $patient->img; // الاحتفاظ بالصورة القديمة

            if ($request->hasFile('img_url')) {
                $file = $request->file('img_url');

                // لو جالك array من الصور، خذ أول واحدة فقط
                if (is_array($file)) {
                    $file = $file[0];
                }

                // رفع الصورة وأخذ أول مسار فقط
                $result = $this->file_storage($file, 'patient_img');
                $img = is_array($result) ? $result[0] ?? null : $result;
            }

            // تحديث بيانات المريض
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
            $patient->phone_number2 = is_array($request->phone_number2) ? implode(',', $request->phone_number2) : $request->phone_number2;
            $patient->whatsapp = is_array($request->whatsapp) ? implode(',', $request->whatsapp) : $request->whatsapp;
            $patient->the_best_way_to_communicate = $request->the_best_way_to_communicate;
            $patient->insurance_contracting = $request->insurance_contracting;
            $patient->nationality = $request->nationality;
            if ($request->has('id_country')) {
                $patient->country = $request->id_country;
            }
            if ($request->has('id_governmentes')) {
                $patient->governorate = $request->id_governmentes;
            }
            if ($request->has('id_city')) {
                $patient->city = $request->id_city;
            }
            if ($request->has('id_area')) {
                $patient->area = $request->id_area;
            }
            $patient->full_address = $request->full_address;
            $patient->img = $img;
            $patient->status = 'active';
            // صلاحيات المستخدم والفروع
            $user = Auth::user();
            $patient->default_branch = $user->default_branch ?? null;
            $patient->user_id = $user->id;
            $patient->account_id = $user->account_id ?? null;
            $patient->email = $request->email;
            $patient->blood_type = $request->blood_type;
            $patient->allergies = $request->allergies;
            $patient->chronic_diseases = $request->chronic_diseases;
            $patient->emergency_contact_name = $request->emergency_contact_name;
            $patient->emergency_contact_phone = $request->emergency_contact_phone;
            $patient->emergency_contact_relation = $request->emergency_contact_relation;
            $patient->emergency_contact_address = $request->emergency_contact_address;
            $patient->notes = $request->notes;

            // ✅ لو مفيش medical_number اتولد تلقائي
            if (empty($patient->medical_number)) {
                $lastId = DB::table('patient')->max('id') + 1;
                $patient->medical_number = $lastId . '-' . date('Y');
            }

            // ✅ علم أن البيانات مكتملة
            $patient->data_confirmed = 1;

            // حفظ التغييرات
            $patient->save();
            DB::commit();

            return redirect()->route('medical_record.index')->with('success', 'تم تحديث بيانات المريض بنجاح.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage(),
                ]);
        }
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

    public function getmedical_record(Request $request)
    {
        $patients = medical_record::select(['id', 'medical_number', 'full_name', 'gender', 'nationality', 'phone_number', 'date_of_birth', 'created_at', 'status', 'img', 'notes']);
        // فلتر للجنس إذا كان موجوداً في الطلب
        if ($request->has('gender') && $request->gender != '') {
            $patients->where('gender', $request->gender);
        }

        // فلتر للحالة إذا كان موجوداً في الطلب
        if ($request->has('status') && $request->status != '') {
            $patients->where('status', $request->status);
        }

        // فلتر للدولة إذا كان موجوداً في الطلب
        if ($request->has('country') && $request->country != '') {
            $patients->where('country', $request->country);
        }

        // فلتر للمنطقة إذا كان موجوداً في الطلب
        if ($request->has('area') && $request->area != '') {
            $patients->where('area', $request->area);
        }

        // فلتر للمدينة إذا كان موجوداً في الطلب
        if ($request->has('city') && $request->city != '') {
            $patients->where('city', $request->city);
        }

        // فلتر للمحافظة إذا كان موجوداً في الطلب
        if ($request->has('governorate') && $request->governorate != '') {
            $patients->where('governorate', $request->governorate);
        }

        return DataTables::of($patients)
            ->addColumn('status', function ($patient) {
                // تحديد الحالة باللغة العربية
                $status = $patient->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    case 'temporarily_suspended':
                        return '<span class="badge bg-danger">' . trans('status.temporarily_suspended') . '</span>';
                    case 'old_patient':
                        return '<span class="badge bg-danger">' . trans('status.old_patient') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('full_name', function ($patient) {
                // تحقق من وجود صورة المريض
                $imageSrc = $patient->img ? asset('attachments/' . $patient->img) : asset('assets/images/users/user-dummy-img.jpg');

                // عرض الصورة واسم المريض
                return '<img src="' . $imageSrc . '" alt="Patient Image" class="img-thumbnail" style="width: 50px; height: 50px;"> ' . $patient->full_name;
            })
            ->editColumn('created_at', function ($patient) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($patient->created_at)->format('d/m/Y h:i A');
            })
            ->editColumn('date_of_birth', function ($patient) {
                // حساب العمر بناءً على تاريخ الميلاد
                $birthDate = Carbon::parse($patient->date_of_birth);
                $age = $birthDate->age; // العمر بالسنوات
                $months = $birthDate->diffInMonths(Carbon::now()) % 12; // الأشهر
                $days = $birthDate->diffInDays(Carbon::now()) % 30; // الأيام

                // عرض العمر بالسنوات والشهور والأيام
                return $age . ' سنة، ' . $months . ' شهر، ' . $days . ' يوم';
            })
            ->editColumn('gender', function ($patient) {
                // تحديد الجنس وعرض أيقونة أو صورة بناءً على الجنس
                if ($patient->gender == 'male') {
                    return '<i class="fas fa-male"></i> ' . trans('patient_trans.male'); // ذكر
                } elseif ($patient->gender == 'female') {
                    return '<i class="fas fa-female"></i> ' . trans('patient_trans.female'); // أنثى
                } else {
                    return '<i class="fas fa-genderless"></i> ' . trans('patient_trans.unknown'); // غير معروف
                }
            })
            ->editColumn('phone_number', function ($patient) {
                // عرض أرقام الهاتف بشكل منظم تحت بعض
                $phoneNumbers = '';
                if ($patient->phone_number) {
                    $phoneNumbers .= '<div>' . $patient->phone_number . '</div>';
                }
                if ($patient->phone_number2) {
                    $phoneNumbers .= '<div>' . $patient->phone_number2 . '</div>';
                }
                if ($patient->whatsapp) {
                    $phoneNumbers .= '<div>' . $patient->whatsapp . '</div>';
                }

                return $phoneNumbers; // عرض الأرقام بشكل منظم
            })
            ->addColumn('action', function ($patient) {
                $encryptedId = Crypt::encryptString($patient->id);
                $deleteFunction = 'deleteFunction(' . $patient->id . ')';
                // تحقق من وجود ملف PDF للعرض
                $pdfUrl = asset('attachments/file_data/' . str_pad($patient->medical_number, 4, '0', STR_PAD_LEFT) . '.pdf');
                return '
        <a href="' .
                    route('medical_record.show', ['medical_record' => $encryptedId]) .
                    '" class="btn btn-info btn-sm">' .
                    trans('buttons.show') .
                    '</a>
        
        <a href="' .
                    $pdfUrl .
                    '" class="btn btn-success btn-sm" target="_blank">
            ' .
                    trans('buttons.show_pdf_file') .
                    '
        </a>
        
        <a href="' .
                    route('medical_record.edit', ['medical_record' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">
            ' .
                    trans('buttons.edit') .
                    '
        </a>
        
        <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">
            ' .
                    trans('buttons.delete') .
                    '
        </a>';
            })

            ->rawColumns(['status', 'full_name', 'gender', 'phone_number', 'action'])
            ->make(true);
    }
}