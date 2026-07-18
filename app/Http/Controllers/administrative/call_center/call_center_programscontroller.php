<?php
namespace App\Http\Controllers\administrative\call_center;
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
use App\models\administrative\ClinicServiceLink;
use App\models\administrative\scheduling_settings;
use App\Traits\patienttrackingtrait;

class call_center_programscontroller extends Controller
{
    use file_storage, calculateage, get_price_service, get_contract, patienttrackingtrait;

    public function index()
    {
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $clinic = admsclinic::all();
        $service = service::get();
        $doctors = Employee::where('employee_type', 'physician')->where('employee_status', 'in_service')->get();
        $how_to_identify_place = how_to_identify_place::all();
        $jops = jops::all();
        return view('administrative.call_center.program.booking.index', compact('contract', 'nationality', 'country', 'clinic', 'service', 'doctors', 'jops', 'how_to_identify_place'));
    }
    public function no_show_reservations()
    {
      return view('administrative.call_center.program.no_show.index');
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
                return '
    <div class="d-flex align-items-start gap-3hover-row p-2 rounded shadow-sm" style="cursor: pointer;" onclick="showpatient(\'' .
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
        <div>
            <img src="' .
                    $imageSrc .
                    '" alt="Patient Image" class="avatar-xs rounded-circle shadow" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ddd;">
        </div>
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
            <div>
                <p class="text-muted" style="font-size: 13px;">' .
                    $phone1 .
                    '/' .
                    $phone2 .
                    '</p>
            </div>
        </div>
    </div>';
            })
            // حذف السطر الخاص بـ patient_action
            ->rawColumns(['patient_info', 'medical_number'])
            ->make(true);
    }

    // In YourController.php
    public function getDoctorsByClinic(Request $request)
    {
        $doctors = scheduling_settings::where('id_clinic', $request->clinic_id)
            ->where('status', 'active')
            ->with('doctor') // Eager load the doctor relationship
            ->get()
            ->pluck('doctor')
            ->unique('id'); // Ensures unique doctors based on their ID

        return response()->json([
            'doctors' => $doctors,
        ]);
    }
    public function getClinicServices(Request $request)
    {
        $clinic = admsclinic::with(['examinationService', 'followUpService', 'followUpOperationService'])->find($request->clinic_id);
        if ($clinic) {
            // جمع جميع الخدمات المرتبطة
            $services = collect();
            if ($clinic->examinationService) {
                $services->push($clinic->examinationService);
            }
            if ($clinic->followUpService) {
                $services->push($clinic->followUpService);
            }
            if ($clinic->followUpOperationService) {
                $services->push($clinic->followUpOperationService);
            }
            // إزالة التكرارات
            $services = $services->unique('id')->map(function ($service) {
                return ['id' => $service->id, 'name' => $service->name];
            });
        } else {
            $services = [];
        }

        return response()->json(['services' => $services]);
    }
    public function getsecond_level_service(Request $request)
    {
        $services = servicesublevel::where('level_id', '1')->get();
        $services = $services->unique('id')->map(function ($service) {
            return ['id' => $service->id, 'name' => $service->name];
        });
        return response()->json(['services' => $services]);
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
                'reservation_source' => 'callcenter',
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

    
    public function current_reservations(Request $request)
    {
        $todayDate = now()->toDateString();
        $reservations = reservations::with('doctor', 'patient', 'clinic', 'patient_visits');
        $reservations->where('date_reservations', $todayDate);

        // فلترة النتائج بناءً على الفلاتر المدخلة
        if ($request->has('clinc_id') && $request->clinc_id != '') {
            $reservations->where('clinic_id', $request->clinc_id);
        }
        if ($request->has('doctor_id') && $request->doctor_id != '') {
            $reservations->where('id_doctor', $request->doctor_id);
        }

        $reservations = $reservations->get(); // استعلام للحصول على البيانات

        return DataTables::of($reservations)
            ->addColumn('sr_no', function ($reservation) {
                return $reservation->id;
            })
            ->addColumn('q_number', function ($reservation) {
                return $reservation->q_number;
            })
            ->addColumn('status_and_visit', function ($reservation) {
                // حالة الحجز
                $statusClass = [
                    'reserved' => 'info',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'completed' => 'success',
                    'confirmed' => 'info',
                    'waiting_for_payment' => 'warning',
                ];
                $status = $reservation->status;
                $statusClassBadge = $statusClass[$status] ?? 'info';
                $statusBadge = '<span class="badge bg-' . $statusClassBadge . '" style="display: block;margin-bottom: 10px;">' . trans('outpatient_trans.' . $status) . '</span>';

                // نوع الزيارة
                $visitTypeBadge = '-';
                if ($reservation->patient_visits->type == 'examination') {
                    $visitTypeBadge = '<span class="badge bg-info" style="display: block;background-color: #49a131 !important;"><i class="fas fa-stethoscope"></i> ' . trans('outpatient_trans.examination') . '</span>';
                } elseif ($reservation->patient_visits->type == 'follow_up') {
                    $visitTypeBadge = '<span class="badge bg-info" style="display: block;"><i class="fas fa-clipboard-check"></i> ' . trans('outpatient_trans.follow_up') . '</span>';
                } elseif ($reservation->patient_visits->type == 'follow_up_opr') {
                    $visitTypeBadge = '<span class="badge bg-warning" style="display: block;"><i class="fas fa-procedures"></i> ' . trans('outpatient_trans.follow_up_opr') . '</span>';
                }

                // دمج الحالة ونوع الزيارة معًا
                return $statusBadge . ' ' . $visitTypeBadge;
            })
            ->addColumn('doctor_clinic', function ($reservation) {
                return $reservation->doctor ? $reservation->clinic->name_ar . '/' . $reservation->doctor->full_name : '-';
            })
            ->addColumn('patient_id', function ($reservation) {
                return $reservation->patient ? $reservation->patient->medical_number : '-';
            })
            ->addColumn('patient_name', function ($reservation) {
                return $reservation->patient ? $reservation->patient->full_name : '-';
            })
            ->addColumn('patient_phone', function ($reservation) {
                $phone = $reservation->patient->phone_number;
                $phone2 = $reservation->patient->phone_number2;
                $whatsapp = $reservation->patient->whatsapp;

                $phoneHtml = '<i class="bx bx-phone-outgoing text-info"></i>' . $phone;

                if ($phone2) {
                    $phoneHtml .=
                        '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bx-phone-outgoing text-info" style="color: #140cfd;"></i>' .
                        $phone2 .
                        '</span>';
                }

                if ($whatsapp) {
                    $phoneHtml .=
                        '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bxl-whatsapp text-success"></i>' .
                        $whatsapp .
                        '</span>';
                }

                return $phoneHtml;
            })
            ->addColumn('age', function ($reservation) {
                return $reservation->patient ? Carbon::parse($reservation->patient->date_of_birth)->age : '-';
            })
            ->addColumn('patient_contract', function ($reservation) {
                return $reservation->patient && $this->getcontractname($reservation->patient->insurance_contracting) ? $this->getcontractname($reservation->patient->insurance_contracting) : '-';
            })
            ->addColumn('reservations_details', function ($reservation) {
                $reservationDate = Carbon::parse($reservation->date_reservations)->format('d/m/Y');
                $reservationTime = $reservation->time_slot; // إذا كان لديك time_slot في الحجز
                $qNumber = $reservation->q_number;
                $reservationId = $reservation->id;

                return '<div style="margin-bottom: 10px;text-align: start;">
                      <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.created_at') .
                    ' ' .
                    Carbon::parse($reservation->created_at)->format('d/m/Y h:i A') .
                    '
                    </span>
                    <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.q_number') .
                    ' ' .
                    $qNumber .
                    '
                    </span>
                </div>
                <div style="text-align: start;">
                  <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.date_reservations') .
                    ' ' .
                    $reservationDate .
                    ' ' .
                    $reservationTime .
                    '
                    </span>
                    <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                        ' .
                    trans('outpatient_trans.reservation_number') .
                    ' ' .
                    $reservationId .
                    '
                    </span>
                </div>';
            })
            ->addColumn('action', function ($data) {
                $encryptedPatientId = Crypt::encryptString($data->patient->id ?? '');
                $encryptedId = Crypt::encryptString($data->id);

                $actions =
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-cog-outline"></i> ' .
                    trans('outpatient_trans.action') .
                    '
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">';

                // Update patient data link
                $actions .=
                    '<a target="_blank"  class="dropdown-item" href="' .
                    route('medical_record.edit', ['medical_record' => $encryptedPatientId]) .
                    '">
                    <i class="ri-pencil-fill text-warning"></i> ' .
                    trans('outpatient_trans.update_patient_data') .
                    '
                </a>';

                // Add service button
                $actions .=
                    '<button class="dropdown-item" onclick="getsecond_level_service(); getdataresrv(' .
                    $data->contract .
                    ', ' .
                    $data->id_patient .
                    ', ' .
                    $data->id .
                    ');" data-bs-toggle="modal" data-bs-target="#add_service">
                <i class="mdi mdi-book-cross text-info"></i> ' .
                    trans('outpatient_trans.add_service') .
                    '</button>';

                // Cancel reservation button
                $actions .=
                    '<button class="dropdown-item" onclick="cancel_reservation(' .
                    $data->id .
                    ')" data-bs-toggle="modal" data-bs-target="#cancelReservationModal">
                    <i class="mdi mdi-table-cancel text-danger"></i> ' .
                    trans('outpatient_trans.cancel_reservation') .
                    '
                </button>';

                // Visit history button
                $actions .=
                    '<button class="dropdown-item" onclick="get_last_visit(' .
                    $data->id_patient .
                    ')" data-bs-toggle="modal" data-bs-target="#visitModal">
                    <i class="mdi mdi-book-clock text-info"></i> ' .
                    trans('outpatient_trans.visits') .
                    '
                </button>';

                $actions .= '<div class="dropdown-divider"></div>';

                // Attachments link
                $actions .=
                    '<a class="dropdown-item" href="">
                    <i class="mdi mdi-folder-check-outline text-success"></i> ' .
                    trans('outpatient_trans.attachments') .
                    '
                </a>';

                // Print receipt link
                $actions .=
                    '<a class="dropdown-item" target="_blank" href="">
                    <i class="mdi mdi-printer text-secondary"></i> ' .
                    trans('outpatient_trans.print_receipt') .
                    '
                </a>';

                // Print prescription link
                $actions .=
                    '<a class="dropdown-item" target="_blank" href="">
                    <i class="mdi mdi-printer-outline text-secondary"></i> ' .
                    trans('outpatient_trans.print_prescription') .
                    '
                </a>';

                $actions .= '</div></div>';

                return $actions;
            })
            ->rawColumns(['status_and_visit', 'action', 'visit_type', 'reservations_details', 'patient_phone']) // معاملة الأعمدة الخاصة بالـ status و action كـ raw
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
}