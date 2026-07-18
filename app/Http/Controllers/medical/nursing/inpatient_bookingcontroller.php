<?php
namespace App\Http\Controllers\medical\nursing;
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
use App\models\administrative\medical_record;
use App\models\medical\vitalsign;

class inpatient_bookingcontroller extends Controller
{
    use file_storage, get_contract;

    public function index()
    {
        return view('medical.nursing.program.inpatent_book');
    }
    public function start($id)
    {
        try {
            $idi = Crypt::decryptString($id); // استخدم decryptString بدلاً من decrypt
            $reservations = inpatientbooking::find($idi);
            $patient = medical_record::find($reservations->patient_id);
            $patient_visits = patient_visits::find($reservations->visit_id);
            $latestVitalSign = vitalsign::where('id_patient', $reservations->patient_id)->latest()->first();

            if (!$reservations) {
                return redirect()->back()->with('error', 'الحجز غير موجود.');
            }

            return view('medical.nursing.program.start_session_out', compact('patient', 'patient_visits', 'latestVitalSign', 'reservations'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'خطأ في فك التشفير: ' . $e->getMessage());
        }
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
                $encryptedPatientId = Crypt::encryptString($booking->id ?? '');
                $encryptedId = Crypt::encryptString($booking->id);
                // تعريف المتغير actions
                $actions = '';

                // زر مميز بتصميم أنيق
                $actions .=
                    '
        <a target="_blank" class="btn btn-primary waves-effect waves-infoshadow-none" href="' .
                    route('start_session_in.start', ['id' => $encryptedPatientId]) .
                    '">
            <i class="bx bxs-right-arrow-circle me-2"></i> ' .
                    trans('buttons.open') .
                    '
        </a>';

                return $actions;
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
}