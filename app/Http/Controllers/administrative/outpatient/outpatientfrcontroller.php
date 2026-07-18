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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\Traits\web\calculateage;
use App\Traits\web\get_price_service;
use App\Traits\web\get_contract;
use App\models\administrative\scheduling_settings;

class outpatientfrcontroller extends Controller
{
    use file_storage, calculateage, get_price_service, get_contract;

    public function future_reservations(Request $request)
    {
        if (
            !$request->filled('date_from') &&
            !$request->filled('date_to') &&
            !$request->filled('clinc_id') &&
            !$request->filled('doctor_id')
        ) {
            return app(DataTables::class)->eloquent(reservations::whereNull('id'))->make(true);
        }

        $reservations = reservations::with('doctor', 'patient', 'clinic', 'patient_visits', 'user');

        if ($request->filled('date_from')) {
            $fromDate = Carbon::parse($request->date_from)->toDateString();
            $reservations->where('date_reservations', '>=', $fromDate);
        }

        if ($request->filled('date_to')) {
            $toDate = Carbon::parse($request->date_to)->toDateString();
            $reservations->where('date_reservations', '<=', $toDate);
        }

        if ($request->filled('clinc_id')) {
            $reservations->where('clinic_id', $request->clinc_id);
        }

        if ($request->filled('doctor_id')) {
            $reservations->where('id_doctor', $request->doctor_id);
        }

        return app(DataTables::class)->eloquent($reservations)
            ->addColumn('sr_no', fn($r) => $r->id)
            ->addColumn('q_number', fn($r) => $r->q_number)
            ->addColumn('status_and_visit', fn($r) => $this->getStatusAndVisitBadge($r))
            ->addColumn('doctor_clinic', fn($r) => $r->doctor ? $r->clinic->name_ar . '/' . $r->doctor->full_name : '-')
            ->addColumn('patient_id', fn($r) => $r->patient ? $r->patient->medical_number : '-')
            ->addColumn('patient_name', fn($r) => $this->getPatientNameHtml($r))
            ->addColumn('patient_phone', fn($r) => $this->getPatientPhoneHtml($r))
            ->addColumn('age', fn($r) => $r->patient ? Carbon::parse($r->patient->date_of_birth)->age : '-')
            ->addColumn('patient_contract', fn($r) =>
                $r->patient && $this->getcontractname($r->patient->insurance_contracting)
                    ? $this->getcontractname($r->patient->insurance_contracting)
                    : '-')
            ->addColumn('reservations_details', fn($r) => $this->getReservationDetailsHtml($r))
            ->addColumn('action', fn($r) => $this->getActionButtons($r))
            ->rawColumns(['status_and_visit', 'action', 'visit_type', 'reservations_details', 'patient_phone', 'patient_name'])
            ->make(true);
    }

    private function getStatusAndVisitBadge($reservation)
    {
        $statusClass = [
            'reserved' => 'primary',
            'pending' => 'warning',
            'cancelled' => 'danger',
            'completed' => 'success',
            'confirmed' => 'info',
            'waiting_for_payment' => 'warning',
        ];
        $status = $reservation->status;
        $statusClassBadge = $statusClass[$status] ?? 'info';

        // ⭐ لو في ملاحظات نغير لون الخلفية للـ Badge
        if (!empty($reservation->reservation_note)) {
            $statusClassBadge = 'dark'; // لون مختلف
        }

        $statusBadge = '<span class="badge bg-' . $statusClassBadge . '" style="display: block;margin-bottom: 10px;">' 
            . trans('outpatient_trans.' . $status) . '</span>';

        $visitTypeBadge = '-';
        if ($reservation->patient_visits && $reservation->patient_visits->type == 'examination') {
            $visitTypeBadge = '<span class="badge bg-primary" style="display: block;background-color: #49a131 !important;">
                                  <i class="fas fa-stethoscope"></i> ' . trans('outpatient_trans.examination') . '
                               </span>';
        } elseif ($reservation->patient_visits && $reservation->patient_visits->type == 'follow_up') {
            $visitTypeBadge = '<span class="badge bg-info" style="display: block;">
                                  <i class="fas fa-clipboard-check"></i> ' . trans('outpatient_trans.follow_up') . '
                               </span>';
        } elseif ($reservation->patient_visits && $reservation->patient_visits->type == 'follow_up_opr') {
            $visitTypeBadge = '<span class="badge bg-warning" style="display: block;">
                                  <i class="fas fa-procedures"></i> ' . trans('outpatient_trans.follow_up_opr') . '
                               </span>';
        }

        return $statusBadge . ' ' . $visitTypeBadge;
    }

    private function getReservationDetailsHtml($reservation)
    {
        $reservationDate = Carbon::parse($reservation->date_reservations)->format('d/m/Y');
        $reservationTime = $reservation->time_slot;
        $qNumber = $reservation->q_number;
        $reservationId = $reservation->id;

        $html = '<div style="margin-bottom: 10px;text-align: start;">
                  <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">'
                    . trans('outpatient_trans.created_at') . ' ' . Carbon::parse($reservation->created_at)->format('d/m/Y h:i A') .
                  '</span>
                  <span class="badge badge-soft-dark badge-border" style="margin: 5px;text-align: start;display: initial;">'
                    . trans('outpatient_trans.q_number') . ' ' . $qNumber .
                  '</span>
                </div>
                <div style="text-align: start;">
                  <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">'
                    . trans('outpatient_trans.date_reservations') . ' ' . $reservationDate . ' ' . $reservationTime .
                  '</span>
                  <span class="badge badge-soft-info badge-border" style="margin: 5px;text-align: start;display: initial;">'
                    . trans('outpatient_trans.reservation_number') . ' ' . $reservationId .
                  '</span>
                </div>';

        // ⭐ لو في ملاحظات
        if (!empty($reservation->reservation_note)) {
            $html .= '<div class="mt-2 p-2" style="background:#ffeeba;border:1px solid #f0ad4e;border-radius:5px;">
                        <strong><i class="mdi mdi-note-text-outline text-danger"></i> '
                        . trans('outpatient_trans.note') . ':</strong> ' . e($reservation->reservation_note) . '<br>
                        <small class="text-muted"><i class="mdi mdi-account"></i> '
                        . ($reservation->user ? $reservation->user->name : '-') . '</small>
                      </div>';
        }

        return $html;
    }

    private function getPatientNameHtml($reservation)
    {
        $name = $reservation->patient ? $reservation->patient->full_name : '-';

        // ⭐ تمييز اسم المريض بلون أحمر لو في ملاحظات
        if (!empty($reservation->reservation_note)) {
            return '<span style="color:#d9534f;font-weight:bold;">' . e($name) . '</span>';
        }

        return e($name);
    }

    private function getActionButtons($data)
    {
        $encryptedPatientId = Crypt::encryptString($data->patient->id ?? '');
        $encryptedId = Crypt::encryptString($data->id);

        $actions =
            '<div class="btn-group">
                  <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog-outline"></i> ' . trans('outpatient_trans.action') . '
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">';

        // زرار تأكيد الحجز
        if (is_null($data->confirmed_at)) {
            $actions .=
                '<button class="dropdown-item" onclick="confirmReservation(' . $data->id . ')">
                      <i class="mdi mdi-check-circle text-success"></i> ' . trans('outpatient_trans.confirm_reservation') . '
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
    }

    private function getPatientPhoneHtml($reservation)
    {
        $phone = $reservation->patient->phone_number ?? '';
        $phone2 = $reservation->patient->phone_number2 ?? '';
        $whatsapp = $reservation->patient->whatsapp ?? '';

        $phoneHtml = '<i class="bx bx-phone-outgoing text-primary"></i>' . $phone;

        if ($phone2) {
            $phoneHtml .= '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bx-phone-outgoing text-info" style="color: #140cfd;"></i>' . $phone2 . '
                      </span>';
        }

        if ($whatsapp) {
            $phoneHtml .= '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bxl-whatsapp text-success"></i>' . $whatsapp . '
                      </span>';
        }

        return $phoneHtml;
    }

    // 🔹 دالة لتأكيد الحجز
    public function confirmReservation(Request $request)
    {
        $reservation = reservations::findOrFail($request->id);
        $reservation->confirmed_at = Carbon::now();
        $reservation->status = 'confirmed_waiting_for_arrival';
        $reservation->save();

        return response()->json(['success' => true, 'message' => trans('outpatient_trans.reservation_confirmed')]);
    }
}