<?php
namespace App\Http\Controllers\administrative\inpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general\area;
use App\models\general\adm_s_floors;
use App\models\general\adm_s_beds;
use App\models\general\adm_s_rooms;
use App\models\administrative\patient_visits;
use App\models\administrative\inpatientbooking;
use App\models\administrative\bookingattachment;
use App\models\finance\invoices;
use App\models\finance\receipts;
use App\models\general\nursingstation;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\Traits\web\get_contract;
use App\models\finance\contract_last_level;
use App\models\general\nationalities;
use App\models\general\countries;
use App\models\administrative\how_to_identify_place;
use App\models\administrative\jops;

class inpatient_bookingcontroller extends Controller
{
    use file_storage, get_contract;

    public function index()
    {
        return view('administrative.inpatient.program.booking.index');
    }

    public function create()
    {
        $nursingstation = nursingstation::with(['floor'])
            ->where('status', 'active')
            ->get();
        $contract = contract_last_level::get();
        $nationality = nationalities::where('account_id', Auth::user()->id_account)->get();
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $how_to_identify_place = how_to_identify_place::all();
        $jops = jops::all();
        return view('administrative.inpatient.program.booking.create', compact('nursingstation', 'contract', 'nationality', 'country', 'jops', 'how_to_identify_place'));
    }

    public function getpatientvisit(Request $request)
    {
        $patient_visits = patient_visits::where('patient_id', $request->patient_id)->get();

        return DataTables::of($patient_visits)
            ->addColumn('visit_status', function ($patient_visits) {
                return '<span class="badge bg-' . ($patient_visits->status == 'open' ? 'success' : 'danger') . '">' . trans('status.' . $patient_visits->status) . '</span>';
            })
            ->editColumn('visit_date', function ($patient_visits) {
                return Carbon::parse($patient_visits->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('visit_type', function ($patient_visits) {
                $visitTypes = [
                    'outptient' => trans('inpatient_trans.outptient'),
                    'inpatient' => trans('inpatient_trans.inpatient'),
                    'emergency' => trans('inpatient_trans.emergency'),
                    'home_visit' => trans('inpatient_trans.home_visit'),
                    'telemedicine' => trans('inpatient_trans.telemedicine'),
                    'lab' => trans('inpatient_trans.lab'),
                    'rad' => trans('inpatient_trans.rad'),
                    'pharmacy' => trans('inpatient_trans.pharmacy'),
                ];

                $typeLabel = $visitTypes[$patient_visits->type] ?? trans('status.unknown');
                $badgeColor = match ($patient_visits->type) {
                    'examination_outptient' => 'success',
                    'inpatient' => 'primary',
                    'emergency' => 'danger',
                    'home_visit' => 'warning',
                    'telemedicine' => 'info',
                    'lab' => 'dark',
                    'rad' => 'dark',
                    'pharmacy' => 'dark',
                    default => 'secondary',
                };

                return '<span class="badge bg-' . $badgeColor . '">' . $typeLabel . '</span>';
            })
            ->rawColumns(['visit_status', 'visit_type', 'visit_date'])
            ->make(true);
    }

    // دالة لحفظ الحجز
    public function store(Request $request)
    {
        // التحقق من المدخلات
        $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'bed_id' => 'nullable|exists:adm_s_beds,id',
            'patient_status' => 'nullable|in:stable,critical,recovering',
            'case_type' => 'nullable|string',
            'case_severity' => 'nullable|string',
            'booking_date' => 'nullable|date',
            'expected_discharge_date' => 'nullable|date',
            'admission_source_id' => 'nullable|exists:adm_s_admission_reasons,id',
            'referring_physician_id' => 'nullable|exists:employees,id',
            'attending_physician_id' => 'nullable|exists:employees,id',
            'referred_hospital' => 'nullable|string',
            'referring_physician_notes' => 'nullable|string',
            'specialty_notes' => 'nullable|string',
            'room_id' => 'nullable|exists:adm_s_rooms,id',
            'nursing_station_id' => 'nullable|exists:adm_s_nursing_stations,id',
            'full_room_booking' => 'nullable|boolean',
            'residency_level' => 'nullable|string',
            'blood_donation' => 'nullable|boolean',
            'admission_reason' => 'nullable|string',
            'manual_booking_number' => 'nullable|string',
            'booking_notes' => 'nullable|string',
            'initial_diagnosis' => 'nullable|string',
            'physician_request_notes' => 'nullable|string',
            'visit_id' => 'nullable|exists:patient_visits,id',
        ]);

        // التحقق من وجود حجز نشط للمريض
        $existingBooking = inpatientbooking::where('patient_id', $request->input('patient_id'))->where('status', 'active')->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'المريض لديه حجز نشط بالفعل.');
        }

        // إنشاء زيارة جديدة للمريض
        $existingVisit = patient_visits::where('patient_id', $request->input('patient_id'))->where('type', 'inpatient')->where('status', 'open')->first();

        $adm_s_rooms = adm_s_rooms::find($request->room_id);

        if ($existingVisit) {
            $visit_id = $existingVisit->id;
        } else {
            $patient_visits = new patient_visits();
            $patient_visits->patient_id = $request->input('patient_id');
            $patient_visits->date_visit = $request->input('booking_date');
            $patient_visits->type = 'inpatient';
            $patient_visits->status = 'open';
            $patient_visits->account_id = auth()->user()->id_account;
            $patient_visits->user_id = auth()->id();
            $patient_visits->save();
            $visit_id = $patient_visits->id;
        }

        // إنشاء الحجز
        $inpatientBooking = inpatientbooking::create([
            'patient_id' => $request->input('patient_id'),
            'bed_id' => $request->input('bed_id'),
            'patient_status' => $request->input('patient_status'),
            'case_type' => $request->input('case_type'),
            'case_severity' => $request->input('case_severity'),
            'booking_date' => $request->input('booking_date'),
            'expected_discharge_date' => $request->input('expected_discharge_date'),
            'admission_source_id' => $request->input('admission_source_id'),
            'referring_physician_id' => $request->input('referring_physician_id'),
            'treating_physician_id' => $request->input('treating_physician_id'),
            'referred_hospital' => $request->input('referred_hospital'),
            'referring_physician_notes' => $request->input('referring_physician_notes'),
            'specialty_notes' => $request->input('specialty_notes'),
            'room_id' => $request->input('room_id'),
            'nursing_station_id' => $request->input('nursing_station_id'),
            'full_room_booking' => $request->has('full_room_booking') ? 1 : 0,
            'residency_level' => $adm_s_rooms->residency_id,
            'blood_donation' => $request->input('blood_donation'),
            'admission_reason' => $request->input('admission_reason'),
            'manual_booking_number' => $request->input('manual_booking_number'),
            'booking_notes' => $request->input('booking_notes'),
            'initial_diagnosis' => $request->input('initial_diagnosis'),
            'physician_request_notes' => $request->input('physician_request_notes'),
            'visit_id' => $visit_id,
            'status' => 'confirmed',
            'created_by' => auth()->id(),
        ]);

        $bed = adm_s_beds::find($request->bed_id);

        if (!$bed) {
            return response()->json(['message' => 'Bed not found'], 404);
        }

        // إذا كان خيار حجز الغرفة بالكامل مفعّلًا
        if ($request->has('full_room_booking') && $request->full_room_booking == 1) {
            adm_s_beds::where('room_id', $bed->room_id)
                ->where('status', 'available')
                ->update(['status' => 'reserved', 'booking_id' => $inpatientBooking->id]);
        } else {
            $bed->status = 'reserved';
            $bed->booking_id = $inpatientBooking->id;
            $bed->save();
        }

        // إنشاء فاتورة جديدة
        $latestInvoice = invoices::latest('id')->first();
        $invoiceNumber = $latestInvoice ? $latestInvoice->id + 1 : 1;
        $invoices = new invoices();
        $invoices->number = 'INV-' . str_pad($invoiceNumber, 5, '0', STR_PAD_LEFT);
        $invoices->patient_id = $inpatientBooking->patient_id;
        $invoices->reservation_id = $inpatientBooking->id;
        $invoices->status = 'unpaid';
        $invoices->currency = 'EGP';
        $invoices->reservation_type = 'inpatient';
        $invoices->treasury = 'main';
        $invoices->paid = $request->input('amount_paid') ?? 0;
        $invoices->user_id = auth()->id();
        $invoices->save();

        // إنشاء إيصال جديد إذا كانت هناك دفعات
        if ($request->input('amount_paid') != 0) {
            $receipts = new receipts();
            $receipts->invoices_number = $invoices->number;
            $receipts->invoices_id = $invoices->id;
            $receipts->id_reservation = $invoices->booking_id;
            $receipts->pay_type = $request->input('payment_method');
            $receipts->id_contract = $request->input('id_contract');
            $receipts->total_paid = $request->input('amount_paid') ?? 0;
            $receipts->id_patient = $inpatientBooking->patient_id;
            $receipts->treasury = 'main';
            $receipts->notes = null;
            $receipts->branches = '1';
            $receipts->id_user = auth()->id();
            $receipts->save();
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['booking_id'] = $inpatientBooking->id;

        $attachments = [
            'patient_id_front' => $request->file('patient_id_front') ? $this->file_storage($request->file('patient_id_front'), 'booking_attachments') : null,
            'patient_id_back' => $request->file('patient_id_back') ? $this->file_storage($request->file('patient_id_back'), 'booking_attachments') : null,
            'companion_id_front' => $request->file('companion_id_front') ? $this->file_storage($request->file('companion_id_front'), 'booking_attachments') : null,
            'companion_id_back' => $request->file('companion_id_back') ? $this->file_storage($request->file('companion_id_back'), 'booking_attachments') : null,
            'admission_order' => $request->file('admission_order') ? $this->file_storage($request->file('admission_order'), 'booking_attachments') : null,
            'medical_report' => $request->file('medical_report') ? $this->file_storage($request->file('medical_report'), 'booking_attachments') : null,
        ];

        if (!collect($attachments)->filter()->count()) {
        } else {
            $data = array_merge($data, $attachments);
            bookingattachment::create($data);
        }

        return redirect()->route('inpatient_booking.index')->with('success', 'تم الحفظ بنجاح!');
    }
    public function getbookings(Request $request)
    {
        $todayDate = now()->toDateString(); // Current date

        // Fetch bookings with related data
        $bookings = inpatientbooking::with(['patient', 'room', 'bed', 'nursingStation']);

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
            ->rawColumns(['status', 'action', 'patient_phone', 'dates', 'location', 'days_stayed'])
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
            'pending' => 'badge-soft-warning',
            'confirmed' => 'badge-soft-success',
            'canceled' => 'badge-soft-danger',
            'admitted' => 'badge-primary',
            'discharged' => 'badge-secondary',
            'postponed' => 'badge-info',
            'rejected' => 'badge-dark',
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
        return '<div style="margin-bottom: 10px;text-align: start;">
              <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.nursing_station') .
            ' ' .
            ($booking->nursingStation ? $booking->nursingStation->name_ar : '-') .
            '
              </span>
            </div>
            <div style="text-align: start;">
              <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.room') .
            ' ' .
            ($booking->room ? $booking->room->name : '-') .
            '
              </span>
              <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">
                ' .
            trans('inpatient_trans.bed') .
            ' ' .
            ($booking->bed ? $booking->bed->name : '-') .
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

        $actions .=
            '<a target="_blank" class="dropdown-item" href="' .
            route('medical_record.edit', ['medical_record' => $encryptedPatientId]) .
            '">
                  <i class="ri-pencil-fill text-warning"></i> ' .
            trans('general.edit_patient_data') .
            '</a>';

        $actions .= '</div></div>';

        return $actions;
    }
    private function getPatientPhoneHtml($booking)
    {
        $phone = $booking->patient->phone_number??'';
        $phone2 = $booking->patient->phone_number2??'';
        $whatsapp = $booking->patient->whatsapp??'';

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
}