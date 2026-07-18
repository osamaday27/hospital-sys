<?php
namespace App\Http\Controllers\administrative\outpatient;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\administrative\medical_record;
use App\models\administrative\admsclinic;
use App\models\administrative\reservations;
use App\models\administrative\patient_visits;
use App\models\general\government;
use App\models\general\nationalities;
use App\models\finance\contract_last_level;
use App\models\finance\service;
use App\models\finance\invoices;
use App\models\finance\receipts;
use App\models\finance\servicesublevel;
use App\models\general\countries;
use App\models\hr\Employee;
use App\models\administrative\how_to_identify_place;
use App\models\administrative\jops;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\Traits\web\calculateage;
use App\Traits\web\get_price_service;
use App\Traits\web\get_contract;
use App\Traits\patienttrackingtrait;
use App\models\administrative\scheduling_settings;
use App\models\administrative\ClinicServiceLink;
use App\Traits\reservation\patientnoshowalerttrait;

class outpatientcontroller extends Controller
{
    use file_storage, calculateage, get_price_service, get_contract, patienttrackingtrait, patientnoshowalerttrait;

    public function index()
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $how_to_identify_place = how_to_identify_place::all();
        $jops = jops::all();
        $clinic = admsclinic::all();
        $service = service::get();
        $doctors = Employee::where('employee_type', 'physician')->where('employee_status', 'in_service')->get();
        return view('administrative.outpatient.program.index', compact('contract', 'nationality', 'country', 'clinic', 'service', 'doctors', 'jops', 'how_to_identify_place'));
    }
    public function printBarcode($patient_id)
    {
        $patient = medical_record::findOrFail($patient_id);

        return view('administrative.outpatient.program.print_barcode', [
            'patient' => $patient,
        ]);
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
            ->addColumn('patient_info', function ($record) {
                $imageSrc = $record->img ? asset($record->img) : asset('assets/images/users/user-dummy-img.jpg');
                $phone1 = $record->phone_number ? htmlspecialchars($record->phone_number) : '<span class="text-muted">N/A</span>';
                $phone2 = $record->phone_number2 ? htmlspecialchars($record->phone_number2) : '<span class="text-muted">N/A</span>';

                // احسب التحذير
                $warning = $this->getPatientNoShowWarning($record->id, null, null);

                return '
    <div class="d-flex flex-column gap-2 hover-row p-2 rounded shadow-sm" style="cursor: pointer;" onclick="showpatient(\'' .
                    addslashes($record->full_name) .
                    '\', \'' .
                    addslashes($record->contract ? $record->contract->name : 'N/A') .
                    '\', \'' .
                    addslashes($record->gender) .
                    '\', \'' .
                    addslashes($imageSrc) .
                    '\', \'' .
                    addslashes($record->medical_number) .
                    '\', \'' .
                    addslashes($record->nationalities ? $record->nationalities->name : 'N/A') .
                    '\', \'' .
                    addslashes($record->date_of_birth) .
                    '\', \'' .
                    addslashes($this->calculateAge($record->date_of_birth)) .
                    '\', \'' .
                    addslashes($record->phone_number) .
                    '\', \'' .
                    addslashes($record->phone_number2) .
                    '\', \'' .
                    addslashes($record->whatsapp) .
                    '\', \'' .
                    addslashes($record->insurance_contracting) .
                    '\', \'' .
                    addslashes($record->id) .
                    '\')">
        <div class="d-flex gap-3">
            <img src="' .
                    $imageSrc .
                    '" alt="Patient Image"
                class="avatar-xs rounded-circle shadow"
                style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ddd;">
            <div>
                <h5 class="fs-14 mb-1 fw-bold" style="color: #033056;">' .
                    htmlspecialchars($record->full_name) .
                    '</h5>
                <p class="text-muted mb-1" style="font-size: 13px;">
                    <span class="fw-medium" style="color: black;">' .
                    trans('outpatient_trans.patient_medical_number') .
                    ':</span>
                    <span class="fw-medium">' .
                    htmlspecialchars($record->medical_number) .
                    '</span>
                    <span class="fw-medium" style="color: black;"> ' .
                    trans('outpatient_trans.contract_name') .
                    ':</span>
                    <span class="fw-medium">' .
                    ($record->contract ? htmlspecialchars($record->contract->name) : '<span class="text-muted">N/A</span>') .
                    '</span>
                </p>
                <p class="text-muted" style="font-size: 13px;">' .
                    $phone1 .
                    '/' .
                    $phone2 .
                    '</p>' .
                    ($warning ? '<div class="alert alert-warning p-1 mt-1 mb-0" style="font-size: 12px;">' . nl2br(e($warning)) . '</div>' : '') .
                    '
                <!-- زر تعديل المريض -->
               <!-- زر تعديل المريض مع تشفير الـ id -->
<a href="' .
                    url('ar/medical_record/' . Crypt::encryptString($record->id)) .
                    '/edit' .
                    '"
    class="btn btn-sm btn-outline-primary mt-2" target="_blank">
    <i class="ri-edit-2-line"></i> ' .
                    trans('outpatient_trans.edit_patient') .
                    '
</a>

            </div>
        </div>
    </div>';
            })
            ->rawColumns(['patient_info', 'medical_number'])
            ->make(true);
    }

    // In YourController.php
    public function getDoctorsByClinic(Request $request)
    {
        $doctors = scheduling_settings::where('id_clinic', $request->clinic_id)
            ->where('status', 'active')
            ->with(['doctor:id,full_name']) // eager load
            ->get()
            ->pluck('doctor')
            ->unique('id') // unique by doctor ID
            ->values(); // لإعادة ترتيب الـ keys

        return response()->json([
            'doctors' => $doctors,
        ]);
    }

    public function checkPatientWarnings(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'clinic_id' => 'nullable|exists:adm_s_clinics,id',
            'doctor_id' => 'nullable|exists:employees,id',
        ]);

        $warning = $this->getPatientNoShowWarning($request->patient_id, $request->clinic_id, $request->doctor_id);

        return response()->json([
            'warning' => $warning,
        ]);
    }
    public function getClinicServices(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:adm_s_clinics,id',
        ]);

        $clinicServices = \App\Models\administrative\ClinicServiceLink::with('service')
            ->where('clinic_id', $request->clinic_id)
            ->where('status', 'active')
            ->get()
            ->unique('service_id') // منع التكرار
            ->map(function ($link) {
                return [
                    'id' => $link->service->id,
                    'name' => $link->service->name,
                    'service_type' => $link->service_type, // كشف - متابعة - متابعة عملية - أخرى
                    'is_consultant' => (bool) $link->is_consultant,
                    'is_doctor' => (bool) $link->is_doctor,
                    'status' => $link->status,
                ];
            })
            ->values();

        return response()->json([
            'services' => $clinicServices,
        ]);
    }

    public function getsecond_level_service(Request $request)
    {
        // تقدر تغيّر level_type من الريكوست، الافتراضي outpatient
        $levelType = $request->input('level_type', 'outpatient');

        $sublevels = servicesublevel::query()
            // نجيب الـ sublevels اللي تابعة لـ servicelevel نوعه outpatient
            ->whereHas('level1', function ($q) use ($levelType) {
                $q->where('level_type', $levelType);
            })
            // فلاتر اختيارية
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            })
            ->when($request->filled('default_branch'), function ($q) use ($request) {
                $q->where('default_branch', $request->input('default_branch'));
            })
            ->get()
            ->unique('id')
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    // إرجاع الاسم مترجم حسب اللغة الحالية
                    'name' => $sub->getTranslation('name', app()->getLocale()),
                ];
            })
            ->values(); // لإعادة الفهارس 0..n بشكل نظيف

        return response()->json(['services' => $sublevels], 200);
    }

    public function getlast_level_service(Request $request)
    {
        $services = service::where('sublevel_id', $request->second_level_id)->get();
        $services = $services->unique('id')->map(function ($service) {
            return ['id' => $service->id, 'name' => $service->name];
        });
        return response()->json(['services' => $services]);
    }
    public function get_price_service(Request $request)
    {
        // استرجاع المعرفات
        $contract_id = $request->contract_id;
        $service_id = $request->service_id;

        // منطق الحصول على السعر
        $result = $this->getPrice($contract_id, $service_id);

        // التأكد من وجود البيانات
        if (isset($result['original_price']) && isset($result['amount_to_pay'])) {
            return response()->json($result);
        } else {
            return response()->json(['error' => 'فشل في جلب البيانات']);
        }
    }
    // ✅ تخزين الحجز
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'id_doctor' => 'required|exists:employees,id',
            'clinic_id' => 'required|exists:adm_s_clinics,id',
            'id_opening_service' => 'required|exists:fin_services,id',
            'booking_date' => 'required|date',
            'time_slot' => 'required|string',
            'total' => 'required|numeric|min:0',
            'patient_contribution' => 'required|numeric|min:0',
            'contract_contribution' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'id_payment_method' => 'required|string',
            'remaining_amount' => 'required|numeric|min:0',
            'id_contract' => 'required|string',
        ]);

        $patient_id = $request->patient_id;
        $clinic_id = $request->clinic_id;
        $opening = $request->id_opening_service;

        // ✅ استرجاع نوع الزيارة من ClinicServiceLink
        $link = ClinicServiceLink::where('clinic_id', $clinic_id)->where('service_id', $opening)->where('status', 'active')->first();

        if (!$link) {
            return response()->json(['status' => 'error', 'message' => trans('outpatient_trans.You must choose the service first')]);
        }

        $visit_type = $link->service_type;
        $queue = $this->calculateQueue($request);

        // منع الحجز المكرر
        $existingReservation = reservations::where('id_patient', $patient_id)
            ->where('clinic_id', $clinic_id)
            ->where('id_doctor', $request->id_doctor)
            ->wherenot('status', 'cancelled')
            ->whereDate('date_visit', Carbon::parse($request->booking_date)->toDateString())
            ->first();

        if ($existingReservation) {
            return response()->json(['status' => 'error', 'message' => trans('outpatient_trans.Reservation already exists')]);
        }

        DB::beginTransaction();
        try {
            $visit = patient_visits::create([
                'patient_id' => $patient_id,
                'date_visit' => $request->booking_date,
                'type' => $visit_type,
                'status' => 'open',
                'account_id' => Auth::user()->id_account,
                'user_id' => Auth::id(),
            ]);

            $reservation = reservations::create([
                'id_patient' => $patient_id,
                'visit_id' => $visit->id,
                'id_doctor' => $request->id_doctor,
                'case_type' => $request->case_type,
                'reservation_note' => $request->reservation_note ?? null,

                'clinic_id' => $clinic_id,
                'date_visit' => Carbon::parse($request->booking_date)->toDateString(),
                'date_reservations' => Carbon::parse($request->booking_date)->toDateString(),
                'time_slot' => $request->time_slot,
                'reservation_type' => $opening,
                'q_number' => $queue,
                'status' => 'unconfirmed',
                'reservation_source' => 'outpatient',
                'contract' => $request->id_contract,
                'contract_id' => $request->id_contract,
                'total' => $request->total,
                'patient_contribution' => $request->patient_contribution,
                'contract_contribution' => $request->contract_contribution,
                'discount' => $request->discount ?? 0,
                'payment_method' => $request->id_payment_method,
                'paid_amount' => 0,
                'remaining_amount' => $request->remaining_amount,
                'account_id' => Auth::user()->id_account,
                'user_id' => Auth::id(),
            ]);

            $this->logPatientStep($patient_id, $visit->id, 'تم الحجز بالعيادات الخارجية', 'pending', $request->id_doctor, '');

            DB::commit();
            return response()->json(['status' => 'success', 'message' => trans('outpatient_trans.reservation_saved'), 'reservation_id' => $reservation->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()]);
        }
    }
    protected function calculateQueue(Request $request): int
    {
        $last = reservations::where('id_doctor', $request->id_doctor)->where('date_reservations', $request->booking_date)->latest('q_number')->first();

        return $last ? $last->q_number + 1 : 1;
    }

    public function arrival_confirmed(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:adm_s_reservations,id',
    ]);

    DB::beginTransaction();
    try {
        $res = reservations::with('patient')->findOrFail($request->id);

        // ✅ تحقق من حالة بيانات المريض
        if (!$res->patient || $res->patient->data_confirmed == 0 || empty($res->patient->medical_number)) {
            // 🔹 رجع URL لتبويب تعديل بيانات المريض
            $encryptedPatientId = \Crypt::encryptString($res->id_patient);
            return response()->json([
                'success' => false,
                'redirect' => true,
                'redirect_url' => route('medical_record.edit', ['medical_record' => $encryptedPatientId]),
                'message' => __('outpatient_trans.patient_data_incomplete'),
            ]);
        }

        // ✅ لو بيانات المريض كاملة نكمل العملية
        $res->update([
            'status' => 'confirm_waiting_for_payment',
            'arrival_confirmed_at' => now(),
        ]);

        // إنشاء الفاتورة
        $nextId = invoices::max('id') + 1;
        $invoice = invoices::create([
            'number' => 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT),
            'patient_id' => $res->id_patient,
            'reservation_id' => $res->id,
            'status' => 'unpaid',
            'total' => $res->total,
            'patient_contribution' => $res->patient_contribution,
            'contract_contribution' => $res->contract_contribution,
            'discount' => $res->discount,
            'currency' => 'EGP',
            'reservation_type' => 'out',
            'treasury' => 'main',
            'paid' => $res->paid_amount,
            'residual' => $res->remaining_amount,
            'contract_id' => $res->contract_id,
            'user_id' => Auth::id(),
        ]);

        // إنشاء إيصال لو فيه دفعة
        if ($res->paid_amount > 0) {
            receipts::create([
                'invoices_number' => $invoice->number,
                'invoices_id' => $invoice->id,
                'id_reservation' => $res->id,
                'pay_type' => $res->payment_method,
                'id_contract' => $res->contract_id,
                'total_paid' => $res->paid_amount,
                'total_remainder' => $res->remaining_amount,
                'id_patient' => $res->id_patient,
                'treasury' => 'main',
                'notes' => null,
                'branches' => '1',
                'id_user' => Auth::id(),
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => trans('outpatient_trans.confirm_success'),
            'invoice_number' => $invoice->number,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'فشل عند تأكيد الحجز: ' . $e->getMessage(),
        ], 500);
    }
}

   public function current_reservations(Request $request)
{
    $todayDate = now()->toDateString();

    $reservations = reservations::select([
        'id',
        'id_patient',
        'case_type',
        'visit_id',
        'clinic_id',
        'id_doctor',
        'date_visit',
        'date_reservations',
        'time_slot',
        'reservation_type',
        'q_number',
        'status',
        'contract',
        'reservation_source',
        'confirmed_at',
        'arrival_confirmed_at',
        'reservation_note',
        'user_id',
        'created_at',
    ])
        ->with([
            'doctor:id,full_name',
            'patient:id,medical_number,full_name,date_of_birth,phone_number,phone_number2,whatsapp,insurance_contracting',
            'clinic:id,name_ar',
            'patient_visits:id,type',
            'user:id,name',
        ])
        ->where('date_reservations', $todayDate);

    if ($request->filled('clinc_id')) {
        $reservations->where('clinic_id', $request->clinc_id);
    }
    if ($request->filled('doctor_id')) {
        $reservations->where('id_doctor', $request->doctor_id);
    }
    if ($request->filled('search.value')) {
        $search = $request->input('search.value');
        $reservations->where(function ($q) use ($search) {
            $q->whereHas('patient', function ($query) use ($search) {
                $query
                    ->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('phone_number2', 'like', "%{$search}%")
                    ->orWhere('medical_number', 'like', "%{$search}%");
            })
                ->orWhereHas('doctor', function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%");
                })
                ->orWhereHas('clinic', function ($query) use ($search) {
                    $query->where('name_ar', 'like', "%{$search}%");
                })
                ->orWhere('q_number', 'like', "%{$search}%");
        });
    }

    $reservations = $reservations->get();

    return DataTables::of($reservations)
        ->addColumn('sr_no', fn($reservation) => $reservation->id)
        ->addColumn('q_number', fn($reservation) => $reservation->q_number)

        ->addColumn('status_and_visit', function ($reservation) {
            $statusClass = [
                'reserved' => 'primary',
                'pending' => 'warning',
                'cancelled' => 'danger',
                'completed' => 'success',
                'confirmed' => 'info',
                'waiting_for_payment' => 'warning',
                'confirm_waiting_for_payment' => 'warning',
                'confirmed_paid' => 'info',
                'confirmed_partial' => 'info',
                'unconfirmed' => 'warning',
            ];
            $status = $reservation->status;
            $statusClassBadge = $statusClass[$status] ?? 'info';

            if (!empty($reservation->reservation_note)) {
                $statusClassBadge = 'dark';
            }

            $statusBadge = '<span class="badge bg-' . $statusClassBadge . ' mb-1">' . trans('outpatient_trans.' . $status) . '</span>';

            $visitTypeBadge = '-';
            if ($reservation->patient_visits?->type) {
                $visitTypes = [
                    'examination' => ['class' => 'primary', 'icon' => 'fas fa-stethoscope', 'text' => 'examination'],
                    'follow_up' => ['class' => 'info', 'icon' => 'fas fa-clipboard-check', 'text' => 'follow_up'],
                    'operation_follow_up' => ['class' => 'warning', 'icon' => 'fas fa-procedures', 'text' => 'follow_up_opr'],
                    'Urgent_Consultation' => ['class' => 'secondary', 'icon' => 'fas fa-ambulance', 'text' => 'Urgent_Consultation'],
                    'External_Radiology_Report' => ['class' => 'warning', 'icon' => 'fas fa-x-ray', 'text' => 'External_Radiology_Report'],
                    'Medical_Report' => ['class' => 'dark', 'icon' => 'fas fa-file-medical', 'text' => 'Medical_Report'],
                ];

                if (isset($visitTypes[$reservation->patient_visits->type])) {
                    $vt = $visitTypes[$reservation->patient_visits->type];
                    $visitTypeBadge = '<span class="badge bg-' . $vt['class'] . '"><i class="' . $vt['icon'] . '"></i> ' . trans('outpatient_trans.' . $vt['text']) . '</span>';
                }
            }

            $reservationTypeBadge = $reservation->case_type == 'doctor'
                ? '<span class="badge bg-info"><i class="ri-user-2-line"></i> ' . trans('outpatient_trans.doctor_case') . '</span>'
                : '<span class="badge bg-secondary"><i class="ri-hospital-line"></i> ' . trans('outpatient_trans.hospital_case') . '</span>';

            $sourceMap = [
                'outpatient' => ['class' => 'success', 'icon' => 'ri-hospital-line', 'text' => 'outpatient_source'],
                'callcenter' => ['class' => 'warning', 'icon' => 'ri-phone-line', 'text' => 'call_center_source'],
                'portal' => ['class' => 'primary', 'icon' => 'ri-computer-line', 'text' => 'portal_source'],
            ];
            $src = $reservation->reservation_source ?? 'outpatient';
            $sourceBadge = isset($sourceMap[$src])
                ? '<span class="badge bg-' . $sourceMap[$src]['class'] . '"><i class="' . $sourceMap[$src]['icon'] . '"></i> ' . trans('outpatient_trans.' . $sourceMap[$src]['text']) . '</span>'
                : '';

            $userBadge = $reservation->user ? '<span class="badge bg-light text-dark"><i class="ri-user-fill"></i> ' . e($reservation->user->name) . '</span>' : '';

            return $statusBadge . ' ' . $visitTypeBadge . '<br>' . $reservationTypeBadge . ' ' . $sourceBadge . '<br>' . $userBadge;
        })

        ->addColumn('doctor_clinic', fn($r) => $r->doctor ? $r->clinic->name_ar . '/' . $r->doctor->full_name : '-')
        ->addColumn('patient_id', fn($r) => $r->patient?->medical_number ?? '-')

        ->addColumn('patient_name', function ($r) {
            $name = $r->patient?->full_name ?? '-';
            return !empty($r->reservation_note) ? '<span style="color:#d9534f;font-weight:bold;">' . e($name) . '</span>' : e($name);
        })

        ->addColumn('patient_phone', function ($r) {
            $p = $r->patient;
            if (!$p) {
                return '-';
            }
            $html = '';
            if ($p->phone_number) {
                $html .= '<i class="bx bx-phone-outgoing text-primary"></i>' . $p->phone_number;
            }
            if ($p->phone_number2) {
                $html .= '<div><i class="bx bx-phone-outgoing text-info"></i>' . $p->phone_number2 . '</div>';
            }
            if ($p->whatsapp) {
                $html .= '<div><i class="bx bxl-whatsapp text-success"></i>' . $p->whatsapp . '</div>';
            }
            return $html;
        })

        ->addColumn('age', fn($r) => $r->patient ? now()->diffInYears($r->patient->date_of_birth) : '-')
        ->addColumn('patient_contract', fn($r) => $r->patient ? $this->getcontractname($r->patient->insurance_contracting) : '-')

        ->addColumn('reservations_details', function ($r) {
            $html =
                '<div>
                    <span class="badge badge-soft-dark">' . trans('outpatient_trans.created_at') . ' ' . $r->created_at->format('d/m/Y h:i A') . '</span>
                    <span class="badge badge-soft-dark">' . trans('outpatient_trans.q_number') . ' ' . $r->q_number . '</span>
                </div>
                <div>
                    <span class="badge badge-soft-info">' . trans('outpatient_trans.date_reservations') . ' ' . $r->date_reservations->format('d/m/Y') . ' ' . $r->time_slot . '</span>
                    <span class="badge badge-soft-info">' . trans('outpatient_trans.reservation_number') . ' ' . $r->id . '</span>
                </div>';

            if ($r->confirmed_at) {
                $html .= '<div><span class="badge bg-success"><i class="mdi mdi-check-circle-outline"></i> ' . trans('outpatient_trans.confirmed_at') . ': ' . $r->confirmed_at->format('d/m/Y h:i A') . '</span></div>';
            }

            if ($r->arrival_confirmed_at) {
                $html .= '<div><span class="badge bg-primary"><i class="mdi mdi-walk"></i> ' . trans('outpatient_trans.arrival_confirmed_at') . ': ' . $r->arrival_confirmed_at->format('d/m/Y h:i A') . '</span></div>';
            }

            if (!empty($r->reservation_note)) {
                $html .= '<div class="mt-2 p-2" style="background:#ffeeba;border:1px solid #f0ad4e;border-radius:5px;">
                            <strong><i class="mdi mdi-note-text-outline text-danger"></i> ' . trans('outpatient_trans.note') . ':</strong> ' . e($r->reservation_note) . '<br>
                            <small class="text-muted"><i class="mdi mdi-account"></i> ' . ($r->user?->name ?? '-') . '</small>
                          </div>';
            }
            return $html;
        })

        ->addColumn('action', function ($data) {
            $encryptedPatientId = Crypt::encryptString($data->patient->id ?? '');
            $actions =
                '<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-cog-outline"></i> ' . trans('outpatient_trans.action') . '
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">';

            if ($data->status == 'unconfirmed') {
                $actions .= '<button class="dropdown-item" onclick="confirmReservation(' . $data->id . ')">
                                <i class="mdi mdi-check-circle text-success"></i> ' . trans('outpatient_trans.confirm_reservation') . '
                             </button>';
            }
            if ($data->status == 'confirmed_waiting_for_arrival') {
                $actions .= '<button class="dropdown-item" onclick="arrival_confirmed(' . $data->id . ')">
                                <i class="mdi mdi-check-circle-outline"></i> ' . trans('outpatient_trans.arrival_confirmed') . '
                             </button>';
            }
  $actions .=
            '<a target="_blank" class="dropdown-item" href="' . route('medical_record.edit', ['medical_record' => $encryptedPatientId]) . '">
                  <i class="ri-pencil-fill text-warning"></i> ' . trans('outpatient_trans.update_patient_data') . '
            </a>';

        $actions .=
            '<button class="dropdown-item" onclick="getsecond_level_service(); getdataresrv(' . $data->contract . ', ' . $data->id_patient . ', ' . $data->id . ');" data-bs-toggle="modal" data-bs-target="#add_service">
                  <i class="mdi mdi-book-cross text-primary"></i> ' . trans('outpatient_trans.add_service') . '
            </button>';

        $actions .=
            '<button class="dropdown-item" onclick="get_last_visit(' . $data->id_patient . ')" data-bs-toggle="modal" data-bs-target="#visitModal">
                  <i class="mdi mdi-book-clock text-info"></i> ' . trans('outpatient_trans.visits') . '
            </button>';

        $actions .=
            '<button class="dropdown-item" onclick="getdataresrvupdate(' . $data->id_patient . ', ' . $data->id . ', \'' . addslashes($data->clinic->name_ar) . '\', \'' . addslashes($data->doctor->full_name) . '\', \'' . addslashes($data->service->name) . '\')" data-bs-toggle="modal" data-bs-target="#update_booking">
                  <i class="mdi mdi-clock-edit" style="color: #ff5400 !important;"></i> ' . trans('outpatient_trans.update_booking') . '
            </button>';

        $actions .= '<div class="dropdown-divider"></div>';

        $actions .=
            '<a class="dropdown-item" href="">
                  <i class="mdi mdi-folder-check-outline text-success"></i> ' . trans('outpatient_trans.attachments') . '
            </a>';

        $actions .=
            '<a class="dropdown-item" target="_blank" href="">
                  <i class="mdi mdi-printer text-secondary"></i> ' . trans('outpatient_trans.print_receipt') . '
            </a>';

        $actions .=
            '<a class="dropdown-item" target="_blank" href="">
                  <i class="mdi mdi-printer-outline text-secondary"></i> ' . trans('outpatient_trans.print_prescription') . '
            </a>';

        $actions .= '</div></div>';

        return $actions;
    

        })

        ->rawColumns(['status_and_visit', 'reservation_type', 'action', 'reservations_details', 'patient_phone', 'patient_name'])
        ->make(true);
}

    public function cancelReservation(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:adm_s_reservations,id',
            'reason_cancel_reservation' => 'required|string|max:255',
        ]);

        $reservation = reservations::find($request->id);

        if ($reservation) {
            $reservation->status = 'cancelled'; // تحديث الحالة إلى مُلغى
            $reservation->reason_cancel_reservation = $request->reason_cancel_reservation;
            $reservation->cancelled_at = now();
            $reservation->save();
            return response()->json(['success' => true, 'message' => __('outpatient_trans.cancel_success')]);
        }

        return response()->json(['success' => false, 'message' => __('outpatient_trans.cancel_error')], 404);
    }
    // ✅ تعديل الحجز
    public function update(Request $request)
    {
        $reservation = reservations::find($request->id_booking);
        if (!$reservation) {
            return response()->json(['success' => false, 'message' => __('outpatient_trans.cancel_error')], 404);
        }

        if ($request->filled('clinic_id')) {
            $reservation->clinic_id = $request->clinic_id;
        }
        if ($request->filled('id_doctor')) {
            $reservation->id_doctor = $request->id_doctor;
        }
        if ($request->filled('booking_date')) {
            $reservation->date_reservations = $request->booking_date;
        }
        if ($request->filled('id_opening_service')) {
            $reservation->reservation_type = $request->id_opening_service;

            $link = ClinicServiceLink::where('clinic_id', $reservation->clinic_id)->where('service_id', $request->id_opening_service)->where('status', 'active')->first();

            if (!$link) {
                return response()->json(['status' => 'error', 'message' => trans('outpatient_trans.You must choose the service first')]);
            }

            $visit_type = $link->service_type;
            $patient_visits = patient_visits::find($reservation->visit_id);
            if ($patient_visits) {
                $patient_visits->type = $visit_type;
                $patient_visits->save();
            }
        }
        if ($request->filled('time_slot')) {
            $reservation->time_slot = $request->time_slot;
        }

        $reservation->save();
        return response()->json(['success' => true, 'message' => __('outpatient_trans.cancel_success')]);
    }
}