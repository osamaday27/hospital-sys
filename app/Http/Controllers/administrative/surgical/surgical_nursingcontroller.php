<?php
namespace App\Http\Controllers\administrative\surgical;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\models\finance\service;
use App\models\general\adm_s_rooms_surgical;
use App\models\administrative\adm_b_surgery_bookings;
use App\models\administrative\surgery_medical_team;
use App\models\administrative\adm_s_operation_schedules;
use App\models\hr\employee;
use App\Traits\web\get_contract;
use  App\models\medical\ms_diseases_list;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use App\models\medical\vitalsign;

class surgical_nursingcontroller extends Controller
{
    use file_storage, get_contract;

    public function index()
    {
        return view('medical.surgical.program.nursing.index');
    }

    // عرض تفاصيل
    public function show(AdmSClinic $admSClinic) {}

    // عرض صفحة تعديل
    public function edit($encryptedId) {}

    // تحديث
    public function update(Request $request) {}

    public function destroy($id) {}
    public function getbookings(Request $request)
    {
        $todayDate = now()->toDateString(); // Current date

        // Fetch bookings with related data
        $bookings = adm_b_surgery_bookings::with(['patient', 'room']);

        // Apply filters if provided
        $bookings = $this->applyBookingFilters($bookings, $request);

        // Query the data
        $bookings = $bookings->get();

        // Convert results to DataTables
        return DataTables::of($bookings)
            ->addColumn('sr_no', function ($booking) {
                return $booking->id;
            })
            ->addColumn('patient_name', function ($booking) {
                return $booking->patient ? $booking->patient->full_name : '-';
            })
            ->addColumn('medical_number', function ($booking) {
                return $booking->patient ? $booking->patient->medical_number : '-';
            })
            ->addColumn('surgical', function ($booking) {
                return $booking->services ? $booking->services->name : '-';
            })
            ->addColumn('patient_phone', function ($booking) {
                return $this->getPatientPhoneHtml($booking);
            })
            ->addColumn('age', function ($booking) {
                return $booking->patient ? Carbon::parse($booking->patient->date_of_birth)->age : '-';
            })
            ->addColumn('contract', function ($booking) {
                return $booking->patient && $this->getContractName($booking->patient->insurance_contracting) ? $this->getContractName($booking->patient->insurance_contracting) : '-';
            })
            ->addColumn('status', function ($booking) {
                return $this->getBookingStatusBadge($booking);
            })
            ->addColumn('location', function ($booking) {
                return $this->getBookinglocatHtml($booking);
            })
            ->addColumn('dates', function ($booking) {
                return $this->getBookingDatesHtml($booking);
            })
            ->addColumn('action', function ($booking) {
                return $this->getBookingActionButtons($booking);
            })
            ->addColumn('days_stayed', fn($booking) => Carbon::parse($booking->booking_date)->diffInDays(Carbon::now()))
            ->rawColumns(['status', 'action', 'patient_phone', 'dates', 'location', 'days_stayed', 'surgical'])
            ->make(true);
    }

    /**
     * Apply filters to the bookings
     */
    private function applyBookingFilters($bookings, $request)
    {
        if ($request->has('date_from') && $request->date_from != '') {
            $fromDate = Carbon::parse($request->date_from)->toDateString();
            $bookings->where('booking_date', '>=', $fromDate);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $toDate = Carbon::parse($request->date_to)->toDateString();
            $bookings->where('booking_date', '<=', $toDate);
        }

        // Only future bookings
        $bookings->where('status', '!=', 'discharged');
        $bookings->where('status', '!=', 'canceled');

        return $bookings;
    }

    /**
     * Get the status badge for bookings
     */
    private function getBookingStatusBadge($booking)
    {
        $statusLabels = [
            'booked' => 'badge-soft-primary', // تم الحجز
            'confirmed' => 'badge-soft-success', // تم التأكيد
            'preparing_patient' => 'badge-soft-warning', // تجهيز المريض
            'preparing_room' => 'badge-soft-warning', // تجهيز الغرفة
            'ready_for_surgery' => 'badge-info', // جاهز للعملية

            'anesthesia_started' => 'badge-danger', // بدأ التخدير
            'anesthesia_completed' => 'badge-dark', // التخدير مكتمل
            'incision_started' => 'badge-danger', // بدء الشق الجراحي
            'in_progress' => 'badge-primary', // العملية جارية
            'on_hold' => 'badge-warning', // موقوفة مؤقتًا

            'surgery_completed' => 'badge-success', // العملية ناجحة
            'closing_wound' => 'badge-secondary', // يتم إغلاق الجرح
            'wound_closed' => 'badge-secondary', // الجرح مغلق
            'anesthesia_reversed' => 'badge-info', // يتم إيقاف التخدير
            'recovery_room' => 'badge-soft-info', // غرفة الإفاقة
            'under_observation' => 'badge-light', // تحت المراقبة
            'discharged' => 'badge-soft-success', // خرج المريض

            'canceled' => 'badge-soft-danger', // ملغاة
            'failed' => 'badge-dark', // فشلت العملية
            'postponed' => 'badge-info', // مؤجلة
            'transferred' => 'badge-warning', // تم نقل المريض
        ];

        return '<span class="badge ' . ($statusLabels[$booking->status] ?? 'badge-light') . '  badge-border">' . trans('status.' . $booking->status) . '</span>';
    }

    /**
     * Get the booking dates details in HTML
     */
    private function getBookingDatesHtml($booking)
    {
        $bookingDate = Carbon::parse($booking->booking_date)->format('d/m/Y');
        $expectedDischargeDate = Carbon::parse($booking->expected_discharge_date)->format('d/m/Y');
        $createdAt = Carbon::parse($booking->created_at)->format('d/m/Y h:i A');

        return '<div style="margin-bottom: 10px;text-align: start;">
              <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.created_at') .
            ' ' .
            Carbon::parse($booking->created_at)->format('d/m/Y h:i A') .
            '
              </span>
                       </div>
            <div style="text-align: start;">
              <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.booking_date') .
            ' ' .
            $expectedDischargeDate .
            '
              </span>
              <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.booking_id') .
            ' ' .
            $booking->id .
            '
              </span>
            </div>';
    }
    private function getBookinglocatHtml($booking)
    {
        return '
            <div style="text-align: start;">
              <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.room') .
            ' ' .
            ($booking->room ? $booking->room->name_ar : '-') .
            '
              </span>
              
            </div>';
    }

    /**
     * Get the action buttons for bookings
     */
    private function getBookingActionButtons($booking)
    {
        $encryptedPatientId = Crypt::encryptString($booking->patient->id ?? '');
        $encryptedBookingId = Crypt::encryptString($booking->id);

        $actions =
            '<div class="btn-group">
                  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog-outline"></i> ' .
            trans('inpatient_trans.action') .
            '
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">';

        // تعديل بيانات المريض
        $actions .=
            '<a target="_blank" class="dropdown-item" href="' .
            route('medical_record.edit', ['medical_record' => $encryptedPatientId]) .
            '">
                  <i class="ri-pencil-fill text-warning"></i> ' .
            trans('general.edit_patient_data') .
            '
                </a>';
        // عرض تفاصيل المريض
        $actions .=
            '<a class="dropdown-item" href="' .
            //  route('patient.details', ['patient' => $encryptedPatientId]) .
            '">
                  <i class="ri-user-fill text-dark"></i> ' .
            trans('general.view_patient_details') .
            '
                </a>';
        // إضافة الحالة التالية
        $actions .=
            '<a class="dropdown-item" href="' .
            //    route('status.next', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-arrow-right-fill text-secondary"></i> ' .
            trans('general.nextstatus') .
            '
                </a>';

        // إضافة الخدمات
        $actions .=
            '<a class="dropdown-item" href="' .
            //   route('services.add', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-service-fill text-success"></i> ' .
            trans('general.add_services') .
            '
                </a>';

        // إضافة الأدوية
        $actions .=
            '<a class="dropdown-item" href="' .
            //    route('medications.add', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-capsule-fill text-danger"></i> ' .
            trans('general.add_medications') .
            '
                </a>';

        // إضافة المستلزمات
        $actions .=
            '<a class="dropdown-item" href="' .
            //  route('supplies.add', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-shopping-cart-fill text-primary"></i> ' .
            trans('general.add_supplies') .
            '
                </a>';

        // عد الآلات
        $actions .=
            '<a class="dropdown-item" href="' .
            //    route('machines.count', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-tools-fill text-info"></i> ' .
            trans('general.count_machines') .
            '
                </a>';

        // شيت Time In/Out
        $actions .=
            '<a class="dropdown-item" href="' .
            //     route('time_sheet.view', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-time-fill text-warning"></i> ' .
            trans('general.timeout_sheet') .
            '
                </a>';
        $actions .=
            '<a class="dropdown-item" href="' .
            //     route('time_sheet.view', ['booking' => $encryptedBookingId]) .
            '">
                  <i class="ri-time-fill text-warning"></i> ' .
            trans('general.signin_sheet') .
            '
                </a>';

        $actions .= '</div></div>';

        return $actions;
    }

    private function getPatientPhoneHtml($booking)
    {
        $phone = $booking->patient->phone_number;
        $phone2 = $booking->patient->phone_number2;
        $whatsapp = $booking->patient->whatsapp;

        $phoneHtml = '<i class="bx bx-phone-outgoing text-primary"></i>' . $phone;

        if ($phone2) {
            $phoneHtml .=
                '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bx-phone-outgoing text-info" style="color: #140cfd;"></i>' .
                $phone2 .
                '
                      </span>';
        }

        if ($whatsapp) {
            $phoneHtml .=
                '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bxl-whatsapp text-success"></i>' .
                $whatsapp .
                '
                      </span>';
        }

        return $phoneHtml;
    }

    public function details($id)
    {
        $booking = adm_b_surgery_bookings::with(['patient', 'room', 'services'])->findOrFail($id);
        $teamMembers = surgery_medical_team::where('surgery_bookings_id', $id)->with('medical_team')->get();
        $availableEmployees = employee::all();
    
            $patient = medical_record::find($booking->patient_id);
            $patient_visits = patient_visits::find($booking->visit_id);
            $latestVitalSign = vitalsign::where('id_patient', $booking->patient_id)->latest()->first();
          // حساب عمر المريض
        if ($patient->birth_date) {
            $age = \Carbon\Carbon::parse($patient->birth_date)->age;
        } else {
            $age = 20;
        }
           
             return view('medical.surgical.program.nursing.details', compact('age','booking','latestVitalSign', 'teamMembers', 'availableEmployees','patient','patient_visits'));
    }
}